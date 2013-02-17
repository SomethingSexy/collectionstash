<?php
class UserPointFact extends AppModel {
	var $name = 'UserPointFact';
	var $actsAs = array('Containable');
	var $belongsTo = array('User');

	public function getUserTotalPointsCurrentYear($userId) {
		$year = date("Y");
		return $this -> getUserTotalPointsYear($userId, $year);
	}

	public function getUserTotalPointsCurrentMonth($userId) {
		$month = date("m");
		$year = date("Y");
		return $this -> getUserTotalPointsMonth($userId, $month, $year);
	}

	public function getCurrentMonthlyLeaders() {
		$month = date("m");
		$year = date("Y");
		return $this -> getMonthlyLeaders($month, $year);
	}

	// Helper functions for common months and years we need to pull back

	public function getUserTotalPointsPreivousMonth($userId) {
		$date = date('M Y', strtotime("last month"));
		$parsedDate = date_parse_from_format('M Y', $date);
		$month = $parsedDate['month'];
		$year = $parsedDate['year'];
		return $this -> getUserTotalPointsMonth($userId, $month, $year);
	}

	public function getPreviousMonthyLeaders() {
		$date = date('M Y', strtotime("last month"));
		$parsedDate = date_parse_from_format('M Y', $date);
		$month = $parsedDate['month'];
		$year = $parsedDate['year'];
		return $this -> getMonthlyLeaders($month, $year);
	}

	public function getYearlyLeaders() {
		$year = date("Y");
		return $this -> getLeadersByYear($year);
	}

	/**
	 * This will return a users total points for the year
	 */
	public function getUserTotalPointsYear($userId, $year) {
		$retVal = 0;
		$points = $this -> find('all', array('conditions' => array('UserPointFact.user_id' => $userId, 'UserPointFact.year' => $year)));

		if (!empty($points)) {
			foreach ($points as $key => $value) {
				$retVal = $retVal + $value['UserPointFact']['points'];
			}
		}

		return $retVal;
	}

	/**
	 * This will return a users total points for the month
	 */
	public function getUserTotalPointsMonth($userId, $month, $year) {
		$retVal = 0;
		$points = $this -> find('first', array('conditions' => array('UserPointFact.user_id' => $userId, 'UserPointFact.month' => $month, 'UserPointFact.year' => $year)));
		if (isset($points['UserPointFact'])) {
			$retVal = $points['UserPointFact']['points'];
		}

		return $retVal;
	}

	/**
	 * This will return the top 5 users for the month
	 */
	public function getMonthlyLeaders($month, $year) {
		$retVal = array();
		$retVal = $this -> find('all', array('limit' => 5, 'order' => array('UserPointFact.points' => 'desc'), 'conditions' => array('UserPointFact.month' => $month, 'UserPointFact.year' => $year)));

		return $retVal;
	}

	/**
	 * This will return the top 5 users for the year
	 */
	public function getLeadersByYear($year) {
		// TODO get to work
		$retVal = $this -> find('all', array('contain'=> false, 'group' => 'UserPointFact.user_id', 'conditions' => array('UserPointFact.year' => '2012')));

		return $retVal;
	}

}
?>
