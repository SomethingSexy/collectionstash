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

	function edit($id = null) {
		$this -> checkLogIn();
		$this -> loadModel('User');
		debug($this -> data);
		debug($id);
		if (!empty($this -> data)) {
			$fieldList = array('edition_size', 'cost', 'condition_id', 'merchant_id');
			if ($this -> User -> CollectiblesUser -> save($this -> data, true, $fieldList)) {
				$this -> Session -> setFlash(__('Collectible was successfully updated.', true), null, null, 'success');
				$this -> redirect(array('controller' => 'stashs', 'action' => 'index'), null, true);
			} else {
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			}
		} else {
			$username = $this -> getUsername();
			$joinRecords = $this -> User -> getCollectibleByUser($username, $id);
			$this -> loadModel('Condition');
			$this -> set('conditions', $this -> Condition -> find('list', array('order' => 'name')));
			$this -> loadModel('Merchant');
			$this -> set('merchants', $this -> Merchant -> find('list', array('order' => 'name')));
			$this -> set('collectible', $joinRecords);
		}
	}

	function remove($id = null) {
		$this -> checkLogIn();
		if (!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect(array('action' => 'index'));
		} else {
			$username = $this -> getUsername();
			if ($username) {
				$this -> loadModel('User');
				if ($this -> User -> CollectiblesUser -> delete($id, false)) {
					$this -> Session -> setFlash(__('Collectible was successfully removed.', true), null, null, 'success');
					//WHERE DO I GO BACK TO?
					$this -> redirect(array('controller' => 'stashs', 'action' => 'index'), null, true);
				}
			} else {
				$this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
			}
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