<?php
App::uses('Sanitize', 'Utility');
class StashsController extends AppController {
	public $name = 'Stashs';
	public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify');

	function stats($id = null) {
		//This is private stuff for sure make sure they are logged in
		$this -> checkLogIn();

		if ($id) {
			$id = Sanitize::clean($id, array('encode' => false));
			$stash = $this -> Stash -> findById($id);
			//Check to make sure that the user id tied to this stash is the one logged in
			if ($stash['Stash']['user_id'] == $this -> getUserId()) {
				$stashStats = $this -> Stash -> getStashStats($id);
				//CollectiblesUser -> find('all', array('conditions' => array('CollectiblesUser.stash_id'=>$id), 'contain'=> false));
				$this -> set(compact('stashStats'));
			} else {
				$this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
			}

		} else {
			$this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
		}

	}

	/**
	 * 8.10.11 - Should I use this as the main entry point for a user stash?
	 */
	// function index($id = null) {
	// //Add something in here that if they aren't logged in and they pass in an id, we return that users collection
	// $username = $this -> getUsername();
	// if($username) {
	// if($id) {
	// $this -> setStashIdSession($id);
	// } else {
	// $id = $this -> getStashIdSession();
	// }
	//
	// if($id) {
	// $result = $this -> Stash -> User -> findByUsername($username);
	// $this -> loadModel('Stash');
	//
	// //TODO need to chec to see if $id is valid
	// $this -> setStashIdSession($id);
	// /*
	// * Right now, I am searching and paginating on a stash because
	// * that is what I am viewing.  Really this should be done in the stashs
	// * controller and not here since I am trying to get rid of this controller.
	// *
	// * This will let me eventually have other types attached to one stash.
	// */
	// $this -> paginate = array('conditions' => array('id' => $id), 'limit' => 20, 'contain' => array('CollectiblesUser' => array('Collectible' => array('Manufacture', 'License', 'Collectibletype', 'Upload'))));
	//
	// //        $this->paginate = array('conditions'=>array('CollectiblesUser.stash_id'=>$id),
	// //                'limit' => 20,
	// //                'contain'=>array(
	// //                'Collectible' => array ( 'Manufacture','Collectibletype','Upload'))
	// //        );
	//
	// // $data = $this->User->CollectiblesUser->find("all", array(
	// //         'conditions'=>array('CollectiblesUser.stash_id'=>$id),
	// //         //'limit' => 1,
	// //         'contain'=>array(
	// //         'Collectible' => array ( 'Manufacture','Collectibletype','Upload'))
	// //
	// //     ));
	// $collectibleCount = $this -> Stash -> getNumberOfCollectiblesInStash($id);
	// $this -> set('collectibleCount', $collectibleCount);
	// $data = $this -> paginate('Stash');
	// debug($data[0]['CollectiblesUser']);
	// $this -> set('collectibles', $data[0]['CollectiblesUser']);
	// } else {
	// $this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
	// }
	// } else {
	// $this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
	// }
	// }

