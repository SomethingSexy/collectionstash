<?php
/**
 * This will process notifications and determine what to do with them.
 *
 * Right now it will convert them over to emails.
 *
 * This will run once every hour.
 */
class ProcessActivitiesShell extends AppShell {
	public $uses = array('Activity', 'UserPointFact', 'Point');

	public function main() {
		// This grabs yesterdayd date
		//array('Post.read_count BETWEEN ? AND ?' => array(1,10))

		// This will have the numeric representation of the month
		$month = date("m");
		$day = date("d") - 1;
		// This will have the year string
		$year = date("Y");

		// Supposedly this is the slowest but I am not too worried about it for now
		$start = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, $day, $year));
		$end = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $day, $year));

		$activities = $this -> Activity -> find('all', array('conditions' => array('Activity.created BETWEEN ? AND ?' => array($start, $end))));

		$points = $this -> Point -> find('list', array('fields' => array('Point.activity_type_id', 'Point.points')));

		debug($activities);

		foreach ($activities as $key => $activity) {
			$score = $points[$activity['ActivityType']['id']];
			if ($score !== '0') {
				$data = $activity['Activity']['data'];

				$saveData = array();
				$saveData['UserPointFact'] = array();
				$saveData['UserPointFact']['month'] = $month;
				$saveData['UserPointFact']['year'] = $year;
				
				// Figure out the id of the user we are adding the score for
				// Add comment
				if ($activity['ActivityType']['id'] === '1') {
					// user id
					$userId = $data -> actor -> id;
					$saveData['UserPointFact']['user_id'] = $userId;
				}
				// Add to Stash
				else if ($activity['ActivityType']['id'] === '2') {
					// user id
					$userId = $data -> actor -> id;
					$saveData['UserPointFact']['user_id'] = $userId;
				}
				// Remove From Stash
				else if ($activity['ActivityType']['id'] === '3') {
					// user id
					$userId = $data -> actor -> id;
					$saveData['UserPointFact']['user_id'] = $userId;
				}
				//Edit User Collectible
				else if ($activity['ActivityType']['id'] === '4') {
					// user id
					$userId = $data -> actor -> id;
					$saveData['UserPointFact']['user_id'] = $userId;
				}
				// Add Photo
				else if ($activity['ActivityType']['id'] === '5') {
					// user id
					$userId = $data -> actor -> id;
					$saveData['UserPointFact']['user_id'] = $userId;
				}
				// Approve New
				else if ($activity['ActivityType']['id'] === '8') {
					// user id
					$userId = $data -> target -> id;
					$saveData['UserPointFact']['user_id'] = $userId;
				}
				// Approve Edit
				else if ($activity['ActivityType']['id'] === '9') {
					// user id
					$userId = $data -> target -> id;
					$saveData['UserPointFact']['user_id'] = $userId;
				}
				// invite
				else if ($activity['ActivityType']['id'] === '10') {
					// user id
					$userId = $data -> actor -> id;
					$saveData['UserPointFact']['user_id'] = $userId;
				}

				// Now that we have a score and who to add it to, let's see if we have something
				// added already

				$userFact = $this -> getFact($saveData['UserPointFact']['user_id'], $month, $year);
				
				$this -> updateFact($userFact, $score);

			}
		}
	}

	private function getUser($id) {
		return $this -> User -> find('first', array('contain' => false, 'conditions' => array('User.id' => $id)));
	}

	private function getFact($userId, $month, $year) {
		$userFact = $this -> UserPointFact -> find('first', array('contain' => false, 'conditions' => array('UserPointFact.user_id' => $userId, 'UserPointFact.month' => $month, 'UserPointFact.year' => $year)));
		if (!$userFact) {
			$saveData = array();
			$saveData['UserPointFact'] = array();
			$saveData['UserPointFact']['month'] = $month;
			$saveData['UserPointFact']['year'] = $year;
			$saveData['UserPointFact']['user_id'] = $userId;
			$saveData['UserPointFact']['points'] = 0;
			$this -> UserPointFact -> create();
			if ($this -> UserPointFact -> save($saveData)) {
				$id = $this -> UserPointFact -> id;
				$userFact = $this -> UserPointFact -> find('first', array('conditions' => array('UserPointFact.id' => $id)));
			}
		}

		return $userFact;
	}

	private function updateFact($userFact, $score) {
		// Since we are updating everyone one at a time and not in batch, I should be able to take the current
		// one passed in, add the score and then save...the next time through we will be retrieving the
		// fact again so that we can update it
		$userFact['UserPointFact']['points'] = $userFact['UserPointFact']['points']  + $score;
		
		$this -> UserPointFact -> save($userFact);
	}

}
?>