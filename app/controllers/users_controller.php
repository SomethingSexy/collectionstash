<?php
App::import('Sanitize');
class UsersController extends AppController {
	var $name = 'Users';

	var $helpers = array('Html', 'Form', 'FileUpload.FileUpload');

	var $components = array('Email');

	function login() {
		$message = null;
		$messageType = null;
		if($this -> Session -> check('Message.error')) {
			$message = $this -> Session -> read('Message.error');
			$message = $message['message'];
			$messageType = 'error';
		} else if($this -> Session -> check('Message.success')) {
			$message = $this -> Session -> read('Message.success');
			$message = $message['message'];
			$messageType = 'success';
		}

		$this -> Session -> destroy();
		debug($message);
		$this -> Session -> setFlash($message, null, null, $messageType);
		$success = true;
		if($this -> data) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			$this -> User -> recursive = 0;
			$results = $this -> User -> getUser($this -> data['User']['username']);
			if($results) {
				if($results['User']['status'] == 0) {
					if($results['User']['password'] == Security::hash($this -> data['User']['password'])) {
						$this -> User -> id = $results['User']['id'];
						$this -> User -> saveField('last_login', date("Y-m-d H:i:s", time()));
						$this -> log($results);
						$this -> Session -> write('user', $results);
						$this -> log('User ' . $results['User']['id'] . ' successfully logged in at ' . date("Y-m-d H:i:s", time()), 'info');
						if(!empty($this -> data['User']['fromPage'])) {
							$this -> redirect($this -> data['User']['fromPage'], null, true);
						} else {
							$this -> redirect( array('controller'=>'stashs','action' => 'view', $results['User']['username']), null, true);
						}
					} else {
						$this -> Session -> setFlash(__('Invalid Login.', true), null, null, 'error');
						$success = false;
					}
				} else {
					$this -> Session -> setFlash(__('Your account has not been activated yet.', true), null, null, 'error');
					$success = false;
				}
			} else {
				$this -> Session -> setFlash(__('Invalid Login.', true), null, null, 'error');
				$success = false;
			}
		}

