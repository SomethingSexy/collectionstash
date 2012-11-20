<?php

/**
 * We need a before save setting, that checks to see if an upload exists for this
 * collectible already.  If it does not, it makes it the primary
 */
class CollectiblesUpload extends AppModel {
	public $name = 'CollectiblesUpload';
	public $belongsTo = array('Upload', 'Collectible', 'Revision' => array('dependent' => true));
	public $actsAs = array('Revision', 'Containable', 'Editable' => array('modelAssociations' => array('belongsTo' => array('Upload', 'Action')), 'type' => 'collectiblesupload', 'model' => 'CollectiblesUploadEdit'));

	public $validate = array(
	//upload id field
	'upload_id' => array('rule' => array('validateUploadId'), 'required' => true, 'message' => 'Must be a valid image.'));

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
		if (isset($upload['CollectiblesUpload']['reason'])) {
			$action['Action']['reason'] = $upload['CollectiblesUpload']['reason'];
		}

		unset($upload['CollectiblesUpload']['reason']);
		$currentVersion = $this -> findById($upload['CollectiblesUpload']['id']);
		if ($autoUpdate) {

		} else {
			// Doing this so that we have a record of the current version

			if ($this -> saveEdit($currentVersion, $upload['CollectiblesUpload']['id'], $userId, $action)) {
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
		debug($data);
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
					$errors = $this -> convertErrorsJSON($this -> Attribute -> validationErrors, 'Attribute');
					$retVal['response']['errors'] = $errors;
					return $retVal;
				}
				$upload = array();
				$upload['Upload'] = $data['Upload'];

				// Now we need to kick off a save of the attribute
				$uploadAddResponse = $this -> Upload -> add($upload, $userId);

				if ($uploadAddResponse && $uploadAddResponse['response']['isSuccess']) {
					debug($uploadAddResponse);
					$retVal['response']['data'] = $uploadAddResponse['response']['data'];
					$uploadId = $uploadAddResponse['response']['data']['Upload']['id'];
					$data['CollectiblesUpload']['upload_id'] = $uploadId;
				} else {
					$dataSource -> rollback();
					// return that response, should be universal
					return $uploadAddResponse;
				}
			}

			if ($autoUpdate) {
				unset($data['Upload']);
				$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> ADD, null);
				$data = array_merge($data, $revision);
				if ($this -> saveAll($data, array('validate' => false))) {
					$dataSource -> commit();
					$collectiblesUpload = $this -> find('first', array('conditions' => array('CollectiblesUpload.id' => $this -> id)));
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['data'] = $collectiblesUpload;
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
					$retVal['response']['data']['Edit'] = $savedEdit;
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
		debug($collectiblesUploadEditVersion);

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
					$retVal = true;
				}
			}

		} else if ($collectiblesUploadEditVersion['Action']['action_type_id'] === '2') {// Edit
			// Can't edit right now
		}

		if ($retVal) {
			$collectible = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $collectiblesUploadEditVersion['CollectiblesUploadEdit']['collectible_id'])));
			$message = 'We have approved the following collectible upload you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectiblesUploadEditVersion['CollectiblesUploadEdit']['collectible_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$this -> notifyUser($collectiblesUploadEditVersion['CollectiblesUploadEdit']['edit_user_id'], $message);
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
		$collectiblesUploadEdit = $this -> findEdit($editId);
		debug($collectiblesUploadEdit);
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
			$this -> notifyUser($collectiblesUploadEdit['CollectiblesUploadEdit']['edit_user_id'], $message);
		}

		return $retVal;
	}

}
?>
