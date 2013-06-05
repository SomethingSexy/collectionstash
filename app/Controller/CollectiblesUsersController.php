<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesUsersController extends AppController {

	public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js');

	public function view($id = null) {
		if (!is_null($id) && is_numeric($id)) {
			$id = Sanitize::clean($id, array('encode' => false));
			//TODO should be more model behavior but whateves
			//First lets grab the collectible user
			$collectiblesUser = $this -> CollectiblesUser -> getUserCollectible($id);
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
	 * This will handle add, update, delete asynchronously
	 */
	public function collectible($id = null, $type = 'Default') {

		if (!$this -> isLoggedIn()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to add a collectible.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}

		if ($this -> request -> isPut()) {

		} else if ($this -> request -> isPost()) {
			$collectible['CollectiblesUser'] = $this -> request -> input('json_decode', true);
			$collectible['CollectiblesUser'] = Sanitize::clean($collectible['CollectiblesUser']);

			$response = $this -> CollectiblesUser -> add($collectible, $this -> getUser(), $type);

			$this -> set('returnData', $response);
		} else if ($this -> request -> isDelete()) {
			// for now this will handle deletes where the user is prompted
			// about the delete
			// we need to pull the query parameters
			$value = $this -> request -> query('collectible_user_remove_reason_id');
			$value = $this -> request -> query('sold_cost');
			$value = $this -> request -> query('remove_date');
			
			

		} else if ($this -> request -> isGet()) {

		}
	}

	/**
	 * This method adds a collectible to a user's stash
	 *
	 * TODO: Update this at some point to make it ajax
	 */
	public function add($id = null) {
		$this -> checkLogIn();
		if (!is_null($id) && is_numeric($id)) {
			$user = $this -> getUser();
			$collectible = $this -> CollectiblesUser -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $id), 'contain' => array('Currency')));
			if (!empty($this -> request -> data)) {
				if (isset($collectible) && !empty($collectible)) {

					//This returns all collectibles in this stash if I ever need them
					$stash = $this -> CollectiblesUser -> Stash -> find("first", array('contain' => array('User'), 'conditions' => array('Stash.user_id' => $user['User']['id'])));
					$this -> request -> data['CollectiblesUser']['stash_id'] = $stash['Stash']['id'];
					$this -> request -> data['CollectiblesUser']['user_id'] = $this -> getUserId();
					$this -> request -> data['CollectiblesUser']['collectible_id'] = $collectible['Collectible']['id'];
					if ($this -> CollectiblesUser -> saveAll($this -> request -> data)) {
						$collectibleUser = $this -> CollectiblesUser -> getUserCollectible($this -> CollectiblesUser -> id);
						$this -> Session -> setFlash(__('Your collectible was successfully added.', true), null, null, 'success');
						//This should be in the model I think
						$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Stash.Collectible.add', $this, array('stashId' => $stash['Stash']['id'])));
						$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$ADD_COLLECTIBLE_STASH, 'user' => $user, 'collectible' => $collectible, 'stash' => $stash)));
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

		$collectiblesUser = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('User', 'Merchant', 'Collectible' => array('Currency'))));
		if (isset($collectiblesUser) && !empty($collectiblesUser)) {
			$loggedInUserId = $this -> getUserId();
			if ($loggedInUserId === $collectiblesUser['CollectiblesUser']['user_id']) {

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
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$data['response']['errors'] = array();
			//must be logged in to post comment
			if (!$this -> isLoggedIn()) {
				$error = array('message' => __('You must be logged in to add an item.'));
				$error['inline'] = false;

				array_push($data['response']['errors'], $error);
				$this -> set('removeCollectiblesUsers', $data);
				return;
			}
			if ($this -> request -> is('post') || $this -> request -> is('put')) {
				$id = Sanitize::clean($id);

				if (!is_null($id) && is_numeric($id)) {
					$collectiblesUser = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('User', 'Collectible', 'Stash')));
					if (isset($collectiblesUser) && !empty($collectiblesUser)) {
						$loggedInUserId = $this -> getUserId();
						if ($loggedInUserId === $collectiblesUser['CollectiblesUser']['user_id']) {
							if ($this -> CollectiblesUser -> delete($id)) {
								$data['response']['isSuccess'] = true;
								$this -> set('removeCollectiblesUsers', $data);
								$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$REMOVE_COLLECTIBLE_STASH, 'user' => $this -> getUser(), 'collectible' => $collectiblesUser, 'stash' => $collectiblesUser)));
								return;
							} else {
								$data['response']['isSuccess'] = false;
								array_push($data['response']['errors'], array('message' => __('Invalid request.')));
								$this -> set('removeCollectiblesUsers', $data);
								return;
							}
						} else {
							$data['response']['isSuccess'] = false;
							array_push($data['response']['errors'], array('message' => __('Invalid request.')));
							$this -> set('removeCollectiblesUsers', $data);
							return;
						}
					} else {
						$data['response']['isSuccess'] = false;
						array_push($data['response']['errors'], array('message' => __('Invalid request.')));
						$this -> set('removeCollectiblesUsers', $data);
						return;
					}
				} else {
					$data['response']['isSuccess'] = false;
					array_push($data['response']['errors'], array('message' => __('Invalid request.')));
					$this -> set('removeCollectiblesUsers', $data);
					return;
				}
			} else {
				$data['response']['isSuccess'] = false;
				array_push($data['response']['errors'], array('message' => __('Invalid request.')));
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
							$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$REMOVE_COLLECTIBLE_STASH, 'user' => $this -> getUser(), 'collectible' => $collectiblesUser, 'stash' => $collectiblesUser)));
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

	// I might maintain 2 add functions because they will do different things
	// Quick add will be for when you are adding something without entering any
	// information or you are adding to your wishlist.
	public function quickAdd($id, $type = 'Default') {
		$data = array();
		$data['response'] = array();
		$data['response']['isSuccess'] = false;
		$data['response']['errors'] = array();
		//must be logged in to post comment
		if (!$this -> isLoggedIn()) {
			$error = array('message' => __('You must be logged in to add an item.'));
			$error['inline'] = false;

			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$id = Sanitize::clean($id);
			$type = Sanitize::clean($type);
			$collectiblesUser = array();
			$collectiblesUser['CollectiblesUser']['collectible_id'] = $id;
			$response = $this -> CollectiblesUser -> add($collectiblesUser, $this -> getUser(), $type);

			if ($response) {
				$this -> set('returnData', $response);
			} else {
				//Something really fucked up
				$data['response']['isSuccess'] = false;
				array_push($data['response']['errors'], array('message' => __('Invalid request.')));
				$this -> set('returnData', $data);
			}
		} else {
			$data['response']['isSuccess'] = false;
			array_push($data['response']['errors'], array('message' => __('Invalid request.')));
			$this -> set('returnData', $data);
			return;
		}
	}

	public function registry($id = null) {
		if (!is_null($id) && is_numeric($id)) {
			$collectible = $this -> CollectiblesUser -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $id), 'contain' => false));
			if (!empty($collectible)) {
				$usersWho = $this -> CollectiblesUser -> getListOfUsersWho($id, $collectible['Collectible']['numbered']);

				$wishlist = $this -> CollectiblesUser -> getListOfUserWishlist($id);

				$this -> set('showEditionSize', $collectible['Collectible']['numbered']);
				$this -> set('registry', $usersWho);

				$this -> set('wishlist', $wishlist);
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