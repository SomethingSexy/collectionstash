<?php
App::uses('BaseActivity', 'Lib/Activity');
class CommentActivity extends BaseActivity {

	private $user;

	private $comment;

	private $entity;

	public function __construct($data) {
		$this -> user = $data['user']['User'];
		$this -> comment = $data['comment']['Comment'];
		// This should contain both the entity object
		// and the model object that the entity is tied to
		$this -> entity = $data['entity'];
		CakeLog::write('info', 'Comment create');
		parent::__construct();
	}

	public function buildActivityJSON() {
		$retVal = array();
		$retVal['published'] = date('Y-m-d H:i:s');
		// build the actor
		$actorJSON = $this -> buildActor('user', $this -> user);
		$retVal = array_merge($retVal, $actorJSON);

		// build the object we are acting on, in this case it is a comment
		$objectJSON = $this -> buildObject($this -> comment['id'], null, 'comment', array('comment' => $this -> comment['comment']));
		$retVal = array_merge($retVal, $objectJSON);

		// Now add the target
		if ($this -> entity['EntityType']['type'] === 'stash') {
			$targetJSON = $this -> buildTarget($this -> entity['Stash']['id'], '/stash/' . $this -> entity['Stash']['User']['username'], 'stash', $this -> entity['Stash']['User']['username'] . ' \'s stash');
			$retVal = array_merge($retVal, $targetJSON);
		} else if ($this -> entity['EntityType']['type'] === 'collectible') {
			$targetJSON = $this -> buildTarget($this -> entity['Collectible']['id'], '/collectibles/view/' . $this -> entity['Collectible']['id'], 'collectible', $this -> entity['Collectible']['name']);
			$retVal = array_merge($retVal, $targetJSON);
		}
		
		$verbJSON = $this -> buildVerb('add');
		$retVal = array_merge($retVal, $verbJSON);

		return $retVal;
	}

}
?>