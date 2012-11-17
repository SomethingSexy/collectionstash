<?php

class UploadsController extends AppController {

	public $helpers = array('Html', 'Form', 'Js', 'FileUpload.FileUpload', 'Minify');

	public function upload() {
		debug($this -> request -> data);
		if ($this -> Upload -> saveAll($this -> request -> data['Upload'])) {

		}
	}

}
?>