	public function edit() {
		$this -> request -> data = Sanitize::clean($this -> request -> data, array('encode' => false));
		$username = $this -> getUsername();
		if ($username) {//check to see they passed in data for the add
			if (!empty($this -> request -> data)) {
				//Grab the stash we are trying to edit
				$stashForEdit = $this -> Stash -> findById($this -> request -> data['Stash']['id']);
				debug($stashForEdit);
				$userId = $this -> getUserId();
				$this -> loadModel('User');
				$this -> User -> id = $this -> getUserId();
				$this -> request -> data['Stash']['user_id'] = $userId;
				$user = $this -> User -> find();
				$this -> request -> data['Stash']['total_count'] = $user['User']['stash_count'];
				debug($this -> request -> data);
				//Check to make sure that the user id tied to this stash is the one logged in
				if ($stashForEdit['Stash']['user_id'] == $this -> getUserId()) {
					$this -> Stash -> id = $this -> request -> data['Stash']['id'];
					if ($this -> Stash -> save($this -> request -> data, true)) {
						$this -> set('aStash', array('success' => array('isSuccess' => true, 'message' => __('You have successfully edited your Stash.', true))));
					} else {
						$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'errors' => array($this -> User -> Stash -> validationErrors)));
					}
				} else {
					//$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
				}

			} else {
				//$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
			}
		} else {
			//$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
		}
	}

	public function stashList() {
		$username = $this -> getUsername();
		if ($username) {
			$user = $this -> getUser();
			$this -> loadModel('User');
			$stashCount = $this -> User -> getNumberOfStashesByUser($username);
			$stashDetails = $this -> User -> Stash -> getStashDetails($user['User']['id']);
			$this -> set('aStash', $stashDetails);
		} else {
			$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
		}

	}

	// public function add() {
	// $this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
	// $username = $this -> getUsername();
	// //TODO make sure the stash you are adding to is the same as the person logged in
	// if ($username) {
	// //check to see they passed in data for the add
	// if (!empty($this->request->data)) {
	//
	// if ($this -> RequestHandler -> isAjax()) {
	// Configure::write('debug', 0);
	// }
	//
	// $userId = $this -> getUserId();
	// $this -> loadModel('User');
	// $this -> User -> id = $this -> getUserId();
	// $this->request->data['Stash']['user_id'] = $userId;
	// $user = $this -> User -> find();
	// $this->request->data['Stash']['total_count'] = $user['User']['stash_count'];
	// $this -> User -> Stash -> create();
	// if ($this -> User -> Stash -> save($this->request->data)) {
	// $this -> set('aStash', array('success' => array('isSuccess' => true, 'message' => __('You have successfully created a new Stash.', true))));
	// } else {
	// $this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'errors' => array($this -> User -> Stash -> validationErrors)));
	// }
	// } else {
	// $this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
	// }
	// } else {
	// $this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
	// }
	// }

	// public function remove() {
	// $this -> autoRender = false;
	// $username = $this -> getUsername();
	// if ($username) {
	// //check to see they passed in data for the add
	// if (!empty($this->request->data)) {
	// //Grab the stash we are trying to remove
	// $stashForEdit = $this -> Stash -> findById($this->request->data['Stash']['id']);
	//
	// //Check to make sure that the user id tied to this stash is the one logged in
	// if ($stashForEdit['Stash']['user_id'] == $this -> getUserId()) {
	//
	// $this -> Stash -> id = $this->request->data['Stash']['id'];
	// if ($this -> Stash -> delete()) {
	// $this -> Session -> setFlash(__('You have successfully deleted stash ' . $stashForEdit['Stash']['name'] . '.', true), null, null, 'success');
	// $this -> redirect(array('controller' => 'users', 'action' => 'home'), null, true);
	// }
	// }
	// }
	// }
	//
	// $this -> Session -> setFlash(__('There was a problem trying to delete your stash.', true), null, null, 'success');
	// $this -> redirect(array('controller' => 'users', 'action' => 'home'), null, true);
	// }

	public function profileSettings() {
		if ($this -> isLoggedIn()) {
			$user = $this -> getUser();

			$stash = $this -> Stash -> find("first", array('conditions' => array('Stash.user_id' => $user['User']['id']), 'contain' => false));
			$profileSettings = array();
			if ($stash['Stash']['privacy'] === '0') {
				$profileSettings['privacy'] = false;
			} else if ($stash['Stash']['privacy'] === '1') {
				$profileSettings['privacy'] = true;
			}

			$this -> set('aProfileSettings', array('success' => array('isSuccess' => true), 'isTimeOut' => false, 'responseData' => $profileSettings));
		} else {
			$this -> set('aProfileSettings', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
		}
	}

	public function updateProfileSettings() {
		$this -> request -> data = Sanitize::clean($this -> request -> data, array('encode' => false));
		if ($this -> isLoggedIn()) {
			if (!empty($this -> request -> data)) {
				$user = $this -> getUser();

				$stash = $this -> Stash -> find("first", array('conditions' => array('Stash.user_id' => $user['User']['id']), 'contain' => false));

				$this -> Stash -> id = $stash['Stash']['id'];
				if (!isset($this -> request -> data['Stash']['privacy'])) {
					$this -> request -> data['Stash']['privacy'] = 0;
				}
				if ($this -> Stash -> saveField('privacy', $this -> request -> data['Stash']['privacy'])) {
					$this -> set('aProfileSettings', array('success' => array('isSuccess' => true, 'message' => __('You have successfully updated your settings.', true))));
				} else {
					$this -> set('aProfileSettings', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'errors' => array($this -> Stash -> validationErrors)));
				}
			} else {
				$this -> set('aProfileSettings', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'message' => array('There was an issue trying to save your settings.')));
			}

		} else {
			$this -> set('aProfileSettings', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
		}
	}

	public function view($userId = null) {
		if (!is_null($userId)) {
			$view = 'glimpse';
			if (isset($this -> request -> params['named']['view'])) {
				$view = $this -> request -> params['named']['view'];
			}

			$userId = Sanitize::clean($userId, array('encode' => false));
			//Also retrieve the UserUploads at this point, so we do not have to do it later
			$user = $this -> Stash -> User -> find("first", array('conditions' => array('User.username' => $userId), 'contain' => array('Stash', 'UserUpload')));
			debug($user);
			//Ok we have a user, although this seems kind of inefficent but it works for now
			if (!empty($user)) {
				$stash = $this -> Stash -> find("first", array('conditions' => array('Stash.user_id' => $user['User']['id'])));
				if (!empty($stash)) {
					$loggedInUser = $this -> getUser();
					$viewingMyStash = false;
					if ($loggedInUser['User']['id'] === $user['User']['id']) {
						$viewingMyStash = true;
					}
					$this -> set('myStash', $viewingMyStash);
					$this -> set('stashUsername', $userId);
					if ($stash['Stash']['privacy'] === '0' || $viewingMyStash) {
						//$collectibles = $this -> Stash -> CollectiblesUser -> find("all", array('conditions' => array('CollectiblesUser.stash_id' => $stash['Stash']['id']), 'contain' => array('Collectible' => array('Upload', 'Manufacture', 'License', 'Collectibletype')), 'limit' => 24));
						$this -> paginate = array('conditions' => array('CollectiblesUser.stash_id' => $stash['Stash']['id']), 'contain' => array('Collectible' => array('Upload', 'Manufacture', 'License', 'Collectibletype')), 'limit' => 24);
						$collectibles = $this -> paginate('CollectiblesUser');

						//$collectibles = $this -> Stash -> CollectiblesUser -> find("all", array());
						debug($collectibles);
						$this -> set('userUploads', $user['UserUpload']);
						$this -> set(compact('collectibles'));
					} else {
						$this -> render('view_private');
						return;
					}
				} else {
					//This is a fucking error
					$this -> redirect('/', null, true);
				}
			} else {
				$this -> render('view_no_exist');
				return;
			}

		} else {
			$this -> redirect('/', null, true);
		}

		if ($view === 'glimpse') {
			$this -> render('view_glimpse');
		} else {
			$this -> render('view_detail');
		}

		//TODO save for future use
		// $this -> loadModel('CollectiblesUser');
		// debug($this -> CollectiblesUser -> find('all', array('conditions' => array('CollectiblesUser.user_id' => '14', 'Collectible.name' => 'test'))));
		//
		// //Find all collectible users whose collectible id is 217 that are in tash 62
		// debug($this -> Stash -> find('all', array('conditions' => array('Stash.id' => '62'), 'contain' => array('CollectiblesUser' => array('conditions' => array('collectible_id' => '217'))))));
		// //Find all collectible users and poster users for stash id 62
		// debug($this -> Stash -> find('all', array('conditions' => array('Stash.id' => '62'), 'contain' => array('CollectiblesUser', 'PostersUser'))));
		//
		// debug($this -> Stash -> query('SELECT id, stash_id FROM collectibles_users
		// UNION SELECT id, stash_id FROM posters_users'));

	}

	// private function setStashIdSession($id) {
	// if($id) {
	// $this -> Session -> write('stashId', $id);
	// }
	// }
	//
	// private function getStashIdSession() {
	// return $this -> Session -> read('stashId');
	// }

}
?>