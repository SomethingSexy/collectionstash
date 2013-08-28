<?php
/**
 * This will process notifications and determine what to do with them.
 *
 * We will check the profile of each notification to see if that user is configured to send emails
 *
 * This will run once every hour.
 */
class ProcessNotificationsShell extends AppShell {
	public $uses = array('Notification', 'Email', 'Profile');

	public function main() {
		//Grabbing them all for now
		$notifications = $this -> Notification -> find("all", array('contain' => array('User' => array('Profile')), 'conditions' => array('processed' => 0)));

		foreach ($notifications as $key => $notification) {
			$email = array();
			$processed = false;

			if (isset($notification['User']['Profile']) && $notification['User']['Profile']['email_notification']) {
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
						$processed = true;
					}

				} else {
					// set it equal to true...whatever
					$processed = true;
				}
			} else {
				// if they don't want emails, then set to processed = true
				$processed = true;
			}

			if ($processed) {
				$this -> Notification -> id = $notification['Notification']['id'];
				$this -> Notification -> saveField('processed', 1, false);
			}

		}
	}

}
?>