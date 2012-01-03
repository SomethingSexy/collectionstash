<?php
App::uses('Sanitize', 'Utility');
class ForgottenRequestsController extends AppController {

	public $helpers = array('Html', 'Ajax', 'Minify.Minify');
	var $components = array('Email');

	public function forgotPassword() {
		if (!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data);
			$this -> loadModel('User');

			$this -> User -> set($this -> data);
			/*
			 * Don't really need to check if it is a valid email address, just
			 * need to make sure that it exists already.  We MIGHT want to update
			 * this in the future so that we always assume it sends so people
			 * cannot phish for email address in our system.
			 */
			if ($this -> User -> isValidUserEmail($this -> data)) {
				$forgottenUser = $this -> User -> getUserByEmail($this -> data);
				//Ok now that we have the user, lets create the forgotten entry
				$forgottenModel = array();
				$forgottenModel['ForgottenRequest']['user_id'] = $forgottenUser['User']['id'];
				//Save our new forgotten request
				if ($this -> ForgottenRequest -> saveAll($forgottenModel)) {
					//If it is successful, well then lets shoot off an email
					$forgottenId = $this -> ForgottenRequest -> id;
					if($this -> __sendForgottenEmail($forgottenUser, $forgottenId)){
						// $this -> Session -> setFlash(__('An email as been sent with instructions on how to reset your password.', true), null, null, 'success');
						$this -> render('forgotPasswordComplete');
					} else {
						
					}
					
				} else {

				}
			} else {
				$this -> User -> validationErrors['email'] = 'No user exists with that email address.';
			}
		}
	}

	function __sendForgottenEmail($forgottenUser, $forgottenId) {
		// Set data for the "view" of the Email
		$this -> set('forgotten_url', 'http://' . env('SERVER_NAME') . '/users/resetPassword/' . $forgottenId);
		$this -> set('username', $forgottenUser['User']['username']);

		$this -> Email -> smtpOptions = array('port' => Configure::read('Settings.Email.port'), 'timeout' => Configure::read('Settings.Email.timeout'), 'host' => Configure::read('Settings.Email.host'), 'username' => Configure::read('Settings.Email.username'), 'password' => Configure::read('Settings.Email.password'));
		$this -> Email -> delivery = 'smtp';
		$this -> Email -> to = $forgottenUser['User']['email'];
		$this -> Email -> subject = 'Forgotten Password Request';
		$this -> Email -> from = Configure::read('Settings.Email.from');
		$this -> Email -> template = 'forgotten_password';
		$this -> Email -> sendAs = 'text';
		// you probably want to use both :)
		$return = $this -> Email -> send();
		$this -> set('smtp_errors', $this -> Email -> smtpError);
		if (!empty($this -> Email -> smtpError)) {
			$return = false;
			$this -> log('There was an issue sending the email ' . $this -> Email -> smtpError . ' for user ' . $user_id, 'error');
		}

		return $return;
	}

}
?>