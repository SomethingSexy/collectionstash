<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class InvitesController extends AppController {

	public $helpers = array('Html', 'Js', 'FileUpload.FileUpload', 'Minify');

	/**
	 * This will return the invites that the user has already invited
	 *
	 * TODO: If invite is turned off, block this
	 */
	public function view() {
		if ($this -> isLoggedIn()) {
			$user = $this -> getUser();
			//debug($user);
			/*
			 * TODO if admin, they get an unlimited amount of invites
			 */
			$invites = $this -> Invite -> find("all", array('conditions' => array('Invite.user_id' => $user['User']['id']), 'contain' => false));
			debug($invites);

			if ($this -> isUserAdmin()) {
				$allowedInvites = Configure::read('Settings.Profile.total-admin-invites-allowed');
			} else {
				$allowedInvites = Configure::read('Settings.Profile.total-invites-allowed');
			}

			$invitesJson = array();
			if (!empty($invites)) {
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

	/**
	 * This will add an invite for this user
	 *
	 * TODO: If invites are turned off, do not allow this action
	 */
	public function add() {
		if ($this -> isLoggedIn()) {
			//TODO update invite count in session
			if (!empty($this -> request -> data)) {
				$user = $this -> getUser();
				$addCount = $user['User']['invite_count'] + 1;
				if ($this -> isUserAdmin()) {
					$totalAllowed = Configure::read('Settings.Profile.total-admin-invites-allowed');
				} else {
					$totalAllowed = Configure::read('Settings.Profile.total-invites-allowed');
				}

				if ($addCount > $totalAllowed) {
					$this -> set('aInvites', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'errors' => array('0' => array('email' => 'You have reached your max number of invites.'))));
				} else {
					$this -> request -> data = Sanitize::clean($this -> request -> data);
					$this -> request -> data['Invite']['user_id'] = $user['User']['id'];
					$this -> request -> data['Invite']['registered'] = 0;
					//This a good way to ensure no hacking?
					unset($this -> request -> data['Invite']['id']);
					if ($this -> Invite -> save($this -> request -> data)) {
						$count = $user['User']['invite_count'];
						$count = $count + 1;
						$user['User']['invite_count'] = $count;
						if ($this -> __sendInviteEmail($this -> request -> data['Invite']['email'], $user['User']['username'])) {
							$this -> log('Successfully sent invite email to address ' . $this -> request -> data['Invite']['email'] . ' ' . date("Y-m-d H:i:s", time()), 'info');
						} else {
							$this -> log('Failed sending invite email to ' . $this -> request -> data['Invite']['email'] . ' ' . date("Y-m-d H:i:s", time()), 'error');
						}
						$this -> Session -> write('user', $user);
						$this -> set('aInvites', array('success' => array('isSuccess' => true, 'message' => __('You have successfully invited a user.', true))));
						$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$USER_INVITE, 'user' => $user)));
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
		if ($email) {
			$cakeEmail = new CakeEmail('smtp');
			$cakeEmail -> emailFormat('text');
			$cakeEmail -> template('invite', 'simple');
			$cakeEmail -> to($email);
			$cakeEmail -> subject(__('You have been invited to Collection Stash!'));
			$cakeEmail -> viewVars(array('register_url' => 'http://' . env('SERVER_NAME') . '/users/register/' . $email, 'user_name' => $user_name));
			$cakeEmail -> send();
		} else {
			$return = false;
		}

		return $return;
	}

}
?>
