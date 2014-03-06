<?php

/**
 * We need a before save setting, that checks to see if an upload exists for this
 * collectible already.  If it does not, it makes it the primary
 */
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesUpload extends AppModel {
	public $name = 'CollectiblesUpload';
	public $belongsTo = array('Upload', 'Collectible', 'Revision' => array('dependent' => true));
	public $actsAs = array('Revision', 'Containable', 'Editable' => array('modelAssociations' => array('belongsTo' => array('Upload', 'Action')), 'type' => 'collectiblesupload', 'model' => 'CollectiblesUploadEdit'));

	public $validate = array(
	//upload id field
	'upload_id' => array('rule' => array('validateUploadId'), 'required' => true, 'message' => 'Must be a valid image.'));

	private $collectibleCacheKey = 'upload_collectible_';

	public function beforeSave($options = array()) {
		// Before we save check to see if there is an existin image that is the primary
		// if not, set
		if (!isset($this -> data['CollectiblesUpload']['primary']) || !$this -> data['CollectiblesUpload']['primary']) {
			$primary = $this -> find('first', array('contain' => false, 'conditions' => array('CollectiblesUpload.collectible_id' => $this -> data['CollectiblesUpload']['collectible_id'], 'CollectiblesUpload.primary' => 1)));
			if (empty($primary)) {
				$this -> data['CollectiblesUpload']['primary'] = true;
			}
		}
		return true;
	}

	function afterSave($created, $options = array()) {
		// so far we only doing singles, I don't think we do multiple
		if (isset($this -> data['CollectiblesUpload']['collectible_id'])) {
			$this -> clearCache($this -> data['CollectiblesUpload']['collectible_id']);
		}
	}

	function validateUploadId($check) {
		if (!isset($check['upload_id']) || empty($check['upload_id'])) {
			return false;
		}
		$result = $this -> Upload -> find('count', array('id' => $check['upload_id']));

		return $result > 0;
	}

	public function findByCollectibleId($id) {
		$uploads = Cache::read($this -> collectibleCacheKey . $id, 'collectible');

		// if it isn't in the cache, add it to the cache
		if (!$uploads) {
			$uploads = $this -> find('all', array('conditions' => array('CollectiblesUpload.collectible_id' => $id), 'contain' => array('Upload')));
			Cache::write($this -> collectibleCacheKey . $id, $uploads, 'collectible');
		}

		return $uploads;
	}

	public function remove($upload, $user, $autoUpdate = false) {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		//Maybe this should be an error code
		$retVal['response']['errors'] = array();
		// There will be an ['Attribute']['reason'] - input field
		// if this attribute is tied to a collectible, are we replacing
		// with an existing attriute? Or removing completely, which will
		// remove all references
		$action = array();
		$action['Action']['action_type_id'] = 4;
		$action['Action']['reason'] = '';
		if (isset($upload['CollectiblesUpload']['reason'])) {
			$action['Action']['reason'] = $upload['CollectiblesUpload']['reason'];
		}

		unset($upload['CollectiblesUpload']['reason']);
		$currentVersion = $this -> findById($upload['CollectiblesUpload']['id']);

		// Now let's check to see if we need to update this based
		// on collectible status
		// If we are already auto updating, no need to check
		if ($autoUpdate === 'false' || $autoUpdate === false) {
			$autoUpdate = $this -> Collectible -> allowAutoUpdate($currentVersion['CollectiblesUpload']['collectible_id'], $user);
		}

		if ($autoUpdate === true || $autoUpdate === 'true') {
			if ($this -> delete($currentVersion['CollectiblesUpload']['id'])) {
				// After we delete the collectible, we need to check and see if
				// we are deleting a primary, if so and there are other
				// uploads, set the first one as the primary

				if ($currentVersion['CollectiblesUpload']['primary']) {
					$firstExisting = $this -> find('first', array('contain' => false, 'conditions' => array('CollectiblesUpload.collectible_id' => $currentVersion['CollectiblesUpload']['collectible_id'])));
					// if we do have one
					if (!empty($firstExisting)) {
						$this -> id = $firstExisting['CollectiblesUpload']['id'];
						$this -> saveField('primary', true, false);
					}
				}

				$this -> clearCache($currentVersion['CollectiblesUpload']['collectible_id']);
			}
		} else {
			// Doing this so that we have a record of the current version

			if ($this -> saveEdit($currentVersion, $upload['CollectiblesUpload']['id'], $user['User']['id'], $action)) {
				$retVal['response']['isSuccess'] = true;
			} else {
				$retVal['response']['isSuccess'] = false;
			}
		}
		return $retVal;
	}

	/**
	 * Right now we are only support adding new uploads to collectibles
	 *
	 * We are not linking uploads yet
	 *
	 * TODO: This needs to be update to check the status of the collectible we are adding this too
	 *       if it is anything other than active, it will automatically add
	 */
	public function add($data, $user, $autoUpdate = false) {
		// Check to see if there is an upload id, if so then we are adding
		// from a previously selected collectible.  We can sumbit an edit for
		// this with the type of add

		// otherwise we are adding a brand new one
		// we need to submit an add for the upload

		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		//Maybe this should be an error code
		$retVal['response']['errors'] = array();
		$retVal['response']['data'] = array();
		$this -> set($data);
		$validCollectible = true;

		// If we have an upload, that means
		// we are saving a collectible upload and creating a new
		// upload at the same time, we don't have to validate
		// the upload id because it does not exist yet
		if (isset($data['Upload'])) {
			unset($this -> validate['upload_id']);
		}

		if ($this -> validates()) {

			$dataSource = $this -> getDataSource();
			$dataSource -> begin();
			// If we hve an upload we need to make sure
			// that that validates as well.
			if (isset($data['Upload'])) {
				$this -> Upload -> set($data);

				// If it doesn't validate return failz
				if (!$this -> Upload -> isValidUpload($data)) {
					// Just in case
					$dataSource -> rollback();
					$retVal['response']['isSuccess'] = false;
					$errors = $this -> convertErrorsJSON($this -> Upload -> validationErrors, 'Upload');
					$retVal['response']['errors'] = $errors;
					return $retVal;
				}
				$upload = array();
				$upload['Upload'] = $data['Upload'];

				// Now we need to kick off a save of the upload
				$uploadAddResponse = $this -> Upload -> add($upload, $user['User']['id']);
				if ($uploadAddResponse && $uploadAddResponse['response']['isSuccess']) {
					$retVal['response']['data'] = $uploadAddResponse['response']['data'];
					$uploadId = $uploadAddResponse['response']['data']['Upload']['id'];
					$data['CollectiblesUpload']['upload_id'] = $uploadId;
				} else {
					$dataSource -> rollback();
					// return that response, should be universal
					return $uploadAddResponse;
				}
			}

			// Now let's check to see if we need to update this based
			// on collectible status
			// If we are already auto updating, no need to check
			if ($autoUpdate === 'false' || $autoUpdate === false) {
				$autoUpdate = $this -> Collectible -> allowAutoUpdate($data['CollectiblesUpload']['collectible_id'], $user);
			}

			if ($autoUpdate === true || $autoUpdate === 'true') {
				unset($data['Upload']);
				$revision = $this -> Revision -> buildRevision($user['User']['id'], $this -> Revision -> ADD, null);
				$data = array_merge($data, $revision);
				if ($this -> saveAll($data, array('validate' => false))) {
					$dataSource -> commit();
					$collectiblesUpload = $this -> find('first', array('conditions' => array('CollectiblesUpload.id' => $this -> id)));
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['data'] = $collectiblesUpload;
					$retVal['response']['data']['isEdit'] = false;

					// However, we only want to trigger this activity on collectibles that have been APPROVED already
					if ($this -> Collectible -> triggerActivity($data['CollectiblesUpload']['collectible_id'], $user)) {
						$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$USER_ADD_NEW, 'user' => $user, 'object' => $collectiblesUpload, 'type' => 'CollectiblesUpload')));
					}

				} else {
					$dataSource -> rollback();
				}
			} else {
				$action = array();
				$action['Action']['action_type_id'] = 1;
				$savedEdit = $this -> saveEdit($data, null, $user['User']['id'], $action);
				if ($savedEdit) {
					// Only commit when the save edit is successful
					$dataSource -> commit();
					$retVal['response']['isSuccess'] = true;
					// Returning the new edit version on this array, kind of lame but need a place to put it
					$retVal['response']['data'] = $savedEdit;
					$retVal['response']['data']['isEdit'] = true;
				} else {
					$dataSource -> rollback();
				}
			}

		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'CollectiblesUpload');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	public function publishEdit($editId, $approvalUserId) {
		$retVal = false;
		// Grab the fields that will need to updated
		$collectiblesUploadEditVersion = $this -> findEdit($editId);
		if ($collectiblesUploadEditVersion['Action']['action_type_id'] === '1') {// Add

			$approval = array();
			$approval['Approval'] = array();
			$approval['Approval']['approve'] = 'true';
			$approval['Approval']['notes'] = '';
			$response = $this -> Upload -> approve($collectiblesUploadEditVersion['CollectiblesUploadEdit']['upload_id'], $approval, $approvalUserId);
			// if this is false, then return false so that we won't be committing shit
			// otherwise carry on
			if (!$response['response']['isSuccess']) {
				return false;
			}

			$collectiblesUpload = array();
			$collectiblesUpload['CollectiblesUpload']['upload_id'] = $collectiblesUploadEditVersion['CollectiblesUploadEdit']['upload_id'];
			$collectiblesUpload['CollectiblesUpload']['collectible_id'] = $collectiblesUploadEditVersion['CollectiblesUploadEdit']['collectible_id'];
			$collectiblesUpload['CollectiblesUpload']['primary'] = $collectiblesUploadEditVersion['CollectiblesUploadEdit']['primary'];
			// Setting this as an add because it was added to the new table..not sure this is right
			$revision = $this -> Revision -> buildRevision($collectiblesUploadEditVersion['CollectiblesUploadEdit']['edit_user_id'], $this -> Revision -> APPROVED, null);
			$collectiblesUpload = array_merge($collectiblesUpload, $revision);
			if ($this -> saveAll($collectiblesUpload, array('validate' => false))) {
				$retVal = true;
			}
		} else if ($collectiblesUploadEditVersion['Action']['action_type_id'] === '4') {// Delete
			// At this point, this collectible upload has to be approved because I cannot
			// delete a pending removal.
			if ($this -> delete($collectiblesUploadEditVersion['CollectiblesUploadEdit']['base_id'])) {
				if ($this -> Upload -> delete($collectiblesUploadEditVersion['Upload']['id'])) {

					// After we delete the collectible, we need to check and see if
					// we are deleting a primary, if so and there are other
					// uploads, set the first one as the primary
					if ($collectiblesUploadEditVersion['CollectiblesUploadEdit']['primary']) {
						$firstExisting = $this -> find('first', array('contain' => false, 'conditions' => array('CollectiblesUpload.collectible_id' => $collectiblesUploadEditVersion['CollectiblesUploadEdit']['collectible_id'])));
						// if we do have one
						if (!empty($firstExisting)) {
							$this -> id = $firstExisting['CollectiblesUpload']['id'];
							$this -> saveField('primary', true, false);
						}
					}

					$this -> clearCache($collectiblesUploadEditVersion['CollectiblesUploadEdit']['collectible_id']);

					$retVal = true;
				}
			}

		} else if ($collectiblesUploadEditVersion['Action']['action_type_id'] === '2') {// Edit
			// Can't edit right now
		}

		if ($retVal) {
			$collectible = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $collectiblesUploadEditVersion['CollectiblesUploadEdit']['collectible_id'])));
			$message = 'We have approved the following collectible upload you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectiblesUploadEditVersion['CollectiblesUploadEdit']['collectible_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$subject = __('Your edit has been approved.');
			$this -> notifyUser($collectiblesUploadEditVersion['CollectiblesUploadEdit']['edit_user_id'], $message, $subject, 'edit_approval');
		}

		return $retVal;
	}

	/**
	 * This method will deny the edit, in which case we will be deleting it
	 */
	public function denyEdit($editId, $email = true) {
		$retVal = false;
		// Grab the fields that will need to updated
		$collectiblesUploadEdit = $this -> findEdit($editId);
		// Right now we can really only add or edit
		if ($collectiblesUploadEdit['Action']['action_type_id'] === '1') {//Add
			// If we were adding an image, then we need to delete the upload and then delete
			// this reference.  Since we cannot link photos right now, if we delete
			// we auto delete the upload right, don't need to check if this is linked
			if ($this -> deleteEdit($collectiblesUploadEdit)) {
				if ($this -> Upload -> delete($collectiblesUploadEdit['Upload']['id'])) {
					$retVal = true;
				}
			}
		} else if ($collectiblesUploadEdit['Action']['action_type_id'] === '4') {// Delete
			// If we are deny a delete, then we are keeping it out there
			// so just delete the edit
			if ($this -> deleteEdit($collectiblesUploadEdit)) {
				$retVal = true;
			}
		}

		if ($retVal && $email) {
			$collectible = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $collectiblesUploadEdit['CollectiblesUploadEdit']['collectible_id'])));
			$message = 'We have denied the following collectible upload you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectiblesUploadEdit['CollectiblesUploadEdit']['collectible_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$subject = __('Your edit has been denied.');
			$this -> notifyUser($collectiblesUploadEdit['CollectiblesUploadEdit']['edit_user_id'], $message, $subject, 'edit_deny');
		}

		return $retVal;
	}

	/**
	 *$id = collectible_id
	 */
	public function clearCache($id) {
		Cache::delete($this -> collectibleCacheKey . $id, 'collectible');
	}

}
?>
