<?php
App::uses('CakeEventListener', 'Event');
App::uses('CommentActivity', 'Lib/Activity');
App::uses('StashActivity', 'Lib/Activity');
App::uses('PhotoActivity', 'Lib/Activity');
App::uses('EditActivity', 'Lib/Activity');
App::uses('SubmissionActivity', 'Lib/Activity');
App::uses('InviteActivity', 'Lib/Activity');
App::uses('WishListActivity', 'Lib/Activity');
App::uses('Activity', 'Model');
class ActivityEventListener implements CakeEventListener {
	/**
	 * Broken the two out just because they might get processed in two different layers
	 */
	public function implementedEvents() {
		return array('Controller.Activity.add' => 'processActivity', 'Model.Activity.add' => 'processActivity');
	}

	public function processModelActivity($event) {

	}

	public function processActivity($event) {
		// Grab the type of activity we are performing
		$activityType = $event -> data['activityType'];
		// This will always need to be attached for now
		$user = $event -> data['user'];

		// Give the event type, get the activity
		$activity = $this -> getActivity($activityType, $event -> data);

		if (!is_null($activity)) {
			// Once we have the activity object, then we are going to
			// build the JSON and then save it to the Activity model
			// then we are all done!

			$json = $activity -> buildActivityJSON();

			$activityData = array();

			$activityData['Activity'] = array();
			$activityData['Activity']['user_id'] = $user['User']['id'];
			$activityData['Activity']['activity_type_id'] = $activityType;
			$activityData['Activity']['data'] = json_encode($json);

			$activityModel = new Activity();
			$activityModel -> create();
			$activityModel -> saveAll($activityData);
		}
	}

	private function getActivity($type, $data) {
		$retVal = null;

		switch ($type) {
			case 1 :
				$retVal = new CommentActivity($data);
				break;
			case 2 :
				$retVal = new StashActivity('add', $data);
				break;
			case 3 :
				$retVal = new StashActivity('remove', $data);
				break;
			case 4 :
				$retVal = new StashActivity('edit', $data);
				break;
			case 5 :
				$retVal = new PhotoActivity('add', $data);
				break;
			case 6 :
				$retVal = new SubmissionActivity('submit', $data);
				break;
			case 7 :
				$retVal = new EditActivity('submit', $data);
				break;
			case 8 :
				$retVal = new SubmissionActivity('approve', $data);
				break;
			case 9 :
				$retVal = new EditActivity('approve', $data);
				break;
			case 10 :
				$retVal = new InviteActivity($data);
				break;
			case 11 :
				$retVal = new SubmissionActivity('add', $data);
				break;
			case 12 :
				$retVal = new EditActivity('edit', $data);
				break;
			case 13 :
				$retVal = new SubmissionActivity('add', $data);
				break;
			case 14 :
				$retVal = new WishListActivity('add', $data);
				break;
			case 15 :
				$retVal = new WishListActivity('remove', $data);
				break;
		}
		return $retVal;
	}

}
?>