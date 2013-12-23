<?php
App::uses('Sanitize', 'Utility');
class WishListsController extends AppController {
	public $name = 'WishLists';
	public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js', 'Time');

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
		$this -> layout = 'fluid';
		if (!is_null($userId)) {
			$userId = Sanitize::clean($userId, array('encode' => false));
			$user = $this -> WishList -> User -> find('first', array('conditions' => array('User.username' => $userId), 'contain' => array('WishList')));
			//Ok we have a user, although this seems kind of inefficent but it works for now
			if (!empty($user)) {
				if (!empty($user['WishList'])) {
					$loggedInUser = $this -> getUser();
					$viewingMyStash = false;
					if ($loggedInUser['User']['id'] === $user['User']['id']) {
						$viewingMyStash = true;
					}
					$this -> set('myStash', $viewingMyStash);
					$this -> set('stashUsername', $userId);
					//If the privacy is 0 or you are viewing your own stash then always show
					//or if it is set to 1 and this person is logged in also show.
					if ($user['WishList']['privacy'] === '0' || $viewingMyStash || ($user['WishList']['privacy'] === '1' && $this -> isLoggedIn())) {
						$this -> paginate = array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesWishList.wish_list_id'))), 'limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => array('CollectiblesWishList.user_id' => $user['User']['id']), 'contain' => array('Collectible' => array('User', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype')));
						$collectibles = $this -> paginate('CollectiblesWishList');

						$this -> set(compact('collectibles'));
						$this -> set('stash', $user['WishList']);
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
			$this -> redirect('/', null, true);
		}
	}

}
?>