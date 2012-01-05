<?php
App::uses('Sanitize', 'Utility');
class UserUploadsController extends AppController {

	public $helpers = array('Html', 'Form', 'Js', 'FileUpload.FileUpload', 'Minify');
	
	public $components = array('Image');
	/**
	 * This will display all uploads for the user logged in, since this is not a publically visible page
	 * to share photos, we are just going to use who is logged in at the time. Might want to change this later
	 * depending on how this page grows.
	 */
	public function uploads() {
		$this -> checkLogIn();
		if (Configure::read('Settings.User.uploads.allowed')) {
			$uploads = $this -> UserUpload -> find('all', array('conditions' => array('UserUpload.user_id' => $this -> getUserId())));
			//This is fucking bullshit but counter cache is not working right now
			$uploadCount = $this -> UserUpload -> find('count', array('conditions' => array('UserUpload.user_id' => $this -> getUserId())));
			$this -> set(compact('uploads'));
			$this -> set(compact('uploadCount'));
			$this -> set('username', $this -> getUsername());
		} else {
			$this -> viewPath = 'errors';
			$this -> render('invalid_request');
		}

	}

	public function upload($name = null) {
		$this -> checkLogIn();
		if (!is_null($name)) {
			$userUpload = $this -> UserUpload -> findByName($name);
			debug($userUpload);
			if (!empty($userUpload)) {
				$this -> set(compact('userUpload'));
			} else {
				$this -> render('upload_not_found');
			}
		} else {
			$this -> viewPath = 'errors';
			$this -> render('invalid_request');
		}
	}

	public function update() {
		$data = array();
		$this -> checkLogIn();
		$this -> request -> data = Sanitize::clean($this -> request -> data);
		debug($this -> request -> data);
		//Grab the user name from the request
		$uploadName = $this -> request -> data['UserUpload']['name'];
		//TODO: update the error, so it doesn't log the user out,
		if (!empty($uploadName)) {
			$userUpload = $this -> UserUpload -> findByName($uploadName);
			debug($userUpload);
			if (!empty($userUpload)) {
				if ($userUpload['UserUpload']['user_id'] === $this -> getUserId()) {
					$this -> UserUpload -> id = $userUpload['UserUpload']['id'];

					if (isset($this -> request -> data['UserUpload']['type']) && ($this -> request -> data['UserUpload']['type'] === 'title' || $this -> request -> data['UserUpload']['type'] === 'description')) {
						if ($this -> UserUpload -> saveField($this -> request -> data['UserUpload']['type'], $this -> request -> data['UserUpload']['data'], true)) {
							$data['success'] = array('isSuccess' => true);
							$data['isTimeOut'] = false;
							$data['data'] = array();
						} else {
							$data['success'] = array('isSuccess' => false);
							$data['isTimeOut'] = false;
							$data['errors'] = array($this -> UserUpload -> validationErrors);
						}
					} else {
						//bad input, not user fault, log out
						$data['success'] = array('isSuccess' => false);
						$data['isTimeOut'] = true;
						$data['data'] = array();
					}

				} else {
					//return success false, trying to update upload user doesn't have access too
					$data['success'] = array('isSuccess' => false);
					$data['isTimeOut'] = true;
					$data['data'] = array();
				}
			} else {
				//return success false, invalid request
				$data['success'] = array('isSuccess' => false);
				$data['isTimeOut'] = true;
				$data['data'] = array();
			}
		} else {
			//return success false, invalid request
			$data['success'] = array('isSuccess' => false);
			$data['isTimeOut'] = true;
			$data['data'] = array();
		}
		debug($data);
		$this -> set('aMetadata', $data);
	}

	/**
	 * This is an Ajax enabled action to upload photos for the user currently logged in.  Just need to check
	 * if they are logged in here and nothing else since we are using the session data
	 */
	public function add() {
		$data = array();
		if ($this -> isLoggedIn()) {
			debug($this -> request -> data);
			$this -> request -> data['UserUpload']['user_id'] = $this -> getUserId();

			//Seriously, need counter cache to work
			//Grab the count, if the current count is less than the total allowed, allow this one
			$uploadCount = $this -> UserUpload -> find('count', array('conditions' => array('UserUpload.user_id' => $this -> getUserId())));
			if ($uploadCount < Configure::read('Settings.User.uploads.total-allowed')) {
				if ($this -> UserUpload -> isValidUpload($this -> request -> data)) {

					if ($this -> UserUpload -> saveAll($this -> request -> data['UserUpload'])) {
						//Grab the user id of the upload that was just added
						$id = $this -> UserUpload -> id;
						$userUpload = $this -> UserUpload -> findById($id);
						/*
						 * We do not want our images to be wider than 100 and higher than 200
						 * This will handle resizing for us and then return the name and location
						 * of the image that we resized
						 */
						$img = $this -> Image -> image($userUpload['UserUpload']['name'], array('width' => 100, 'height' => 200, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $this -> getUserId()));
						$data['success'] = array('isSuccess' => true);
						$data['isTimeOut'] = false;
						$data['data'] = array();
						/*
						 * At this point we are changing license data, so we need to reset the series. We will determine if there are
						 * any series for this license and return a flag so the UI knows it can add a series
						 */
						$data['data']['imageLocation'] = $img['path'];
						$data['data']['imageHeight'] = $img['height'];
						$data['data']['imageName'] = $userUpload['UserUpload']['name'];
						$data['data']['count'] = ++$uploadCount;

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
				$data['success'] = array('isSuccess' => false);
				$data['isTimeOut'] = false;
				$data['errors'][0] = array('totalAllowed' => 'You have reached the maximum number of uploads allowed.');
			}

		} else {
			//If they are not logged in and are trying to access this then just time them out
			$data['success'] = array('isSuccess' => false);
			$data['isTimeOut'] = true;
			$data['data'] = array();
		}

		$this -> set('aUpload', $data);

	}

	public function delete() {
		$data = array();
		$this -> checkLogIn();
		$this -> request -> data = Sanitize::clean($this -> request -> data);
		debug($this -> request -> data);
		//Grab the user name from the request
		$uploadName = $this -> request -> data['UserUpload']['name'];
		//TODO: update the error, so it doesn't log the user out,
		if (!empty($uploadName)) {
			$userUpload = $this -> UserUpload -> findByName($uploadName);
			$this -> request -> data = $userUpload;
			debug($userUpload);
			if (!empty($userUpload)) {
				if ($userUpload['UserUpload']['user_id'] === $this -> getUserId()) {
					$this -> UserUpload -> id = $userUpload['UserUpload']['id'];
					if ($this -> UserUpload -> delete($userUpload['UserUpload']['id'])) {
						$data['success'] = array('isSuccess' => true);
						$data['isTimeOut'] = false;
						$data['data'] = array();
					} else {
						$data['success'] = array('isSuccess' => false);
						$data['isTimeOut'] = false;
						$data['errors'] = array($this -> UserUpload -> validationErrors);
					}

				} else {
					//return success false, trying to update upload user doesn't have access too
					$data['success'] = array('isSuccess' => false);
					$data['isTimeOut'] = true;
					$data['data'] = array();
				}
			} else {
				//return success false, invalid request
				$data['success'] = array('isSuccess' => false);
				$data['isTimeOut'] = true;
				$data['data'] = array();
			}
		} else {
			//return success false, invalid request
			$data['success'] = array('isSuccess' => false);
			$data['isTimeOut'] = true;
			$data['data'] = array();
		}
		debug($data);
		$this -> set('aUpload', $data);
	}

}
?>
