<?php
App::uses('Sanitize', 'Utility');
//TODO this could get renamed account
class ProfilesController extends AppController {

	public $helpers = array('Html', 'Js', 'FileUpload.FileUpload', 'Minify');

	public function index() {
		$this -> checkLogIn();
		$this -> loadModel('Stash');

		$stashProfileSettings = $this -> Stash -> getProfileSettings($this -> getUser());

		$this -> set(compact('stashProfileSettings'));
	}

}
?>