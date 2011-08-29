<?php
class UploadEdit extends AppModel {
	var $name = 'UploadEdit';
	var $belongsTo = array('Upload');
	var $actsAs = array('FileUpload.FileUpload' => array('maxFileSize' => '2097152'), 'Containable');

	function getEditUpload($uploadEditId) {
		//Grab out edit collectible
		$uploadEditVersion = $this -> find("first", array('conditions' => array('UploadEdit.id' => $uploadEditId)));
		//reformat it for us, unsetting some stuff we do not need
		$upload = array();
		$upload['Upload'] = $uploadEditVersion['UploadEdit'];
		unset($upload['Upload']['id']);
		unset($upload['Upload']['created']);
		unset($upload['Upload']['modified']);
		unset($upload['Upload']['collectible_id']);
		debug($upload);
		return $upload;

	}

}
?>    