		if(!$success) {
			$this -> data['User']['password'] = '';
			$this -> data['User']['new_password'] = '';
			$this -> data['User']['confirm_password'] = '';
			$this -> log('User ' . $this -> data['User']['username'] . ' failed logging in at ' . date("Y-m-d H:i:s", time()), 'error');
		}

	}

	function logout() {
		$this -> Session -> delete('user');
		$this -> Session -> destroy();

		$this -> redirect('/', null, true);
	}

	function account($view =null) {
		$username = $this -> getUsername();
		$user = $this -> getUser();
		if($user) {
			//Grab the number of collectibles for this user
			$stashCount = $this -> User -> getNumberOfStashesByUser($username);
			$stashDetails = $this -> User -> Stash -> getStashDetails($user['User']['id']);
			$this -> set('stashCount', $stashCount);
			$this -> set('stashDetails', $stashDetails);
			//Grab the number of pending submissions.

			$this -> loadModel('Collectible');
			$submissionCount = $this -> Collectible -> getPendingCollectiblesByUserId($user['User']['id']);
			$this -> set('submissionCount', $submissionCount);

			$this -> paginate = array('conditions' => array('id' => $stashDetails[0]['Stash']['id']), 'limit' => 20, 'contain' => array('CollectiblesUser' => array('Collectible' => array('Manufacture', 'License', 'Collectibletype', 'Upload'))));

			$collectibleCount = $this -> User -> Stash -> getNumberOfCollectiblesInStash($stashDetails[0]['Stash']['id']);
			$this -> set('collectibleCount', $collectibleCount);
			$data = $this -> paginate('Stash');
			$this -> set('myCollectibles', $data);
			debug($data);

			$this -> set('myCollection', true);
			//$this->set('collectibles',$data);
		} else {
			$this -> redirect( array('action' => 'login'), null, true);
		}
	}

	/**
	 * Need to update this so that if the config is invites-only, then we have to check the email address to make
	 * sure that it is one that is in the list.
	 *
	 * Also for helper, take the passed in email and put it in the $this->data
	 */
	function register($email =null) {
		//Make sure the user name is not a list of specific ones...like any controller names :)
		if(Configure::read('Settings.registration.open')) {
			if(!empty($this -> data)) {
				$this -> data = Sanitize::clean($this -> data, array('encode' => false));
				$proceed = true;
				$invitedUser = null;
				//If invite only is turned on, first make sure that this user is invited
				if(Configure::read('Settings.registration.invite-only')) {
					$invitedUser = $this -> User -> Invite -> find("first", array('conditions' => array('Invite.email' => $this -> data['User']['email'])));
					if(empty($invitedUser)) {
						$proceed = false;
						$this -> Session -> setFlash(__('Sorry for the inconvenience, Collection Stash is currently invite only.', true), null, null, 'error');
					}
				}
				if($proceed) {
					$this -> data['User']['password'] = Security::hash($this -> data['User']['new_password']);
					$this -> data['User']['admin'] = 0;
					$this -> data['User']['status'] = 1;
					//$this -> data['User']['profile_id'] = '';
					$this -> data['Profile'] = array();
					//Set the invites to 0, as they invite people we will increase this number
					$this -> data['Profile']['invites'] = 0;
					$this -> data['Stash'] = array();
					$this -> data['Stash']['0'] = array();
					$this -> data['Stash']['0']['name'] = 'Default';
					$this -> data['Stash']['0']['total_count'] = 0;
					debug($this -> data);
					if($this -> User -> saveAll($this -> data)) {
						$newUserId = $this -> User -> id;
						if(Configure::read('Settings.registration.invite-only')) {
							$this -> User -> Invite -> id = $invitedUser['Invite']['id'];
							$this -> User -> Invite -> saveField('registered','1',false);
						}
						$emailResult = $this -> __sendActivationEmail($this -> User -> id);
						if($emailResult) {
							$this -> Session -> setFlash('Your registration information was accepted');
							$this -> render('registrationComplete');
						} else {
							//At this point sending the email failed, so we should roll it all back
							$this -> User -> delete($newUserId);
							$this -> data['User']['password'] = '';
							$this -> data['User']['new_password'] = '';
							$this -> data['User']['confirm_password'] = '';
							$this -> Session -> setFlash(__('There was a problem registering this information.', true), null, null, 'error');
						}
					} else {
						$this -> data['User']['password'] = '';
						$this -> data['User']['new_password'] = '';
						$this -> data['User']['confirm_password'] = '';
						$this -> Session -> setFlash(__('There was a problem registering this information.', true), null, null, 'error');
					}
				}
			} else {
				if($email) {
					$this -> data['User']['email'] = $email;
				}
			}

		} else {
			$this -> redirect( array('action' => 'login'), null, true);
		}
	}

	/**
	 * Activates a user account from an incoming link
	 *
	 *  @param Int $user_id User.id to activate
	 *  @param String $in_hash Incoming Activation Hash from the email
	 */
	function activate($user_id =null, $in_hash =null) {
		$this -> User -> id = $user_id;
		if($this -> User -> exists()) {
			if($this -> User -> field('status') != 0) {
				if($in_hash == $this -> User -> getActivationHash()) {
					// Update the active flag in the database
					$this -> User -> saveField('status', 0);

					// Let the user know they can now log in!
					$this -> Session -> setFlash(__('Your account has been activated, please log in below', true), null, null, 'success');
					$this -> redirect('login');
				} else {
					$this -> set('userId', $user_id);
					$this -> render('activationExpired');
				}
			} else {
				$this -> Session -> setFlash(__('Your account has already been activated!', true), null, null, 'error');
				$this -> redirect('login');
			}
		} else {
			$this -> Session -> setFlash(__('That user does not exist, please register.', true), null, null, 'error');
			$this -> redirect('login');
		}
		// Activation failed, render ‘/views/user/activate.ctp’ which should tell the user.
	}

	function resendActivation($user_id =null) {
		if($user_id) {
			$this -> User -> id = $user_id;
			if($this -> User -> exists()) {
				if($this -> User -> field('status') != 0) {
					$emailResult = $this -> __sendActivationEmail($this -> User -> id);
					if($emailResult) {
						//do nothing
					} else {
						//Do what?
					}
				} else {
					$this -> Session -> setFlash(__('Your account has already been activated!', true), null, null, 'error');
					$this -> redirect('login');
				}
			} else {
				$this -> redirect('login');
			}
		} else {
			$this -> redirect('login');
		}
	}

	/**
	 * Send out an activation email to the user.id specified by $user_id
	 *  @param Int $user_id User to send activation email to
	 *  @return Boolean indicates success
	 */
	function __sendActivationEmail($user_id) {
		$user = $this -> User -> find( array('User.id' => $user_id), array('User.id', 'User.email', 'User.username'), null, false);
		debug($user);
		if($user === false) {
			debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
			return false;
		}

		// Set data for the "view" of the Email
		$this -> set('activate_url', 'http://' . env('SERVER_NAME') . '/users/activate/' . $user['User']['id'] . '/' . $this -> User -> getActivationHash());
		$this -> set('username', $this -> data['User']['username']);

		$this -> Email -> smtpOptions = array(
			'port' => Configure::read('Settings.Email.port'), 
			'timeout' => Configure::read('Settings.Email.timeout'), 
			'host' => Configure::read('Settings.Email.host'), 
			'username' => Configure::read('Settings.Email.username'), 
			'password' => Configure::read('Settings.Email.password')
		);
		$this -> Email -> delivery = 'smtp';
		$this -> Email -> to = $user['User']['email'];
		$this -> Email -> subject = env('SERVER_NAME') . '– Please confirm your email address';
		$this -> Email -> from = Configure::read('Settings.Email.from');
		$this -> Email -> template = 'user_confirm';
		$this -> Email -> sendAs = 'text';
		// you probably want to use both :)
		$return = $this -> Email -> send();
		$this -> set('smtp_errors', $this -> Email -> smtpError);
		if(!empty($this -> Email -> smtpError)) {
			$return = false;
			$this -> log('There was an issue sending the email ' . $this -> Email -> smtpError . ' for user ' . $user_id, 'error');
		}

		return $return;
	}

}
?>
