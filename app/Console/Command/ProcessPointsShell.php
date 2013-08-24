<?php
/**
 * This process will total up all of the user points and set that to the user table
 *
 * This will also process the year totals.
 *
 * This will run once a day
 */
class ProcessPointsShell extends AppShell {
	public $uses = array('User', 'UserPointFact', 'UserPointYearFact');

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

			// Now let's get the total points for each user for that year and populate our table
			$userYearPoints = $this -> UserPointFact -> getUserTotalPointsCurrentYear($user['User']['id']);
			$userYearFact = $this -> getYearFact($user['User']['id'], date("Y"));
			$this -> updateYearFact($userYearFact, $userYearPoints);

		}
	}

	private function getYearFact($userId, $year) {
		$userFact = $this -> UserPointYearFact -> find('first', array('contain' => false, 'conditions' => array('UserPointYearFact.user_id' => $userId, 'UserPointYearFact.year' => $year)));
		if (!$userFact) {
			$saveData = array();
			$saveData['UserPointYearFact'] = array();
			$saveData['UserPointYearFact']['year'] = $year;
			$saveData['UserPointYearFact']['user_id'] = $userId;
			$saveData['UserPointYearFact']['points'] = 0;
			$this -> UserPointYearFact -> create();
			if ($this -> UserPointYearFact -> save($saveData)) {
				$id = $this -> UserPointYearFact -> id;
				$userFact = $this -> UserPointYearFact -> find('first', array('conditions' => array('UserPointYearFact.id' => $id)));
			}
		}

		// We want this to update
		if (isset($userFact['UserPointYearFact']['modified'])) {
			unset($userFact['UserPointYearFact']['modified']);
		}

		return $userFact;
	}

	private function updateYearFact($userFact, $score) {
		// just overwrite this one
		$userFact['UserPointYearFact']['points'] = $score;

		debug($userFact);

		$this -> UserPointYearFact -> save($userFact);
	}

}
?>