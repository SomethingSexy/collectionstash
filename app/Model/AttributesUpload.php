<?php

/**
 * We need a before save setting, that checks to see if an upload exists for this
 * collectible already.  If it does not, it makes it the primary
 */
class AttributesUpload extends AppModel {
	public $name = 'AttributesUpload';
	public $belongsTo = array('Upload', 'Attribute' => array('dependent' => true), 'Revision' => array('dependent' => true));
	public $actsAs = array('Revision', 'Containable', 'Editable' => array('modelAssociations' => array('belongsTo' => array('Upload', 'Action')), 'type' => 'attributesupload', 'model' => 'AttributesUploadEdit'));

	public $validate = array(
	//upload id field
	'upload_id' => array('rule' => array('validateUploadId'), 'required' => true, 'message' => 'Must be a valid image.'));

	public function beforeSave($options = array()) {
		// Before we save check to see if there is an existin image that is the primary
		// if not, set
		debug($this -> data['AttributesUpload']);
		if (!isset($this -> data['AttributesUpload']['primary']) || !$this -> data['AttributesUpload']['primary']) {
			$primary = $this -> find('first', array('contain' => false, 'conditions' => array('AttributesUpload.attribute_id' => $this -> data['AttributesUpload']['attribute_id'], 'AttributesUpload.primary' => 1)));
			debug($primary);
			if (empty($primary)) {
				$this -> data['AttributesUpload']['primary'] = true;
			}
		}
		return true;
	}

	function validateUploadId($check) {
		if (!isset($check['upload_id']) || empty($check['upload_id'])) {
			return false;
		}
		$result = $this -> Upload -> find('count', array('id' => $check['upload_id']));

		return $result > 0;
	}

