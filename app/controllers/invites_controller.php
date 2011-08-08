<?php
App::import('Sanitize');
class InvitesController extends AppController {

	var $name = 'Invites';
	var $helpers = array('Html', 'Ajax', 'FileUpload.FileUpload');

	public function view() {
		$this -> checkLogIn();
		$user = $this -> getUser();
		$invites = $this -> Invite -> find("all", array('conditions' => array('Invite.user_id' => $user['User']['id'])));
		debug($invites);
		$allowedInvites = Configure::read('Settings.Profile.total-invites-allowed');
		$userInvites = $user['User']['invite_count'];
		$invitesLeft = $allowedInvites - $userInvites;
		$this -> set(compact('invites'));
		$this -> set('invitesLeft', $invitesLeft);
		$this -> set('allowInvites', Configure::read('Settings.Profile.allow-invites'));
	}

	public function add() {
		$this -> checkLogIn();
		//TODO update invite count in session
		if(!empty($this -> data)) {
			if($this -> RequestHandler -> isAjax()) {
				Configure::write('debug', 0);
				//$this->render('../json/add');

			}
		}
	}

}
?>
