<?php
App::uses('Sanitize', 'Utility');
class StashsController extends AppController {
	public $name = 'Stashs';
	public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js', 'Time');

	/*
	 * This action will be used to allow the user to view/edit their stash.  Individual collectible edits will happen in
	 * the ColletiblesUsers controller.  This will be the main launching point.  Although one could argue that this
	 * should go in the CollectiblesUsers controller.
	 *
	 * Right now, I am not keying this by Stash, if I ever get back into multiple stashes this will have to be updated.
	 */
	public function edit() {
		//Since we are making sure they are logged in, there should always be a user
		$this -> checkLogIn();
		$user = $this -> getUser();

		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			debug($this -> request -> data);
			$this -> request -> data = Sanitize::clean($this -> request -> data);

			// foreach ($this -> request -> data['CollectiblesUser'] as $key => $value) {
			// // $this -> Stash -> CollectiblesUser -> id = $value['id'];
			// // $this -> Stash -> CollectiblesUser -> saveField('sort_number', $value['sort_number']);
			//
			// if ($this -> Stash -> CollectiblesUser -> save($value, array('fieldList' => array('sort_number'), 'callbacks' => false))) {
			//
			// } else {
			// debug($this -> Stash -> CollectiblesUser -> validationErrors);
			// }
			// }

			if ($this -> Stash -> CollectiblesUser -> saveMany($this -> request -> data['CollectiblesUser'], array('fieldList' => array('sort_number'), 'callbacks' => false))) {
				$this -> Session -> setFlash(__('Your sort was successfully saved.', true), null, null, 'success');
			} else {
				debug($this -> Stash -> CollectiblesUser -> validationErrors);
				$this -> Session -> setFlash(__('There was a problem saving your sort.', true), null, null, 'error');
			}

		}
		//Ok we have a user, although this seems kind of inefficent but it works for now
		$this -> set('myStash', true);
		$this -> set('stashUsername', $user['User']['username']);

