<?php
App::uses('Sanitize', 'Utility');
//TODO this could get renamed account
class ProfilesController extends AppController {

	public $helpers = array('Html', 'Js', 'FileUpload.FileUpload', 'Minify');

	public function index() {
		if ($this -> isUserAdmin()) {
			$this -> set('allowInvites', true);
		} else {
			$this -> set('allowInvites', Configure::read('Settings.Profile.allow-invites'));
		}

	}

}
?>