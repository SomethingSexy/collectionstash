<?php
App::uses('BaseActivity', 'Lib/Activity');
/**
 * Enhancements: When this eventually gets turned into something on the UI, we will want to expand the edit section so
 * we are better recording what was exactly sumbitted for an edit
 */
class EditActivity extends BaseActivity {

	private $user;

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

		if (isset($data['edit'])) {
			// this SHOULD be the version of the edit
			$this -> edit = $data['edit'];
		}

		if (isset($data['editType'])) {
			$this -> editType = $data['editType'];
		}

		parent::__construct();
	}

	public function buildActivityJSON() {
		$retVal = array();
		$retVal['published'] = date('Y-m-d H:i:s');
		// build the actor
		$actorJSON = $this -> buildActor('user', $this -> user);
		$retVal = array_merge($retVal, $actorJSON);

		if ($this -> action === 'submit') {
			$verbJSON = $this -> buildVerb('submit');
			$retVal = array_merge($retVal, $verbJSON);

			if ($this -> editType === 'Collectible') {
				$objectJSON = $this -> buildEditCollectibleObject($this -> edit);
				$retVal = array_merge($retVal, $objectJSON);

				$targetJSON = $this -> buildEditCollectibleTarget($this -> edit);
				$retVal = array_merge($retVal, $targetJSON);
			} else if ($this -> editType === 'CollectiblesTag') {
				$objectJSON = $this -> buildEditCollectibleTagObject($this -> edit);
				$retVal = array_merge($retVal, $objectJSON);

				$targetJSON = $this -> buildEditCollectibleTagTarget($this -> edit);
				$retVal = array_merge($retVal, $targetJSON);
			} else if ($this -> editType === 'CollectiblesUpload') {
				$objectJSON = $this -> buildEditCollectiblesUploadObject($this -> edit);
				$retVal = array_merge($retVal, $objectJSON);

				$targetJSON = $this -> buildEditCollectiblesUploadTarget($this -> edit);
				$retVal = array_merge($retVal, $targetJSON);
			}

		} else if ($this -> action === 'approve') {

			$verbJSON = $this -> buildVerb('approve');
			$retVal = array_merge($retVal, $verbJSON);

			// build the object we are acting on
			$objectJSON = $this -> buildObject($this -> edit['Edit']['id'], null, 'edit', array());
			$retVal = array_merge($retVal, $objectJSON);

			$targetJSON = $this -> buildTarget($this -> target['id'], '/stash/' . $this -> user['username'], 'user', $this -> user['username']);
			$retVal = array_merge($retVal, $targetJSON);
		}

		return $retVal;
	}

	private function buildEditCollectibleObject($edit) {
		$data = array();
		$data['edit_id'] = $edit['CollectibleEdit']['edit_id'];
		$data['action_id'] = $edit['Action']['id'];
		$data['action_type_id'] = $edit['Action']['action_type_id'];
		$data['type'] = $this -> editType;
		// Set the id of the edit
		$objectJSON = $this -> buildObject($edit['CollectibleEdit']['edit_id'], null, 'edit', $data);

		return $objectJSON;
	}

	private function buildEditCollectibleTarget($edit) {
		// If they change the name, that will get reflected here but eh not a huge deal right now
		$targetJSON = $this -> buildTarget($edit['CollectibleEdit']['base_id'], 'collectibles/view/' . $edit['CollectibleEdit']['base_id'], 'collectible', $edit['CollectibleEdit']['name']);

		return $targetJSON;
	}

	private function buildEditCollectibleTagObject($edit) {
		$data = array();
		$data['edit_id'] = $edit['CollectiblesTag']['edit_id'];
		$data['action_id'] = $edit['Action']['id'];
		$data['action_type_id'] = $edit['Action']['action_type_id'];
		$data['type'] = $this -> editType;
		// Set the id of the edit
		$objectJSON = $this -> buildObject($edit['CollectiblesTag']['edit_id'], null, 'edit', $data);

		return $objectJSON;
	}

	private function buildEditCollectibleTagTarget($edit) {
		// not sure I will be able to get the collectible name here yet
		$targetJSON = $this -> buildTarget($edit['CollectiblesTag']['collectible_id'], 'collectibles/view/' . $edit['CollectiblesTag']['collectible_id'], 'collectible', null);

		return $targetJSON;
	}

	private function buildEditCollectiblesUploadObject($edit) {
		$data = array();
		$data['edit_id'] = $edit['CollectiblesUpload']['edit_id'];
		$data['action_id'] = $edit['Action']['id'];
		$data['action_type_id'] = $edit['Action']['action_type_id'];
		$data['type'] = $this -> editType;
		// Set the id of the edit
		$objectJSON = $this -> buildObject($edit['CollectiblesUpload']['edit_id'], null, 'edit', $data);

		return $objectJSON;
	}

	private function buildEditCollectiblesUploadTarget($edit) {
		// not sure I will be able to get the collectible name here yet
		$targetJSON = $this -> buildTarget($edit['CollectiblesUpload']['collectible_id'], 'collectibles/view/' . $edit['CollectiblesUpload']['collectible_id'], 'collectible', null);

		return $targetJSON;
	}

}
?>