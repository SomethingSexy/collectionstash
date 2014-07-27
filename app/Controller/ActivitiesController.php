<?php
class ActivitiesController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	public function index() {
		//Make sure the user is logged in
		$this -> checkLogIn();
		$this -> paginate = array('limit' => 10, 'order' => array('Activity.created' => 'desc'), 'contain' => array('Activity', 'ActivityType', 'User' => array('fields' => array('id', 'username'))));
		$activities = $this -> paginate('Activity');
		$this -> set(compact('activities'));
	}

	public function view() {

	}

}
?>
