<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
//8.10.11 Update because Approval does not exist anymore
class AdminCollectiblesController extends AppController {
	public $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload', 'Minify.Minify');
	//var $uses = array();

	//TODO this needs to be updated to use the new approval stuff
	public function pending() {
		//8.10.11 Update because Approval does not exist anymore
		// if ($this->isLoggedIn() && $this->isUserAdmin())
		// {
		// $this->loadModel('Approval');
		//
		// $this->paginate = array(
		// "conditions"=> array('Approval.state'=> 1),
		// "contain"=>array ('User','Collectible')
		// );
		//
		// $collectilbes = $this->paginate('Approval');
		// debug($collectilbes);
		// $this->set('collectibles',$collectilbes);
		// }
		// else
		// {
		// $this->redirect(array('controller'=>'users','action' => 'login'), null, true);
		// }
	}

	public function view($id = null, $variant = null) {
		if ($this -> isLoggedIn() && $this -> isUserAdmin()) {

			if ($id) {
				//$id = Sanitize::clean($id, array('encode' => false));
				//first make sure it is not null
				if ($variant != null) {
					//$variant = Sanitize::clean($variant, array('encode' => false));
					//now see if it is true
					if ($variant == 'true') {
						$this -> loadModel('Cvariant');
						$this -> set('collectible', $this -> Cvariant -> read(null, $id));
						$this -> set('variant', 'true');
					} else {
						$this -> loadModel('Collectible');
						$this -> set('collectible', $this -> Collectible -> read(null, $id));
						$this -> set('variant', 'false');
					}
				} else {
					$this -> redirect(array('action' => 'pending'), null, true);
				}
			} else {
				$this -> redirect(array('action' => 'pending'), null, true);
			}

		} else {
			$this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	public function approve($id = null, $collectibleid) {
		if ($this -> isLoggedIn() && $this -> isUserAdmin()) {
			if ($id) {
				//$this->data = Sanitize::clean($this->data, array('encode' => false));
				$this -> loadModel('Approval');
				$approval = $this -> Approval -> read(null, $id);
				if ($approval['Approval']['state'] != '0') {
					//set user id who is doing the approving
					$this -> Approval -> set(array('state' => '0', 'date_approved' => date("Y-m-d H:i:s", time()), 'notes' => $this -> request -> data['Approval']['notes'], 'approved_by_user_id' => $this -> getUserId()));
					if ($this -> Approval -> save()) {
						$this -> loadModel('User');
						$user = $this -> User -> find(array('User.id' => $approval['Approval']['user_id']), array('User.id', 'User.email', 'User.username'), null, false);

						$this -> loadModel('Collectible');
						$collectibleName = $this -> Collectible -> getCollectibleNameById($collectibleId);

						$email = new CakeEmail('smtp');
						$email -> emailFormat('text');
						$email -> template('collectible_approved', 'simple');
						$email -> to($user['User']['email']);
						$email -> subject('Your collectible has been approved!');
						$email -> viewVars(array('collectibleName' => $collectibleName, 'username' => $user['User']['username']));
						$email -> send();
						$this -> Session -> setFlash(__('Collectible has been approved.', true));
						$this -> redirect(array('action' => 'pending'), null, true);

					} else {
						$this -> Session -> setFlash(__('There was a problem approving the collectible.', true));
					}
				} else {
					$this -> Session -> setFlash(__('Collectible has already been approved.', true));
					$this -> redirect(array('action' => 'pending'), null, true);
				}
			} else {
				$this -> redirect(array('action' => 'view'), null, true);
			}
		} else {
			$this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	public function index() {

		if ($this -> isLoggedIn() && $this -> isUserAdmin()) {
			$this -> loadModel('Collectible');
			$collectibleSubCount = $this -> Collectible -> getNumberOfPendingCollectibles();
			$this -> set(compact('collectibleSubCount'));
		} else {
			$this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

}
?>