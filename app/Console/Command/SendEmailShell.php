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
			$cakeEmail -> emailFormat('html');
			if (!empty($email['Email']['template'])) {
				$cakeEmail -> template($email['Email']['template'], 'fancy');
				$emailData = json_decode($email['Email']['template_json_data'], true);
				$emailData['message'] = $email['Email']['body'];
				$cakeEmail -> viewVars($emailData);
			} else {
				$cakeEmail -> template('notification', 'simple');
				$cakeEmail -> viewVars(array('notification' => $email['Email']['body']));
			}

			$cakeEmail -> to($email['Email']['receiver']);
			$cakeEmail -> subject($email['Email']['subject']);

			$cakeEmail -> send();

			$this -> Email -> id = $email['Email']['id'];
			$this -> Email -> saveField('sent', 1, false);

		}
	}

}
?>