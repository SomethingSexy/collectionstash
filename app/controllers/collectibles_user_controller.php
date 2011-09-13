<?php
App::import('Sanitize');
class CollectiblesUserController extends AppController {

	var $name = 'CollectiblesUser';
	var $helpers = array('Html', 'Form', 'Ajax', 'FileUpload.FileUpload');
	var $components = array('RequestHandler');

	public function view($id = null) {
		if (!is_null($id) && is_numeric($id)) {
			$id = Sanitize::clean($id, array('encode' => false));
			//TODO should be more model behavior but whateves
			//First lets grab the collectible user
			$collectiblesUser = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('Collectible' => 'Upload', 'User', 'Stash', 'Condition', 'Merchant')));
			debug($collectiblesUser);
			if (isset($collectiblesUser) && !empty($collectiblesUser)) {
				//First see if the person viewing this collectible is logged in
				$this -> set('stashUsername', $collectiblesUser['User']['username']);
				$viewMyCollectible = false;
				if ($this -> isLoggedIn()) {
					//If they are logged in, check to see if the user ids match up
					if ($this -> getUserId() === $collectiblesUser['User']['id']) {
						$viewMyCollectible = true;
					}
				}
				if ($collectiblesUser['Stash']['privacy'] === '0' || $viewMyCollectible) {
					//You are looking at your collectible, well then BAM, show that shit
					$this -> set('collectible', $collectiblesUser);
				} else {
					$this -> render('viewPrivate');
					return;
				}
			} else {
				$this -> Session -> setFlash(__('Invalid collectible', true));
				$this -> redirect($this -> referer());
			}
		} else {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect($this -> referer());
		}

	}

	public function add($id = null) {
		$this -> checkLogIn();
		if (!empty($this -> data)) {
			$this -> data['CollectiblesUser']['user_id'] = $this -> getUserId();
			$user = $this -> getUser();
			$stash = $this -> CollectiblesUser -> Stash -> find("first", array('conditions' => array('Stash.user_id' => $user['User']['id'])));
			$this -> data['CollectiblesUser']['stash_id'] = $stash['Stash']['id'];
			$collectible_id = $this -> data['CollectiblesUser']['collectible_id'];
			debug($this -> data);
			//TODO should probably do this in the model
			//$this -> loadModel('Collectible');
			$this -> CollectiblesUser -> Collectible -> recursive = -1;
			$collectible = $this -> CollectiblesUser -> Collectible -> findById($collectible_id);
			debug($collectible);
			//Save as a different name so the saveAll doesn't accidently save it
			$this -> data['TempCollectible'] = $collectible['Collectible'];

			debug($this -> data);
			if ($this -> CollectiblesUser -> save($this -> data)) {
				$collectibleUserId = $this -> CollectiblesUser -> id;
				$this -> redirect(array('action' => 'view', $collectibleUserId), "null", true);
				$this -> Session -> setFlash(__('Collectible was successfully added.', true), null, null, 'success');
			} else {
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			}
		} else {
			if (!is_null($id) && is_numeric($id)) {
				$this -> data['CollectiblesUser']['collectible_id'] = $id;

			} else {
				$this -> Session -> setFlash(__('Invalid collectible', true));
				$this -> redirect($this -> referer());
			}
		}
		$conditions = $this -> CollectiblesUser -> Condition -> find("list", array('order' => array('Condition.name' => 'ASC')));

		$merchants = $this -> CollectiblesUser -> Merchant -> find("list", array('order' => array('Merchant.name' => 'ASC')));

		$this -> set(compact('conditions'));
		$this -> set(compact('merchants'));
	}
	/**
	 * This method edits a user's collectible.
	 */
	function edit($id = null) {
		$this -> checkLogIn();
		debug($this -> data);
		$collectiblesUser = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('User', 'Collectible')));
		debug($collectiblesUser);
		if (!empty($this -> data)) {
			if (isset($collectiblesUser) && !empty($collectiblesUser)) {
				$loggedInUserId = $this -> getUserId();
				if ($loggedInUserId === $collectiblesUser['CollectiblesUser']['user_id']) {
					$fieldList = array('edition_size', 'cost', 'condition_id', 'merchant_id');
					$this -> data['CollectiblesUser']['collectible_id'] = $collectiblesUser['CollectiblesUser']['collectible_id'];
					if ($this -> CollectiblesUser -> save($this -> data, true, $fieldList)) {
						$this -> Session -> setFlash(__('Your collectible was successfully updated.', true), null, null, 'success');
						$this -> redirect(array('controller' => 'collectibles_user', 'action' => 'view', $id), null, true);
						return;
					} else {
						$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					}
				} else {
					$this -> Session -> setFlash(__('Invalid access', true), null, null, 'error');
					$this -> redirect('/', null, true);
					return;
				}
			} else {
				$this -> Session -> setFlash(__('Invalid collectible', true), null, null, 'error');
				$this -> redirect($this -> referer(), null, true);
				return;
			}
		} else {
			$this -> data = $collectiblesUser;
		}
		$this -> set('collectible', $collectiblesUser);
		$this -> loadModel('Condition');
		$this -> set('conditions', $this -> Condition -> find('list', array('order' => 'name')));
		$this -> loadModel('Merchant');
		$this -> set('merchants', $this -> Merchant -> find('list', array('order' => 'name')));
	}

	/**
	 * Removes a user's collectible
	 *
	 */
	function remove($id = null) {
		$this -> checkLogIn();
		if (!is_null($id) && is_numeric($id)) {
			$collectiblesUser = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('User')));
			if (isset($collectiblesUser) && !empty($collectiblesUser)) {
				$loggedInUserId = $this -> getUserId();
				if ($loggedInUserId === $collectiblesUser['CollectiblesUser']['user_id']) {
					if ($this -> CollectiblesUser -> delete($id)) {
						$this -> Session -> setFlash(__('Your collectible has been successfully removed.', true), null, null, 'success');
						$this -> redirect(array('controller' => 'stashs', 'action' => 'view', $collectiblesUser['User']['username']), null, true);
					} else {
						$this -> Session -> setFlash(__('Invalid access', true), null, null, 'error');
						$this -> redirect(array('action' => 'view', $id), null, true);
					}
				} else {
					$this -> Session -> setFlash(__('Invalid access', true), null, null, 'error');
					$this -> redirect($this -> referer(), null, true);
				}
			} else {
				$this -> Session -> setFlash(__('Invalid collectible', true), null, null, 'error');
				$this -> redirect($this -> referer(), null, true);
			}
		} else {
			$this -> Session -> setFlash(__('Invalid collectible', true), null, null, 'error');
			$this -> redirect($this -> referer(), null, true);
		}
	}

	public function registry($id = null) {
		$this -> checkLogIn();
		if ($id) {
			$usersWho = $this -> CollectiblesUser -> getListOfUsersWho($id);
			debug($usersWho);
			$this -> set('registry', $usersWho);
		}
	}

}
?>