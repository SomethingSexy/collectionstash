<?php
App::uses('BaseActivity', 'Lib/Activity');
/**
 * This will handle all submissions of new collectibles and attributes and their approvals
 *
 * This will also handle the live updating of collectibles, attributes and their associated data.
 *
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
	 *
	 * This will also handle directly adding new stuff
	 *
	 * User A adds new AttributesCollectible - object = attribute, target = collectible
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
			$this -> object = $data['object'];
		} else if ($this -> type === 'Attribute') {
			$this -> object = $data['object']['Attribute'];
		} else {
			$this -> object = $data['object'];
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
			$objectJSON = $this -> buildObject($this -> object['Collectible']['id'], '/collectibles/view/' . $this -> object['Collectible']['id'] . '/' . $this -> object['Collectible']['slugField'], 'collectible', $this -> object);
			$retVal = array_merge($retVal, $objectJSON);
		} else if ($this -> type === 'Attribute') {
			$objectJSON = $this -> buildObject($this -> object['id'], '/attributes/view/' . $this -> object['id'], 'attribute', array('type' => 'new', 'displayName' => $this -> object['name']));
			$retVal = array_merge($retVal, $objectJSON);
		} else if ($this -> type === 'AttributesCollectible') {
			// remove this if it has it
			unset($this -> object['Attribute']['AttributesCollectible']);
			$objectJSON = $this -> buildObject($this -> object['Attribute']['id'], '/attributes/view/' . $this -> object['Attribute']['id'], 'attribute', $this -> object);
			$retVal = array_merge($retVal, $objectJSON);

			$targetJSON = $this -> buildTarget($this -> object['Collectible']['id'], '/collectibles/view/' . $this -> object['Collectible']['id'], 'collectible', $this -> object['Collectible']['name']);
			$retVal = array_merge($retVal, $targetJSON);
		} else if ($this -> type === 'CollectiblesTag') {
			$objectJSON = $this -> buildObject($this -> object['CollectiblesTag']['id'], null, 'tag', $this -> object);
			$retVal = array_merge($retVal, $objectJSON);

			$targetJSON = $this -> buildTarget($this -> object['Collectible']['id'], '/collectibles/view/' . $this -> object['Collectible']['id'], 'collectible', $this -> object['Collectible']['name']);
			$retVal = array_merge($retVal, $targetJSON);
		} else if ($this -> type === 'CollectiblesUpload') {
			$objectJSON = $this -> buildObject($this -> object['CollectiblesUpload']['id'], '/files/' . $this -> object['Upload']['name'], 'photo', $this -> object);
			$retVal = array_merge($retVal, $objectJSON);

			$targetJSON = $this -> buildTarget($this -> object['Collectible']['id'], '/collectibles/view/' . $this -> object['Collectible']['id'], 'collectible', $this -> object['Collectible']['name']);
			$retVal = array_merge($retVal, $targetJSON);
		} else if ($this -> type === 'ArtistsCollectible') {
			$objectJSON = $this -> buildObject($this -> object['ArtistsCollectible']['id'], '/artist/' . $this -> object['ArtistsCollectible']['id'] . '/' . $this -> object['ArtistsCollectible']['slug'], 'artist', $this -> object);
			$retVal = array_merge($retVal, $objectJSON);

			$targetJSON = $this -> buildTarget($this -> object['Collectible']['id'], '/collectibles/view/' . $this -> object['Collectible']['id'], 'collectible', $this -> object['Collectible']['name']);
			$retVal = array_merge($retVal, $targetJSON);
		} else if ($this -> type === 'AttributesUpload') {
			$objectJSON = $this -> buildObject($this -> object['AttributesUpload']['id'], '/files/' . $this -> object['Upload']['name'], 'photo', $this -> object);
			$retVal = array_merge($retVal, $objectJSON);

			$targetJSON = $this -> buildTarget($this -> object['Attribute']['id'], '/attributes/view/' . $this -> object['Attribute']['id'], 'attribute', $this -> object['Attribute']['name']);
			$retVal = array_merge($retVal, $targetJSON);
		} else if ($this -> type === 'Listing') {
			$objectJSON = $this -> buildObject($this -> object['Listing']['id'], $this -> object['Listing']['url'], 'listing', $this -> object);
			$retVal = array_merge($retVal, $objectJSON);

			$targetJSON = $this -> buildTarget($this -> object['Collectible']['id'], '/collectibles/view/' . $this -> object['Collectible']['id'], 'collectible', $this -> object['Collectible']['displayTitle']);
			$retVal = array_merge($retVal, $targetJSON);
		}

		if ($this -> action === 'approve') {
			$targetJSON = $this -> buildTarget($this -> target['id'], '/stash/' . $this -> target['username'], 'user', $this -> target['username']);
			$retVal = array_merge($retVal, $targetJSON);
		}

		return $retVal;
	}

}
?>