<?php
App::uses('Sanitize', 'Utility');
class CollectiblesUsersController extends AppController {

	public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify');

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
				$this -> set(compact('viewMyCollectible'));
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

	/**
	 * This method adds a collectible to a user's stash
	 */
	public function add($id = null) {
		$this -> checkLogIn();
		debug($id);
		debug($this -> request -> data);
		if (!is_null($id) && is_numeric($id)) {
			$collectible = $this -> CollectiblesUser -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $id), 'contain' => array('Currency')));
			debug($collectible);
			if (!empty($this -> request -> data)) {
				if (isset($collectible) && !empty($collectible)) {
					//$fieldList = array('edition_size', 'cost', 'condition_id', 'merchant_id');
					$user = $this -> getUser();
                    //This returns all collectibles in this stash if I ever need them
					$stash = $this -> CollectiblesUser -> Stash -> find("first", array('conditions' => array('Stash.user_id' => $user['User']['id'])));

					$this -> request -> data['CollectiblesUser']['stash_id'] = $stash['Stash']['id'];
					$this -> request -> data['CollectiblesUser']['user_id'] = $this -> getUserId();
					$this -> request -> data['CollectiblesUser']['collectible_id'] = $collectible['Collectible']['id'];
					if ($this -> CollectiblesUser -> saveAll($this -> request -> data)) {
						$collectibleUser = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.id' => $this -> CollectiblesUser -> id), 'contain' => false));
						$this -> Session -> setFlash(__('Your collectible was successfully added.', true), null, null, 'success');
						$this -> redirect(array('action' => 'view', $collectibleUser['CollectiblesUser']['id']), null, true);
						return;
					} else {
						$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					}

				} else {
					$this -> Session -> setFlash(__('Invalid collectible', true), null, null, 'error');
					$this -> redirect($this -> referer(), null, true);
					return;
				}
			}
			$this -> set('collectible', $collectible);
			$this -> loadModel('Condition');
			$this -> set('conditions', $this -> Condition -> find('list', array('order' => 'name')));
			$this -> loadModel('Merchant');
			$this -> set('merchants', $this -> Merchant -> find('list', array('order' => 'name')));

		} else {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect($this -> referer());
		}
		debug($this->CollectiblesUser->validationErrors);
	}

	/**
	 * This method edits a user's collectible.
	 */
	function edit($id = null) {
		$this -> checkLogIn();
		debug($this -> request -> data);
		$collectiblesUser = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('User', 'Collectible' => array('Currency'))));
		if (isset($collectiblesUser) && !empty($collectiblesUser)) {
			$loggedInUserId = $this -> getUserId();
			if ($loggedInUserId === $collectiblesUser['CollectiblesUser']['user_id']) {
				debug($collectiblesUser);
				if (!empty($this -> request -> data)) {
					$fieldList = array('edition_size', 'cost', 'condition_id', 'merchant_id', 'purchase_date','artist_proof');
					$this -> request -> data['CollectiblesUser']['collectible_id'] = $collectiblesUser['CollectiblesUser']['collectible_id'];
					if ($this -> CollectiblesUser -> save($this -> request -> data, true, $fieldList)) {
						$this -> Session -> setFlash(__('Your collectible was successfully updated.', true), null, null, 'success');
						$this -> redirect(array('controller' => 'collectibles_users', 'action' => 'view', $id), null, true);
						return;
					} else {
						$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					}

				} else {
					$this -> request -> data = $collectiblesUser;
				}
				$this -> set('collectible', $collectiblesUser);
				$this -> loadModel('Condition');
				$this -> set('conditions', $this -> Condition -> find('list', array('order' => 'name')));
				$this -> loadModel('Merchant');
				$this -> set('merchants', $this -> Merchant -> find('list', array('order' => 'name')));

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
		if (!is_null($id) && is_numeric($id)) {
			$collectible = $this -> CollectiblesUser -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $id), 'contain' => false));
			if (!empty($collectible)) {
				$usersWho = $this -> CollectiblesUser -> getListOfUsersWho($id, $collectible['Collectible']['numbered']);
				debug($usersWho);
				$this -> set('showEditionSize', $collectible['Collectible']['numbered']);
				$this -> set('registry', $usersWho);
			} else {
				$this -> redirect("/", null, true);
			}

		} else {
			$this -> redirect("/", null, true);
		}
	}
	
	public function beforeRender(){
		debug($this->validationErrors);	
	}

}
?>