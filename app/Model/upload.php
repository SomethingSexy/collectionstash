<?php
class Upload extends AppModel {
	var $name = 'Upload';
	var $actsAs = array('Revision', 'FileUpload.FileUpload' => array('maxFileSize' => '2097152'), 'Containable');
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
		if (isset($uploadData['Upload']) && !empty($uploadData['Upload']) && isset($uploadData['Upload']['0']) && !empty($uploadData['Upload']['0']) && isset($uploadData['Upload']['0']['file']) && !empty($uploadData['Upload']['0']['file']))
			if (count($uploadData['Upload']) == 1) {
				if ($uploadData['Upload']['0']['file']['name'] != '' || $uploadData['Upload']['0']['url'] != '') {
					$validUpload = true;
				}
			}

		return $validUpload;
	}

	function saveEdit($upload) {
		//Grab the collectible id for the edit we are saving
		$uploadId = $upload['Upload']['upload_id'];
		//now remove it cause it is not necessary
		unset($upload['Upload']['upload_id']);
		debug($uploadId);

		if ($upload['Upload']['action'] === 'A') {
			$this -> create();
			if ($this -> save($upload, array('validate' => false))) {
				return true;
			} else {
				return false;
			}
		} else if ($upload['Upload']['action'] === 'E') {
			//At this point should I delete the old image?
			//save this bad boy
			$this -> id = $uploadId;
			if ($this -> save($upload, array('validate' => false))) {
				return true;
			} else {
				return false;
			}
		} else {
			//not supported action yet
			return false;
		}

	}

}
?>           