<?php
App::uses('BaseActivity', 'Lib/Activity');
class StashActivity extends BaseActivity {

	private $user;

	private $collectible;

	private $stash;

	private $action;

	/**
	 * Type will be the action, add or remove
	 */
	public function __construct($action, $data) {
		debug($data);
		$this -> action = $action;
		$this -> user = $data['user']['User'];
		$this -> collectible = $data['collectible'];
		// This should contain both the entity object
		// and the model object that the entity is tied to
		$this -> stash = $data['stash'];
		parent::__construct();
	}

	public function buildActivityJSON() {
		$retVal = array();
		$retVal['published'] = date('Y-m-d H:i:s');
		// build the actor
		$actorJSON = $this -> buildActor('user', $this -> user);
		$retVal = array_merge($retVal, $actorJSON);

		// build the object we are acting on, in this case it is a comment
		// This should handle customs and originals by passing the $collectible data to the object
		// then when we go to render we can determine if it is a custom or original 
		$objectJSON = $this -> buildObject($this -> collectible['Collectible']['id'], 'collectibles/view/' . $this -> collectible['Collectible']['id'], 'collectible', $this -> collectible);
		$retVal = array_merge($retVal, $objectJSON);

		$stashDisplayname = 'Stash';
		$stashUrl = '/stash/' . $this -> stash['User']['username'];

		if ($this -> stash['Stash']['name'] === 'Wishlist') {
			$stashDisplayname = 'Wishlist';
			$stashUrl = '/wishlist/' . $this -> stash['User']['username'];
		}

		// Now add the target
		$targetJSON = $this -> buildTarget($this -> stash['Stash']['id'], $stashUrl, 'stash', $stashDisplayname);
		$retVal = array_merge($retVal, $targetJSON);

		$verbJSON = $this -> buildVerb($this -> action);
		$retVal = array_merge($retVal, $verbJSON);

		return $retVal;
	}

}
?>