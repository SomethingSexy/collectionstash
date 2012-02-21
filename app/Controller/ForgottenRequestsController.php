<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
class ForgottenRequestsController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	public function forgotPassword() {
		$this -> processRequest($this -> data, 'forgotPasswordComplete', 'forgotten_password', __('Forgotten Password Request'), 'forgot');
	}

	public function forceResetPassword() {
		$this -> processRequest($this -> data, 'resetPasswordComplete', 'reset_password', __('Reset Password Request'), 'reset');
	}

	private function processRequest($user, $completeView, $template, $subject, $type) {
		if (!empty($user)) {
			$user = Sanitize::clean($user);
			$this -> loadModel('User');

			$this -> User -> set($user);
			/*
			 * Don't really need to check if it is a valid email address, just
			 * need to make sure that it exists already.  We MIGHT want to update
			 * this in the future so that we always assume it sends so people
			 * cannot phish for email address in our system.
			 */
			if ($this -> User -> isValidUserEmail($user)) {
				$forgottenUser = $this -> User -> getUserByEmail($user);
				//Ok now that we have the user, lets create the forgotten entry
				$forgottenModel = array();
				$forgottenModel['ForgottenRequest']['user_id'] = $forgottenUser['User']['id'];
				//Save our new forgotten request
				if ($this -> ForgottenRequest -> saveAll($forgottenModel)) {
					//If it is successful, well then lets shoot off an email
					$forgottenId = $this -> ForgottenRequest -> id;
					$email = new CakeEmail('smtp');
					$email -> emailFormat('text');
					$email -> template($template, 'simple');
					$email -> to(trim($forgottenUser['User']['email']));
					$email -> subject($subject);
					$email -> viewVars(array('url' => 'http://' . env('SERVER_NAME') . '/users/resetPassword/' . $type . '/' . $forgottenId, 'username' => $forgottenUser['User']['username']));
					$email -> send();
					$this -> render($completeView);
				} else {

				}
			} else {
				$this -> User -> validationErrors['email'] = 'No user exists with that email address.';
			}
		}
	}

}
?>