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

		$profile = $this -> Profile -> find('first', array('conditions' => array('Profile.user_id' => $this -> getUserId()), 'contain' => false));

		$this -> set(compact('profile'));
	}

	public function profile($id = null) {
		// need to be logged in
		if (!$this -> isLoggedIn()) {
			$this -> response -> statusCode(401);
			return;
		}

		// create
		if ($this -> request -> isPost()) {
			// do nothing for now
		} else if ($this -> request -> isPut()) {// update
			$profile = $this -> request -> input('json_decode', true);
			// no need to clean for now on the update
			$profile = Sanitize::clean($profile);

			$response = $this -> Profile -> updateProfile($profile, $this -> getUser());

			$this -> set('returnData', $response);
		} else if ($this -> request -> isDelete()) {// delete
			// do nothing for now
		}
	}

}
?>