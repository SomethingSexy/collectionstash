<?php
App::import('Sanitize');
class InvitesController extends AppController {

	var $name = 'Invites';
	var $helpers = array('Html', 'Ajax', 'FileUpload.FileUpload');
	var $components = array('RequestHandler', 'Email');

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

			if($this -> isUserAdmin()) {
				$allowedInvites = Configure::read('Settings.Profile.total-admin-invites-allowed');
			} else {
				$allowedInvites = Configure::read('Settings.Profile.total-invites-allowed');
			}

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
				$user = $this -> getUser();
				$addCount = $user['User']['invite_count'] + 1;
				if($this -> isUserAdmin()) {
					$totalAllowed = Configure::read('Settings.Profile.total-admin-invites-allowed');
				} else {
					$totalAllowed = Configure::read('Settings.Profile.total-invites-allowed');
				}

				if($addCount > $totalAllowed) {
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
						if($this -> __sendInviteEmail($this -> data['Invite']['email'], $user['User']['username'])) {
							$this -> log('Successfully sent invite email to address ' . $this -> data['Invite']['email'] . ' ' . date("Y-m-d H:i:s", time()), 'info');
						} else {
							$this -> log('Failed sending invite email to ' . $this -> data['Invite']['email'] . ' ' . date("Y-m-d H:i:s", time()), 'error');
						}
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

	function __sendInviteEmail($email = null, $user_name = null) {
		$return = true;
		if($email) {
			// Set data for the "view" of the Email
			$this -> set('register_url', 'http://' . env('SERVER_NAME') . '/users/register/' . $email);
			$this -> set('user_name', $user_name);
			//$this -> set('username', $this -> data['User']['username']);

			$this -> Email -> smtpOptions = array(
				'port' => Configure::read('Settings.Email.port'), 
				'timeout' => Configure::read('Settings.Email.timeout'), 
				'host' => Configure::read('Settings.Email.host'), 
				'username' => Configure::read('Settings.Email.username'), 
				'password' => Configure::read('Settings.Email.password')
			);
			$this -> Email -> delivery = 'smtp';
			$this -> Email -> to = $email;
			$this -> Email -> subject = 'You have been invited to Collection Stash!';
			$this -> Email -> from = Configure::read('Settings.Email.username');
			$this -> Email -> template = 'invite';
			$this -> Email -> sendAs = 'text';
			// you probably want to use both :)
			$return = $this -> Email -> send();
			$this -> set('smtp_errors', $this -> Email -> smtpError);
			if(!empty($this -> Email -> smtpError)) {
				$return = false;
				$this -> log('There was an issue sending the email ' . $this -> Email -> smtpError . ' for user ' . $user_id, 'error');
			}
		} else {
			$return = false;
		}

		return $return;
	}

}
?>
