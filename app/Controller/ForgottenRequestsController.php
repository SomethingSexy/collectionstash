<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
class ForgottenRequestsController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

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
					$email = new CakeEmail('smtp');
					$email -> emailFormat('text');
					$email -> template('forgotten_password', 'simple');
					$email -> to($forgottenUser['User']['email']);
					$email -> subject('Forgotten Password Request');
					$email -> viewVars(array('forgotten_url' => 'http://' . env('SERVER_NAME') . '/users/resetPassword/' . $forgottenId, 'username' => $forgottenUser['User']['username']));
					$email -> send();
					$this -> render('forgotPasswordComplete');
				} else {

				}
			} else {
				$this -> User -> validationErrors['email'] = 'No user exists with that email address.';
			}
		}
	}
}
?>