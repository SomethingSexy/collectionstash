<?php
App::import('Sanitize');
class CollectiblesUserController extends AppController {

	var $name = 'CollectiblesUser';
	var $helpers = array('Html', 'Form', 'Ajax', 'FileUpload.FileUpload');
	var $components = array('RequestHandler');

	public function view($id = null) {
		if($id == null) {
			$this -> Session -> setFlash(__('Invalid collectible', true), null, null, 'error');
			$this -> redirect(array('action' => 'index'));
		}
		$this -> loadModel('CollectiblesUser');
		$collectible = $this -> CollectiblesUser -> getCollectibleDetail($id);
		debug($collectible);
		$this -> set('collectible', $collectible);
		$this -> loadModel('Collectible');
		$count = $this -> Collectible -> getNumberofCollectiblesInStash($collectible['Collectible']['id']);
		$this -> set('collectibleCount', $count);

	}

	public function add($id = null) {
		$this -> checkLogIn();
		if(!empty($this -> data)) {
			$this -> data['CollectiblesUser']['user_id'] = $this -> getUserId();

			$collectible_id = $this -> data['CollectiblesUser']['collectible_id'];
			debug($this -> data);
			$this -> loadModel('Collectible');
			$this -> Collectible -> recursive = -1;
			$collectible = $this -> Collectible -> findById($collectible_id);
			debug($collectible);
			//Save as a different name so the saveAll doesn't accidently save it
			$this -> data['TempCollectible'] = $collectible['Collectible'];

			debug($this -> data);
			if($this -> CollectiblesUser -> save($this -> data)) {
				$collectibleUserId = $this -> CollectiblesUser -> id;
				$this -> redirect(array('action' => 'view', $collectibleUserId), "null", true);
				$this -> Session -> setFlash(__('Collectible was successfully added.', true), null, null, 'success');
			} else {
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			}
		} else {
			if(!is_null($id) && is_numeric($id)) {
				$this -> data['CollectiblesUser']['collectible_id'] = $id;

			} else {
				$this -> Session -> setFlash(__('Invalid collectible', true));
				$this -> redirect($this -> referer());
			}
		}
	}

	function edit($id = null) {
		$this -> checkLogIn();
		$this -> loadModel('User');
		debug($this -> data);
		debug($id);
		if(!empty($this -> data)) {
			$fieldList = array('edition_size', 'cost', 'condition_id', 'merchant_id');
			if($this -> User -> CollectiblesUser -> save($this -> data, true, $fieldList)) {
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
		if(!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect(array('action' => 'index'));
		} else {
			$username = $this -> getUsername();
			if($username) {
				$this -> loadModel('User');
				if($this -> User -> CollectiblesUser -> delete($id, false)) {
					$this -> Session -> setFlash(__('Collectible was successfully removed.', true), null, null, 'success');
					//WHERE DO I GO BACK TO?
					$this -> redirect(array('controller' => 'stashs', 'action' => 'index'), null, true);
				}
			} else {
				$this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
			}
		}
	}
	
	public function registry($id =null) {
		if($this -> isLoggedIn()) {
			if($id) {
				$this -> loadModel('CollectiblesUser');
				$usersWho = $this -> CollectiblesUser -> getListOfUsersWho($id);
				debug($usersWho);
				$this -> set('usersWho', $usersWho);
			}

		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

}
?>