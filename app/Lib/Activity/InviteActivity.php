<?php
App::uses('BaseActivity', 'Lib/Activity');
class InviteActivity extends BaseActivity {

	private $user;

	private $comment;

	private $entity;

	public function __construct($data) {
		$this -> user = $data['user']['User'];
		CakeLog::write('info', 'Comment create');
		parent::__construct();
	}

	public function buildActivityJSON() {
		$retVal = array();
		$retVal['published'] = date('Y-m-d H:i:s');
		// build the actor
		$actorJSON = $this -> buildActor('user', $this -> user);
		$retVal = array_merge($retVal, $actorJSON);
		
		$verbJSON = $this -> buildVerb('invite');
		$retVal = array_merge($retVal, $verbJSON);

		return $retVal;
	}

}
?>