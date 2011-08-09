<?php
App::import('Sanitize');
class InvitesController extends AppController {

	var $name = 'Invites';
	var $helpers = array('Html', 'Ajax', 'FileUpload.FileUpload');
	var $components = array('RequestHandler');

	public function view() {
		if($this -> isLoggedIn()) {
			if($this -> RequestHandler -> isAjax()) {
				Configure::write('debug', 0);
			}
			$user = $this -> getUser();
			//debug($user);
			/*
			 * TODO if admin, they get an unlimited amount of invites
			 */
			$invites = $this -> Invite -> find("all", array('conditions' => array('Invite.user_id' => $user['User']['id']), 'contain' => false));
			debug($invites);
			$allowedInvites = Configure::read('Settings.Profile.total-invites-allowed');
			$invitesJson = array();
			if(!empty($invites)) {
				$invitesJson['Invites'] = $invites;
			} else {
				$invitesJson['Invites'] = array();
			}

			$userInvites = $user['User']['invite_count'];
			$invitesLeft = $allowedInvites - $userInvites;
			$invitesJson['remaining'] = $invitesLeft;
			//debug($invitesJson);
			$this -> set('aInvites', array('success' => array('isSuccess' => true), 'isTimeOut' => false, 'responseData' => $invitesJson));
		} else {
			$this -> set('aInvites', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
		}

	}

	public function add() {
		if($this -> isLoggedIn()) {
			//TODO update invite count in session
			if(!empty($this -> data)) {
				if($this -> RequestHandler -> isAjax()) {
					Configure::write('debug', 0);
					//$this->render('../json/add');
				}
				/*
				 * TODO if admin, they get an unlimited amount of invites
				 * Now I need to send email!
				 */
				$user = $this -> getUser();
				$addCount = $user['User']['invite_count'] + 1;
				if($addCount > Configure::read('Settings.Profile.total-invites-allowed')) {
					$this -> set('aInvites', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'errors' => array('0' => array('email' => 'You have reached your max number of invites.'))));
				} else {
					$this -> data = Sanitize::clean($this -> data);
					$this -> data['Invite']['user_id'] = $user['User']['id'];
					$this -> data['Invite']['registered'] = 0;
					//This a good way to ensure no hacking?
					unset($this -> data['Invite']['id']);
					if($this -> Invite -> save($this -> data)) {
						$count = $user['User']['invite_count'];
						$count = $count + 1;
						$user['User']['invite_count'] = $count;
						$this -> Session -> write('user', $user);
						$this -> set('aInvites', array('success' => array('isSuccess' => true, 'message' => __('You have successfully invited a user.', true))));
					} else {
						$this -> set('aInvites', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'errors' => array($this -> Invite -> validationErrors)));
					}
				}

			} else {

			}
		} else {
			$this -> set('aInvites', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
		}
	}

}
?>
