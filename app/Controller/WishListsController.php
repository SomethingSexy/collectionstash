<?php
App::uses('Sanitize', 'Utility');
class WishListsController extends AppController {
	public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js', 'Time');

	public function view($userId = null, $type = 'tile') {
		$this -> layout = 'fluid';
		if (!is_null($userId)) {
			$userId = Sanitize::clean($userId, array('encode' => false));
			// we need to grab stash for privacy checks, since that is all done at the Stash level...probably should be moved to profile
			$user = $this -> WishList -> User -> find('first', array('conditions' => array('User.username' => $userId), 'contain' => array('WishList', 'Stash')));
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
					if ($user['Stash'][0]['privacy'] === '0' || $viewingMyStash || ($user['Stash'][0]['privacy'] === '1' && $this -> isLoggedIn())) {
						$this -> paginate = array('limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => array('CollectiblesWishList.user_id' => $user['User']['id']), 'contain' => array('Collectible' => array('User', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype')));
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