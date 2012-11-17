<?php
/**
 * I think this should be indepdent of collectible and attribute and use join tables to link up
 *
 * This way an upload will have its own edit code.  Collectible and Attribute would really only add or remove.
 *
 * A user would submit a new upload and link it to a collectible
 *
 * Or they could edit a upload independently if they wanted to add a description or name Or in the future an owner if the image
 *
 * Although then we would need a Collectibles_upload_edit table and a attribute_upload_edit table
 * 		- actions would consist of Add and Remove
 * 		- If it is add we would add the appropriate one in upload and link it up
 * 		- then when it is approved at the collectible level it would be approved at the upload level
 * 		- If the link is removed and it is not linked to anything else then it would automatically be deleted, otherwise the link would be removed
 * 		- Same if the approval is denied, it would be automatically deleted
 * 		- This would eventually allow us to link the same image to multiple collectibles....think when we allow more than one image to a collectible...variants can share the same core images
 *
 *
 * We would just need to code up a job to convert the old table to thew new table...history would be the same, at the upload level...but how would it work at the collectible level? Storing
 * history for when stuff was added or removed....hmmm
 *
 */
class Upload extends AppModel {
	var $name = 'Upload';
	var $actsAs = array('Editable' => array('type' => 'upload', 'model' => 'UploadEdit', 'behaviors' => array('FileUpload.FileUpload' => array('fileModel' => 'UploadEdit'))), 'Revision' => array('dependent' => true), 'FileUpload.FileUpload' => array('maxFileSize' => '2097152'), 'Containable');
	var $belongsTo = array('Revision' => array('dependent' => true), 'User');
	var $hasMany = array('CollectiblesUpload');

	// [Upload] => Array
	//       (
	//           [0] => Array
	//               (
	//                   [id] => 69
	//                   [name] => bluecity_20.gif
	//                   [type] => image/gif
	//                   [size] => 2611
	//                   [created] => 2010-12-31 20:49:29
	//                   [modified] => 2010-12-31 20:49:29
	//                   [collectible_id] => 213
	//               )
	//
	//       )

	public function isValidUpload($uploadData) {
		$validUpload = false;
		debug($uploadData);
		if (isset($uploadData['Upload']) && !empty($uploadData['Upload']) && isset($uploadData['Upload']['file']) && !empty($uploadData['Upload']['file'])) {
			if ($uploadData['Upload']['file']['name'] != '' || $uploadData['Upload']['url'] != '') {
				$validUpload = true;
			}
		}

		return $validUpload;
	}

	public function add($upload, $userId) {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		//Maybe this should be an error code
		$retVal['response']['errors'] = array();

		// An id of 2 means it is submitted
		$upload['Upload']['status_id'] = 2;
		$upload['Upload']['user_id'] = $userId;
		$upload['EntityType']['type'] = 'upload';
		$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> ADD, null);
		$upload = array_merge($upload, $revision);
		if ($this -> saveAssociated($upload)) {
			$uploadId = $this -> id;
			$savedUpload = $this -> find("first", array('conditions' => array('Upload.id' => $uploadId), 'contain' => false));
			// As of now, we just need to the id but we
			// can expand this later to return more if necessary
			$retVal['response']['data'] = $savedUpload;
			$retVal['response']['isSuccess'] = true;
		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Upload');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	function getUpdateFields($uploadEditId, $includeChanges = false, $notes = null) {
		//Grab out edit collectible
		$uploadEditVersion = $this -> findEdit($uploadEditId);
		//reformat it for us, unsetting some stuff we do not need
		$uploadFields = array();

		if ($uploadEditVersion['UploadEdit']['action'] === 'A') {
			$uploadFields['Upload'] = $uploadEditVersion['UploadEdit'];
			unset($uploadFields['Upload']['id']);
			unset($uploadFields['Upload']['created']);
			unset($uploadFields['Upload']['modified']);
			$uploadFields['Revision']['action'] = 'A';
		} else {
			// $uploadFields['Upload.name'] = '\'' . $uploadEditVersion['UploadEdit']['name'] . '\'';
			// $uploadFields['Upload.edit_user_id'] = '\'' . $uploadEditVersion['UploadEdit']['edit_user_id'] . '\'';
			// $uploadFields['Upload.type'] = '\'' . $uploadEditVersion['UploadEdit']['type'] . '\'';
			// $uploadFields['Upload.size'] = '\'' . $uploadEditVersion['UploadEdit']['size'] . '\'';

			$uploadFields['Upload']['name'] = $uploadEditVersion['UploadEdit']['name'];
			// $uploadFields['Upload']['edit_user_id'] = '\'' . $uploadEditVersion['UploadEdit']['edit_user_id'] . '\'';
			$uploadFields['Upload']['type'] = $uploadEditVersion['UploadEdit']['type'];
			$uploadFields['Upload']['size'] = $uploadEditVersion['UploadEdit']['size'];
			$uploadFields['Upload']['id'] = $uploadEditVersion['UploadEdit']['base_id'];
			$uploadFields['Revision']['action'] = 'E';
		}

		if (!is_null($notes)) {
			$uploadFields['Revision']['notes'] = $notes;
		}
		//Make sure I grab the user id that did this edit
		$uploadFields['Revision']['user_id'] = $uploadEditVersion['UploadEdit']['edit_user_id'];

		return $uploadFields;
	}
	
	/**
	 * More future use but used when approving collectible uploads
	 */
	public function approve($id, $approval, $userId) {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;

		$upload = $this -> find('first', array('conditions' => array('Upload.id' => $id), 'contain' => false));

		if ($approval['Approval']['approve'] === 'true') {
			// 2 is the approvel status now
			if (!empty($upload) && $upload['Upload']['status_id'] === '2') {
				$data = array();
				$data['Upload'] = array();
				$data['Upload']['id'] = $upload['Upload']['id'];
				$data['Upload']['status_id'] = 3;

				$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> APPROVED, $approval['Approval']['notes']);
				$data = array_merge($data, $revision);

				if ($this -> saveAll($data, array('validate' => false))) {
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['code'] = 1;
				} else {
					$retVal['response']['code'] = 4;
				}
			} else {
				$retVal['response']['code'] = 5;
			}
		} else {
			//fuck it, I am deleting it
			if ($this -> delete($upload['Upload']['id'], true)) {
				$retVal['response']['code'] = 2;
			} else {
				$retVal['response']['code'] = 4;
			}

		}

		return $retVal;
	}

}
?>           