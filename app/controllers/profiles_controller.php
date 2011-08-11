<?php
App::import('Sanitize');
//TODO this could get renamed account
class ProfilesController extends AppController {

	var $name = 'Profiles';
	var $helpers = array('Html', 'Ajax', 'FileUpload.FileUpload');

	public function index() {
		$this -> checkLogIn();
	
		$this -> set('allowInvites', Configure::read('Settings.Profile.allow-invites'));	
	}


}
?>