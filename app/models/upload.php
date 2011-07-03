<?php
class Upload extends AppModel {
	var $name = 'Upload';
	var $actsAs = array('Revision', 'FileUpload.FileUpload'=> array(
			'maxFileSize' => '2097152'
		), 
		'Containable');
	var $hasMany = array('Collectible');
	
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
		if(count($uploadData['Upload']) == 1) {
			if($uploadData['Upload']['0']['file']['name'] != '' || $uploadData['Upload']['0']['url'] != '') {
				$validUpload = true;
			}
		}

		return $validUpload;
	}

}
?>           