<?php
App::uses('BaseActivity', 'Lib/Activity');
/**
 * Enhancements: When this eventually gets turned into something on the UI, we will want to expand the edit section so
 * we are better recording what was exactly sumbitted for an edit
 */
class SubmissionActivity extends BaseActivity {

	private $user;

	private $object;

	private $action;

	private $target;

	private $type;

	private $edit;

	private $editType;

	/**
	 * This will handle new collectibles, edits, and admin approvals
	 *
	 * The target will only be used for the approvals I think.
	 *
	 * Otherwise it will be
	 *
	 * User A submits new Collectible -- no target, just object
	 *
	 * User A submits Edit for Collectible -- in this case the Edit will be the object and the Collectible will be the target
	 *
	 * Admin approves new Collectible submitted by User A -- collectible is the object, user is the target
	 *
	 * Admin approves edit for Collectible submitted by User A -- collectible is the object, user is the target
	 */
	public function __construct($action, $data) {
		// Action will either be submit or approve
		// Type will either be new or edit
		$this -> action = $action;
		$this -> type = $data['type'];
		$this -> user = $data['user']['User'];
		if (isset($data['target'])) {
			$this -> target = $data['target']['User'];
		}

		if ($this -> type === 'Collectible') {
			$this -> object = $data['object']['Collectible'];
		}

		if ($this -> type === 'Attribute') {
			$this -> object = $data['object']['Attribute'];
		}

		parent::__construct();
	}

	public function buildActivityJSON() {
		$retVal = array();
		$retVal['published'] = date('Y-m-d H:i:s');
		// build the actor
		$actorJSON = $this -> buildActor('user', $this -> user);
		$retVal = array_merge($retVal, $actorJSON);

		$verbJSON = $this -> buildVerb($this -> action);
		$retVal = array_merge($retVal, $verbJSON);
		if ($this -> type === 'Collectible') {
			$objectJSON = $this -> buildObject($this -> object['id'], 'collectibles/view/' . $this -> object['id'], 'collectible', array('type' => 'new', 'displayName' => $this -> object['name']));
			$retVal = array_merge($retVal, $objectJSON);
		} else if ($this -> type === 'Attribute') {
			$objectJSON = $this -> buildObject($this -> object['id'], 'attributes/view/' . $this -> object['id'], 'attribute', array('type' => 'new', 'displayName' => $this -> object['name']));
			$retVal = array_merge($retVal, $objectJSON);
		}

		if ($this -> action === 'approve') {
			$targetJSON = $this -> buildTarget($this -> target['id'], '/stash/' . $this -> user['username'], 'user', $this -> user['username']);
			$retVal = array_merge($retVal, $targetJSON);
		}

		return $retVal;
	}

}
?>