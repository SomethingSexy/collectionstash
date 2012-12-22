<?php
/**
 * This process will total up all of the user points and set that to the user table
 *
 * This will run once a day
 */
class ProcessPointsShell extends AppShell {
	public $uses = array('User', 'UserPointFact');

	public function main() {
		// Grab all of the users
		// This should work for now until we get a shit ton of users :)
		$users = $this -> User -> find("all", array('contain' => false));

		// for each user, find all of their point records
		// total them up and then set that value
		foreach ($users as $key => $user) {
			$pointRecords = $this -> UserPointFact -> find('all', array('contain' => false, 'conditions' => array('UserPointFact.user_id' => $user['User']['id'])));
			// make sure we have something
			if (!empty($pointRecords)) {
				// start at zero
				$points = 0;
				foreach ($pointRecords as $key => $pointRecord) {
					$points = $points + $pointRecord['UserPointFact']['points'];
				}

				// If they don't equal then update
				if ($user['User']['points'] != $points) {
					$this -> User -> id = $user['User']['id'];
					$this -> User -> saveField('points', $points, false);
				}

			}

		}
	}

}
?>