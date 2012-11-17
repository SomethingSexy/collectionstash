<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEvent', 'Event');
class CollectiblesUsersController extends AppController {

	public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js');

	public function view($id = null) {
		if (!is_null($id) && is_numeric($id)) {
			$id = Sanitize::clean($id, array('encode' => false));
			//TODO should be more model behavior but whateves
			//First lets grab the collectible user
			$collectiblesUser = $this -> CollectiblesUser -> getUserCollectible($id);
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
		if (!is_null($id) && is_numeric($id)) {
			$user = $this -> getUser();
			$collectible = $this -> CollectiblesUser -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $id), 'contain' => array('Currency')));
			if (!empty($this -> request -> data)) {
				if (isset($collectible) && !empty($collectible)) {
					
					//This returns all collectibles in this stash if I ever need them
					$stash = $this -> CollectiblesUser -> Stash -> find("first", array('contain' => false, 'conditions' => array('Stash.user_id' => $user['User']['id'])));
					$this -> request -> data['CollectiblesUser']['stash_id'] = $stash['Stash']['id'];
					$this -> request -> data['CollectiblesUser']['user_id'] = $this -> getUserId();
					$this -> request -> data['CollectiblesUser']['collectible_id'] = $collectible['Collectible']['id'];
					if ($this -> CollectiblesUser -> saveAll($this -> request -> data)) {
						$collectibleUser = $this -> CollectiblesUser -> getUserCollectible($this -> CollectiblesUser -> id);
						$this -> Session -> setFlash(__('Your collectible was successfully added.', true), null, null, 'success');
						//This should be in the model I think
						$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Stash.Collectible.add', $this, array('stashId' => $stash['Stash']['id'])));
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
			$collectibles = $this -> CollectiblesUser -> find("all", array('contain' => array('Collectible' => array('CollectiblesUpload' => array('Upload'), 'Collectibletype', 'Manufacture')), 'conditions' => array('CollectiblesUser.collectible_id' => $id, 'CollectiblesUser.user_id' => $user['User']['id'])));
			debug($collectibles);
			$this -> set(compact('collectibles'));
			$this -> set('collectible', $collectible);
			$this -> set('conditions', $this -> CollectiblesUser -> Condition -> find('list', array('order' => 'name')));
			$this -> set('merchants', $this -> CollectiblesUser -> Merchant -> find('all', array('contain' => false)));

		} else {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect($this -> referer());
		}
	}

	/**
	 * This method edits a user's collectible.
	 */
	function edit($id = null) {
		$this -> checkLogIn();
		debug($this -> request -> data);
		$collectiblesUser = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('User', 'Merchant', 'Collectible' => array('Currency'))));
		if (isset($collectiblesUser) && !empty($collectiblesUser)) {
			$loggedInUserId = $this -> getUserId();
			if ($loggedInUserId === $collectiblesUser['CollectiblesUser']['user_id']) {
				debug($collectiblesUser);
				if (!empty($this -> request -> data)) {
					$fieldList = array('edition_size', 'cost', 'condition_id', 'merchant_id', 'purchase_date', 'artist_proof');
					$this -> request -> data['CollectiblesUser']['collectible_id'] = $collectiblesUser['CollectiblesUser']['collectible_id'];
					if ($this -> CollectiblesUser -> save($this -> request -> data, true, $fieldList)) {
						$this -> Session -> setFlash(__('Your collectible was successfully updated.', true), null, null, 'success');
						$this -> redirect(array('controller' => 'collectibles_users', 'action' => 'view', $id), null, true);
						return;
					} else {
						$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					}

				} else {
					debug($collectiblesUser);
					$this -> request -> data = $collectiblesUser;
				}
				$this -> set('collectible', $collectiblesUser);
				$this -> set('conditions', $this -> CollectiblesUser -> Condition -> find('list', array('order' => 'name')));
				$this -> set('merchants', $this -> CollectiblesUser -> Merchant -> find('all', array('contain' => false)));

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
		//This can handle both ajax requests and standards requests...need to merge this logic though
		//problem is ajax is coming through as a POST and the other is coming through as a GET, should
		//probably make them all the same at some point TODO
		if ($this -> request -> isAjax()) {
			$data = array();
			if (!$this -> isLoggedIn()) {
				$data['success'] = array('isSuccess' => false);
				$data['error']['message'] = __('You must be logged in to remove your collectible.');
				$this -> set('removeCollectiblesUsers', $data);
				return;
			}
			if (($this -> request -> is('post') || $this -> request -> is('put')) && isset($this -> request -> data['CollectiblesUsers']['id'])) {
				$this -> request -> data = Sanitize::clean($this -> request -> data);
				$collectiblesUser = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.id' => $this -> request -> data['CollectiblesUsers']['id']), 'contain' => array('User')));
				if (isset($collectiblesUser) && !empty($collectiblesUser)) {
					$loggedInUserId = $this -> getUserId();
					if ($loggedInUserId === $collectiblesUser['CollectiblesUser']['user_id']) {
						if ($this -> CollectiblesUser -> delete($this -> request -> data['CollectiblesUsers']['id'])) {
							$data['success'] = array('isSuccess' => true);
							$this -> set('removeCollectiblesUsers', $data);
							return;
						} else {
							$data['success'] = array('isSuccess' => false);
							$data['error']['message'] = __('Invalid access.');
							$this -> set('removeCollectiblesUsers', $data);
							return;
						}
					} else {
						$data['success'] = array('isSuccess' => false);
						$data['error']['message'] = __('Invalid access.');
						$this -> set('removeCollectiblesUsers', $data);
						return;
					}
				} else {
					$data['success'] = array('isSuccess' => false);
					$data['error']['message'] = __('Invalid collectible.');
					$this -> set('removeCollectiblesUsers', $data);
					return;
				}
			} else {
				$data['success'] = array('isSuccess' => false);
				$data['error']['message'] = __('Invalid request.');
				$this -> set('removeCollectiblesUsers', $data);
				return;
			}
		} else {
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

	}

	public function registry($id = null) {
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

	public function beforeRender() {

	}

}
?>