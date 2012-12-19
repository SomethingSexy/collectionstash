<?php
/**
 * This will process notifications and determine what to do with them.
 *
 * Right now it will convert them over to emails.
 *
 * This will run once every hour.
 */
class ProcessActivitiesShell extends AppShell {
	public $uses = array('Activity', 'UserPointsFact', 'Point');

	public function main() {
		// This grabs yesterdayd date
		//array('Post.read_count BETWEEN ? AND ?' => array(1,10))

		// Supposedly this is the slowest but I am not too worried about it for now
		$start = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
		$end = date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("d") - 1, date("Y")));

		$activities = $this -> Activity -> find('all', array('conditions' => array('Activity.created BETWEEN ? AND ?' => array($start, $end))));

		$points = $this -> Point -> find('list', array('fields' => array('Point.activity_type_id', 'Point.points')));

		debug($points);

		foreach ($activities as $key => $activity) {
			$score = $points[$activity['ActivityType']['id']];	
			if()
		}
	}

}
?>