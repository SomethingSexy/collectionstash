<?php
class UploadEdit extends AppModel {
	var $name = 'UploadEdit';
	var $belongsTo = array('Upload');
	var $actsAs = array('FileUpload.FileUpload' => array('maxFileSize' => '2097152'), 'Containable');

	function getAddUpload($uploadEditId, $notes = null) {
		//Grab out edit collectible
		$uploadEditVersion = $this -> find("first", array('conditions' => array('UploadEdit.id' => $uploadEditId)));
		//reformat it for us, unsetting some stuff we do not need
		$upload = array();
		$upload['Upload'] = $uploadEditVersion['UploadEdit'];
		unset($upload['Upload']['id']);
		unset($upload['Upload']['created']);
		unset($upload['Upload']['modified']);
		if (!is_null($notes)) {
			$upload['Upload']['notes'] = $notes;
		}
		debug($upload);
		return $upload;
	}

	function getUpdateFields($uploadEditId, $notes = null) {
		//Grab out edit collectible
		$uploadEditVersion = $this -> find("first", array('contain' => false, 'conditions' => array('UploadEdit.id' => $uploadEditId)));
		//reformat it for us, unsetting some stuff we do not need
		$uploadFields = array();
		$uploadFields['Upload.name'] = '\'' . $uploadEditVersion['UploadEdit']['name'] . '\'';
		$uploadFields['Upload.edit_user_id'] = '\'' . $uploadEditVersion['UploadEdit']['edit_user_id'] . '\'';
		$uploadFields['Upload.type'] = '\'' . $uploadEditVersion['UploadEdit']['type'] . '\'';
		$uploadFields['Upload.size'] = '\'' . $uploadEditVersion['UploadEdit']['size'] . '\'';
		//We do not support deleting of images right now, so this is always going to be an E when calling this method
		$updateFields['Upload.action'] = '\'E\'';
		if (!is_null($notes)) {
			$upload['Upload.notes'] = '\'' . $notes .'\'';
		}
		return $uploadFields;
	}

}
?>

