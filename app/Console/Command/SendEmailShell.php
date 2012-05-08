<?php
/**
 * This will send out emails, 100 at a time.
 * 
 * This will have to run once a day to start.
 * 
 * 
 */
App::uses('CakeEmail', 'Network/Email');
class SendEmailShell extends AppShell {
	public $uses = array('Email');

	public function main() {
		//Grabbing them all for now
		$emails = $this -> Email -> find("all", array('limit' => 100, 'conditions' => array('sent' => 0)));
		// $this -> out(print_r($notifications, true));

		foreach ($emails as $key => $email) {
			$cakeEmail = new CakeEmail('smtp');
			$cakeEmail -> emailFormat('text');
			$cakeEmail -> template('notification', 'simple');
			$cakeEmail -> to($email['Email']['receiver']);
			$cakeEmail -> subject($email['Email']['subject']);
			$cakeEmail -> viewVars(array('notification' => $email['Email']['body']));
			$cakeEmail -> send();

			$this -> Email -> id = $email['Email']['id'];
			$this -> Email -> saveField('sent', 1, false);

		}

		//This will go in email shell
		// $email = new CakeEmail('smtp');
		// $email -> emailFormat('text');
		// $email -> template('user_confirm', 'simple');
		// $email -> to('tyler.cvetan@gmail.com');
		// $email -> subject(env('SERVER_NAME') . '– Please confirm your email address');
		// $email -> viewVars(array('activate_url' => 'balls', 'username' => 'fuck'));
		// $email -> send();

		//For now we are going to send emails until I get the settings up

	}

}
?>