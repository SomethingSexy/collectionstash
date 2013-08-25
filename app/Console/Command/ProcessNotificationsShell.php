<?php
/**
 * This will process notifications and determine what to do with them.
 *
 * Right now it will convert them over to emails.
 *
 * This will run once every hour.
 */
class ProcessNotificationsShell extends AppShell {
	public $uses = array('Notification', 'Email');

	public function main() {
		//Grabbing them all for now
		$notifications = $this -> Notification -> find("all", array('conditions' => array('processed' => 0)));
		// $this -> out(print_r($notifications, true));

		foreach ($notifications as $key => $notification) {
			$email = array();
			// if we don't have an email well we cannot send it
			if (!is_null($notification['User']['email'])) {
				$email['Email']['receiver'] = $notification['User']['email'];
				if ($notification['Notification']['subject']) {
					$email['Email']['subject'] = __('Collection Stash - ') . $notification['Notification']['subject'];
				} else {
					$email['Email']['subject'] = __('You have a notification from Collection Stash!');
				}

				$email['Email']['body'] = $notification['Notification']['message'];
				$this -> Email -> create();
				if ($this -> Email -> saveAll($email)) {
					$this -> Notification -> id = $notification['Notification']['id'];
					$this -> Notification -> saveField('processed', 1, false);
				}
			}

		}
	}

}
?>