	public function remove($upload, $userId, $autoUpdate = false) {
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
		if (isset($upload['AttributesUpload']['reason'])) {
			$action['Action']['reason'] = $upload['AttributesUpload']['reason'];
		}

		unset($upload['AttributesUpload']['reason']);
		$currentVersion = $this -> findById($upload['AttributesUpload']['id']);

		// Now let's check to see if we need to update this based
		// on collectible status
		// If we are already auto updating, no need to check
		if ($autoUpdate === 'false' || $autoUpdate === false) {
			if ($this -> Attribute -> isStatusDraft($currentVersion['AttributesUpload']['attribute_id']) || $this -> Attribute -> isStatusSubmitted($currentVersion['AttributesUpload']['attribute_id'])) {
				$autoUpdate = true;
			}

		}

		if ($autoUpdate === true || $autoUpdate === 'true') {
			if ($this -> delete($currentVersion['AttributesUpload']['id'])) {
				// After we delete the collectible, we need to check and see if
				// we are deleting a primary, if so and there are other
				// uploads, set the first one as the primary

				if ($currentVersion['AttributesUpload']['primary']) {
					$firstExisting = $this -> find('first', array('contain' => false, 'conditions' => array('AttributesUpload.attribute_id' => $currentVersion['AttributesUpload']['attribute_id'])));
					// if we do have one
					if (!empty($firstExisting)) {
						$this -> id = $firstExisting['AttributesUpload']['id'];
						$this -> saveField('primary', true, false);
					}
				}
			}
		} else {
			// Doing this so that we have a record of the current version

			if ($this -> saveEdit($currentVersion, $upload['AttributesUpload']['id'], $userId, $action)) {
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
	public function add($data, $userId, $autoUpdate = false) {
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
				$uploadAddResponse = $this -> Upload -> add($upload, $userId);
				debug($uploadAddResponse);
				if ($uploadAddResponse && $uploadAddResponse['response']['isSuccess']) {
					debug($uploadAddResponse);
					$retVal['response']['data'] = $uploadAddResponse['response']['data'];
					$uploadId = $uploadAddResponse['response']['data']['Upload']['id'];
					$data['AttributesUpload']['upload_id'] = $uploadId;
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
				if ($this -> Attribute -> isStatusDraft($data['AttributesUpload']['attribute_id']) || $this -> Attribute -> isStatusSubmitted($data['AttributesUpload']['attribute_id'])) {
					$autoUpdate = true;
				}
			}

			if ($autoUpdate === true || $autoUpdate === 'true') {
				unset($data['Upload']);
				$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> ADD, null);
				$data = array_merge($data, $revision);
				if ($this -> saveAll($data, array('validate' => false))) {
					$dataSource -> commit();
					$AttributesUpload = $this -> find('first', array('conditions' => array('AttributesUpload.id' => $this -> id)));
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['data'] = $AttributesUpload;
					$retVal['response']['data']['isEdit'] = false;
				} else {
					$dataSource -> rollback();
				}
			} else {
				$action = array();
				$action['Action']['action_type_id'] = 1;
				$savedEdit = $this -> saveEdit($data, null, $userId, $action);
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
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'AttributesUpload');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	public function publishEdit($editId, $approvalUserId) {
		$retVal = false;
		// Grab the fields that will need to updated
		$collectiblesUploadEditVersion = $this -> findEdit($editId);
		debug($collectiblesUploadEditVersion);

		if ($collectiblesUploadEditVersion['Action']['action_type_id'] === '1') {// Add

			$approval = array();
			$approval['Approval'] = array();
			$approval['Approval']['approve'] = 'true';
			$approval['Approval']['notes'] = '';
			$response = $this -> Upload -> approve($collectiblesUploadEditVersion['AttributesUploadEdit']['upload_id'], $approval, $approvalUserId);
			// if this is false, then return false so that we won't be committing shit
			// otherwise carry on
			if (!$response['response']['isSuccess']) {
				return false;
			}

			$AttributesUpload = array();
			$AttributesUpload['AttributesUpload']['upload_id'] = $collectiblesUploadEditVersion['AttributesUploadEdit']['upload_id'];
			$AttributesUpload['AttributesUpload']['attribute_id'] = $collectiblesUploadEditVersion['AttributesUploadEdit']['attribute_id'];
			$AttributesUpload['AttributesUpload']['primary'] = $collectiblesUploadEditVersion['AttributesUploadEdit']['primary'];
			// Setting this as an add because it was added to the new table..not sure this is right
			$revision = $this -> Revision -> buildRevision($collectiblesUploadEditVersion['AttributesUploadEdit']['edit_user_id'], $this -> Revision -> APPROVED, null);
			$AttributesUpload = array_merge($AttributesUpload, $revision);
			if ($this -> saveAll($AttributesUpload, array('validate' => false))) {
				$retVal = true;
			}
		} else if ($collectiblesUploadEditVersion['Action']['action_type_id'] === '4') {// Delete
			// At this point, this collectible upload has to be approved because I cannot
			// delete a pending removal.
			if ($this -> delete($collectiblesUploadEditVersion['AttributesUploadEdit']['base_id'])) {
				if ($this -> Upload -> delete($collectiblesUploadEditVersion['Upload']['id'])) {

					// After we delete the collectible, we need to check and see if
					// we are deleting a primary, if so and there are other
					// uploads, set the first one as the primary
					if ($collectiblesUploadEditVersion['AttributesUploadEdit']['primary']) {
						$firstExisting = $this -> find('first', array('contain' => false, 'conditions' => array('AttributesUpload.attribute_id' => $collectiblesUploadEditVersion['AttributesUploadEdit']['attribute_id'])));
						// if we do have one
						if (!empty($firstExisting)) {
							$this -> id = $firstExisting['AttributesUpload']['id'];
							$this -> saveField('primary', true, false);
						}
					}

					$retVal = true;
				}
			}

		} else if ($collectiblesUploadEditVersion['Action']['action_type_id'] === '2') {// Edit
			// Can't edit right now
		}

		if ($retVal) {
			$collectible = $this -> Attribute -> find('first', array('contain' => false, 'conditions' => array('Attribute.id' => $collectiblesUploadEditVersion['AttributesUploadEdit']['attribute_id'])));
			$message = 'We have approved the following part upload you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectiblesUploadEditVersion['AttributesUploadEdit']['attribute_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$this -> notifyUser($collectiblesUploadEditVersion['AttributesUploadEdit']['edit_user_id'], $message);
		}

		return $retVal;
	}

	/**
	 * This method will deny the edit, in which case we will be deleting it
	 */
	public function denyEdit($editId, $email = true) {
		$retVal = false;
		debug($editId);
		// Grab the fields that will need to updated
		$AttributesUploadEdit = $this -> findEdit($editId);
		debug($AttributesUploadEdit);
		// Right now we can really only add or edit
		if ($AttributesUploadEdit['Action']['action_type_id'] === '1') {//Add
			// If we were adding an image, then we need to delete the upload and then delete
			// this reference.  Since we cannot link photos right now, if we delete
			// we auto delete the upload right, don't need to check if this is linked
			if ($this -> deleteEdit($AttributesUploadEdit)) {
				if ($this -> Upload -> delete($AttributesUploadEdit['Upload']['id'])) {
					$retVal = true;
				}
			}
		} else if ($AttributesUploadEdit['Action']['action_type_id'] === '4') {// Delete
			// If we are deny a delete, then we are keeping it out there
			// so just delete the edit
			if ($this -> deleteEdit($AttributesUploadEdit)) {
				$retVal = true;
			}

		}

		if ($retVal && $email) {
			$collectible = $this -> Attribute -> find('first', array('contain' => false, 'conditions' => array('Attribute.id' => $AttributesUploadEdit['AttributesUploadEdit']['attribute_id'])));
			$message = 'We have denied the following part upload you submitted a change to <a href="http://' . env('SERVER_NAME') . '/attributes/view/' . $AttributesUploadEdit['AttributesUploadEdit']['attribute_id'] . '">' . $collectible['Attribute']['name'] . '</a>';
			$this -> notifyUser($AttributesUploadEdit['AttributesUploadEdit']['edit_user_id'], $message);
		}

		return $retVal;
	}

}
?>