<?php
class ActivitiesController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	public function index() {
		$this -> checkLogIn();
		$this -> checkAdmin();
		$this -> paginate = array('limit' => 25, 'order' => array('Activity.created' => 'desc'));
		$activites = $this -> paginate('Activity');
		debug($activites);
		$this -> set(compact('activites'));
	}

	public function view() {

	}

}
?>
