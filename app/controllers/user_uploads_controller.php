<?php
App::import('Sanitize');
class UserUploadsController extends AppController {
	var $helpers = array('Html', 'Form', 'Ajax', 'FileUpload.FileUpload', 'Minify.Minify');
	var $components = array('RequestHandler', 'Image');

	/**
	 * This will display all uploads for the user logged in, since this is not a publically visible page
	 * to share photos, we are just going to use who is logged in at the time. Might want to change this later
	 * depending on how this page grows.
	 */
	public function upload() {
		$this -> checkLogIn();
		$uploads = $this -> UserUpload -> find('all', array('conditions' => array('UserUpload.user_id' => $this -> getUserId())));

		$this -> set(compact('uploads'));
		$this -> set('username', $this -> getUsername());

	}

	/**
	 * This is an Ajax enabled action to upload photos for the user currently logged in.  Just need to check
	 * if they are logged in here and nothing else since we are using the session data
	 */
	public function addUpload() {
		if ($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
		}
		$data = array();
		if ($this -> isLoggedIn()) {
			debug($this -> data);
			$this -> data['UserUpload']['user_id'] = $this -> getUserId();
			if ($this -> UserUpload -> isValidUpload($this -> data)) {
				if ($this -> UserUpload -> saveAll($this -> data['UserUpload'])) {
					//Grab the user id of the upload that was just added
					$id = $this -> UserUpload -> id;
					$userUpload = $this -> UserUpload -> findById($id);
					/*
					 * TODO: If the image is taller than it is wider, reize the height instead of the width then
					 */
					$img = $this -> Image -> image($userUpload['UserUpload']['name'], array('width' => 100, 'height'=> 200, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $this -> getUserId()));
					$data['success'] = array('isSuccess' => true);
					$data['isTimeOut'] = false;
					$data['data'] = array();
					/*
					 * At this point we are changing license data, so we need to reset the series. We will determine if there are
					 * any series for this license and return a flag so the UI knows it can add a series
					 */
					$data['data']['imageLocation'] = $img['path'];
					$data['data']['imageHeight'] = $img['height'];

				} else {
					$data['success'] = array('isSuccess' => false);
					$data['isTimeOut'] = false;
					$data['errors'] = array($this -> UserUpload -> validationErrors);
				}
			} else {
				$data['success'] = array('isSuccess' => false);
				$data['isTimeOut'] = false;
				$data['errors'] = array($this -> UserUpload -> validationErrors);
			}
		} else {
			//If they are not logged in and are trying to access this then just time them out
			$data['success'] = array('isSuccess' => false);
			$data['isTimeOut'] = true;
			$data['data'] = array();
		}

		$this -> set('aUpload', $data);

	}

}
?>