		$collectibles = $this -> Stash -> CollectiblesUser -> find("all", array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'order' => array('sort_number' => 'desc'), 'conditions' => array('CollectiblesUser.user_id' => $user['User']['id']), 'contain' => array('Condition', 'Merchant', 'Collectible' => array('User', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype'))));

		$this -> set(compact('collectibles'));

	}

	public function profileSettings() {
		if ($this -> isLoggedIn()) {
			$user = $this -> getUser();

			$stash = $this -> Stash -> find("first", array('conditions' => array('Stash.user_id' => $user['User']['id']), 'contain' => false));
			$profileSettings = array();
			$profileSettings['privacy'] = $stash['Stash']['privacy'];

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

	public function view($userId = null, $type = 'tile') {
		$this -> set('stashType', 'default');
		if (!is_null($userId)) {
			$userId = Sanitize::clean($userId, array('encode' => false));
			$user = $this -> Stash -> User -> find("first", array('conditions' => array('User.username' => $userId), 'contain' => array('Stash')));
			//Ok we have a user, although this seems kind of inefficent but it works for now
			if (!empty($user)) {
				if (!empty($user['Stash'])) {
					$loggedInUser = $this -> getUser();
					$viewingMyStash = false;
					if ($loggedInUser['User']['id'] === $user['User']['id']) {
						$viewingMyStash = true;
					}
					$this -> set('myStash', $viewingMyStash);
					$this -> set('stashUsername', $userId);
					//If the privacy is 0 or you are viewing your own stash then always show
					//or if it is set to 1 and this person is logged in also show.
					if ($user['Stash'][0]['privacy'] === '0' || $viewingMyStash || ($user['Stash'][0]['privacy'] === '1' && $this -> isLoggedIn())) {
						$this -> paginate = array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => array('CollectiblesUser.active' => true, 'CollectiblesUser.user_id' => $user['User']['id']), 'contain' => array('Condition', 'Merchant', 'Collectible' => array('User', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype', 'ArtistsCollectible' => array('Artist'))));
						$collectibles = $this -> paginate('CollectiblesUser');
						$this -> set(compact('collectibles'));
						$this -> set('stash', $user['Stash'][0]);

						$reasons = $this -> Stash -> CollectiblesUser -> CollectibleUserRemoveReason -> find('all', array('contain' => false));
						$this -> set(compact('reasons'));

						// This will us the standard view
						if ($type === 'list') {
							$this -> render('view_list');
						} else {
							$this -> render('view_v2');
						}
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
			//$this -> redirect('/', null, true);
		}
	}

	// TODO: Since this is a special stash type, it will need its own view because
	// we will not want to show the collectible_user information
	public function wishlist($userId = null, $type = 'tile') {
		$this -> set('stashType', 'wishlist');
		if (!is_null($userId)) {
			$userId = Sanitize::clean($userId, array('encode' => false));
			$user = $this -> Stash -> User -> find("first", array('conditions' => array('User.username' => $userId), 'contain' => array('Stash')));
			//Ok we have a user, although this seems kind of inefficent but it works for now
			if (!empty($user)) {
				if (!empty($user['Stash'])) {
					$loggedInUser = $this -> getUser();
					$viewingMyStash = false;
					if ($loggedInUser['User']['id'] === $user['User']['id']) {
						$viewingMyStash = true;
					}
					$this -> set('myStash', $viewingMyStash);
					$this -> set('stashUsername', $userId);
					//If the privacy is 0 or you are viewing your own stash then always show
					//or if it is set to 1 and this person is logged in also show.
					if ($user['Stash'][0]['privacy'] === '0' || $viewingMyStash || ($user['Stash'][0]['privacy'] === '1' && $this -> isLoggedIn())) {
						$this -> paginate = array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Wishlist"'))), 'limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => array('CollectiblesUser.user_id' => $user['User']['id']), 'contain' => array('Condition', 'Merchant', 'Collectible' => array('User', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype')));
						$collectibles = $this -> paginate('CollectiblesUser');

						$this -> set(compact('collectibles'));
						$this -> set('stash', $user['Stash'][0]);
						// This will us the standard view
						if ($type === 'list') {
							$this -> render('view_list');
						} else {
							$this -> render('view_v2');
						}

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
			//$this -> redirect('/', null, true);
		}
	}

	/**
	 * WTF is this doing?
	 */
	public function comments($userId = null) {
		if (!is_null($userId)) {
			$userId = Sanitize::clean($userId, array('encode' => false));
			//Also retrieve the UserUploads at this point, so we do not have to do it later and comments
			$user = $this -> Stash -> User -> find("first", array('conditions' => array('User.username' => $userId), 'contain' => array('Stash')));
			//Ok we have a user, although this seems kind of inefficent but it works for now
			if (!empty($user)) {
				if (!empty($user['Stash'])) {
					$loggedInUser = $this -> getUser();
					$viewingMyStash = false;
					if ($loggedInUser['User']['id'] === $user['User']['id']) {
						$viewingMyStash = true;
					}
					$this -> set('myStash', $viewingMyStash);
					$this -> set('stashUsername', $userId);
					//If the privacy is 0 or you are viewing your own stash then always show
					//or if it is set to 1 and this person is logged in also show.
					if ($user['Stash'][0]['privacy'] === '0' || $viewingMyStash || ($user['Stash'][0]['privacy'] === '1' && $this -> isLoggedIn())) {
						$this -> set('stash', $user['Stash'][0]);
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
	}

	public function history($userId = null) {
		$this -> layout = 'fluid';

		if (!is_null($userId)) {
			$userId = Sanitize::clean($userId, array('encode' => false));
			//Also retrieve the UserUploads at this point, so we do not have to do it later and comments
			$user = $this -> Stash -> User -> find("first", array('conditions' => array('User.username' => $userId), 'contain' => array('Stash')));
			//Ok we have a user, although this seems kind of inefficent but it works for now
			if (!empty($user)) {
				if (!empty($user['Stash'])) {
					$loggedInUser = $this -> getUser();
					$viewingMyStash = false;
					if ($loggedInUser['User']['id'] === $user['User']['id']) {
						$viewingMyStash = true;
					}
					$this -> set('myStash', $viewingMyStash);
					$this -> set('stashUsername', $userId);
					//If the privacy is 0 or you are viewing your own stash then always show
					//or if it is set to 1 and this person is logged in also show.
					if ($user['Stash'][0]['privacy'] === '0' || $viewingMyStash || ($user['Stash'][0]['privacy'] === '1' && $this -> isLoggedIn())) {
						$graphData = $this -> Stash -> getStashGraphHistory($user);
						debug($graphData);
						$this -> set(compact('graphData'));
						$this -> set('stash', $user['Stash'][0]);
						$this -> paginate = array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => array('CollectiblesUser.user_id' => $user['User']['id']), 'contain' => array('Listing' => array('Transaction'), 'Condition', 'Merchant', 'Collectible' => array('User', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype', 'ArtistsCollectible' => array('Artist'))));
						$collectibles = $this -> paginate('CollectiblesUser');
						$this -> set(compact('collectibles'));
						$reasons = $this -> Stash -> CollectiblesUser -> CollectibleUserRemoveReason -> find('all', array('contain' => false));
						$this -> set(compact('reasons'));

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
	}

}
?>