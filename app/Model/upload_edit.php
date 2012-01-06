<?php
class UploadEdit extends AppModel {
	var $name = 'UploadEdit';
	var $belongsTo = array('Upload');
	var $actsAs = array('FileUpload.FileUpload' => array('maxFileSize' => '2097152'), 'Containable');

	function getUpdateFields($uploadEditId, $notes = null) {
		//Grab out edit collectible
		$uploadEditVersion = $this -> find("first", array('contain' => false, 'conditions' => array('UploadEdit.id' => $uploadEditId)));
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
			$uploadFields['Upload']['id'] = $uploadEditVersion['UploadEdit']['upload_id'];
			$uploadFields['Revision']['action'] = 'E';
		}

		if (!is_null($notes)) {
			$uploadFields['Revision']['notes'] = $notes;
		}
		//Make sure I grab the user id that did this edit
		$uploadFields['Revision']['user_id'] = $uploadEditVersion['UploadEdit']['edit_user_id'];

		return $uploadFields;
	}

}
?>

