<?php
class UploadEdit extends AppModel {
	var $name = 'UploadEdit';
	var $useTable = 'uploads_edit';
	var $actsAs = array('FileUpload.FileUpload'=> array(
			'maxFileSize' => '2097152'
		), 
		'Containable');
}
?>    