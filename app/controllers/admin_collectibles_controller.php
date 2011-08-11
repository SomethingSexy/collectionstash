<?php
App::import('Sanitize');
//8.10.11 Update because Approval does not exist anymore
class AdminCollectiblesController extends AppController {

	var $name = 'AdminCollectibles';

	var $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload');

	var $components = array('Email');

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

	public function view($id =null, $variant =null) {
		if($this -> isLoggedIn() && $this -> isUserAdmin()) {

			if($id) {
				//$id = Sanitize::clean($id, array('encode' => false));
				//first make sure it is not null
				if($variant != null) {
					//$variant = Sanitize::clean($variant, array('encode' => false));
					//now see if it is true
					if($variant == 'true') {
						$this -> loadModel('Cvariant');
						$this -> set('collectible', $this -> Cvariant -> read(null, $id));
						$this -> set('variant', 'true');
					} else {
						$this -> loadModel('Collectible');
						$this -> set('collectible', $this -> Collectible -> read(null, $id));
						$this -> set('variant', 'false');
					}
				} else {
					$this -> redirect( array('action' => 'pending'), null, true);
				}
			} else {
				$this -> redirect( array('action' => 'pending'), null, true);
			}

		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	public function approve($id =null, $collectibleid) {
		if($this -> isLoggedIn() && $this -> isUserAdmin()) {
			if($id) {
				//$this->data = Sanitize::clean($this->data, array('encode' => false));
				$this -> loadModel('Approval');
				$approval = $this -> Approval -> read(null, $id);
				if($approval['Approval']['state'] != '0') {
					//set user id who is doing the approving
					$this -> Approval -> set( array('state' => '0', 'date_approved' => date("Y-m-d H:i:s", time()), 'notes' => $this -> data['Approval']['notes'], 'approved_by_user_id' => $this -> getUserId()));
					if($this -> Approval -> save()) {
						$this -> __sendApprovedEmail($approval['Approval']['user_id'], $collectibleid);
						$this -> Session -> setFlash(__('Collectible has been approved.', true));
						$this -> redirect( array('action' => 'pending'), null, true);

					} else {
						$this -> Session -> setFlash(__('There was a problem approving the collectible.', true));
					}
				} else {
					$this -> Session -> setFlash(__('Collectible has already been approved.', true));
					$this -> redirect( array('action' => 'pending'), null, true);
				}
			} else {
				$this -> redirect( array('action' => 'view'), null, true);
			}
		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	public function index() {

		if($this -> isLoggedIn() && $this -> isUserAdmin()) {
			$this -> loadModel('Collectible');
			$collectibleSubCount = $this -> Collectible -> getNumberOfPendingCollectibles();
			$this -> set(compact('collectibleSubCount'));
		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	function __sendApprovedEmail($user_id, $collectibleId) {
		$this -> loadModel('User');
		$user = $this -> User -> find( array('User.id' => $user_id), array('User.id', 'User.email', 'User.username'), null, false);
		debug($user);
		if($user === false) {
			debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
			return false;
		}
		$this -> loadModel('Collectible');
		$collectibleName = $this -> Collectible -> getCollectibleNameById($collectibleId);
		debug($collectibleName);
		// Set data for the "view" of the Email
		$this -> set('collectibleName', $collectibleName);
		$this -> set('username', $user['User']['username']);

		$this -> Email -> smtpOptions = array('port' => '25', 'timeout' => '30', 'host' => 'smtpout.secureserver.net', 'username' => 'admin@collectionstash.com', 'password' => 'oblivion1968',
		// 'client' => 'smtp_helo_hostname'
		);
		$this -> Email -> delivery = 'smtp';
		$this -> Email -> to = $user['User']['email'];
		$this -> Email -> subject = 'Your collectible has been approved!';
		$this -> Email -> from = 'admin@collectionstash.com';
		$this -> Email -> template = 'collectible_approved';
		$this -> Email -> sendAs = 'text';
		// you probably want to use both :)
		$return = $this -> Email -> send();
		$this -> set('smtp_errors', $this -> Email -> smtpError);
		debug($this -> Email -> smtpError);
		return $return;
	}

}
?>