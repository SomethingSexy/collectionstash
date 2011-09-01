<?php
class UploadEdit extends AppModel {
	var $name = 'UploadEdit';
	var $belongsTo = array('Upload');
	var $actsAs = array('FileUpload.FileUpload' => array('maxFileSize' => '2097152'), 'Containable');

	function getAddUpload($uploadEditId) {
		//Grab out edit collectible
		$uploadEditVersion = $this -> find("first", array('conditions' => array('UploadEdit.id' => $uploadEditId)));
		//reformat it for us, unsetting some stuff we do not need
		$upload = array();
		$upload['Upload'] = $uploadEditVersion['UploadEdit'];
		unset($upload['Upload']['id']);
		unset($upload['Upload']['created']);
		unset($upload['Upload']['modified']);
		//unset($upload['Upload']['collectible_id']);
		debug($upload);
		return $upload;
	}
	
	function getUpdateFields($uploadEditId) {
		//Grab out edit collectible
		$uploadEditVersion = $this -> find("first", array('contain'=>false, 'conditions' => array('UploadEdit.id' => $uploadEditId)));
		//reformat it for us, unsetting some stuff we do not need
		$uploadFields = array();
		$uploadFields['Upload.name'] = '\''.$uploadEditVersion['UploadEdit']['name'].'\'';
		$uploadFields['Upload.edit_user_id'] = '\''.$uploadEditVersion['UploadEdit']['edit_user_id'].'\'';
		$uploadFields['Upload.type'] = '\''.$uploadEditVersion['UploadEdit']['type'].'\'';
		$uploadFields['Upload.size'] = '\''.$uploadEditVersion['UploadEdit']['size'].'\'';
		return $uploadFields;
	}
	

}
?>    



