<?php
class CollectiblesTag extends AppModel {

	public $name = 'CollectiblesTag';
	public $belongsTo = array('Collectible', 'Tag' => array('counterCache' => true), 'Revision');
	public $actsAs = array('Containable', 'Editable' => array('type' => 'tag', 'model' => 'CollectiblesTagEdit'));

	function publishEdit($tagEditId, $notes = null) {
		//Grab out edit collectible
		$tagEditVersion = $this -> findEdit($tagEditId);
		//reformat it for us, unsetting some stuff we do not need
		debug($tagEditVersion);
		$tagFields = array();
		if ($tagEditVersion['Action']['action_type_id'] === '1') {
			$tag = array();
			$tag['CollectiblesTag']['tag_id'] = $tagEditVersion['CollectiblesTagEdit']['tag_id'];
			$tag['CollectiblesTag']['collectible_id'] = $tagEditVersion['CollectiblesTagEdit']['collectible_id'];
			// Setting this as an add because it was added to the new table..not sure this is right
			$tag['Revision']['action'] = 'A';
			$tag['Revision']['user_id'] = $tagEditVersion['CollectiblesTagEdit']['edit_user_id'];
			if ($this -> saveAll($tag, array('validate' => false))) {
				$retVal = true;
			}

		} else if ($tagEditVersion['Action']['action_type_id'] === '4') {
			// At this point this has to have been approved, so delete it
			if (!$this -> delete($tagEditVersion['CollectiblesTagEdit']['base_id'])) {
				return false;
			}
		}

		if ($retVal) {
			$collectible = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $tagEditVersion['CollectiblesTagEdit']['collectible_id'])));
			$message = 'We have approved the following tag you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $tagEditVersion['CollectiblesTagEdit']['collectible_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$this -> notifyUser($tagEditVersion['CollectiblesTagEdit']['edit_user_id'], $message);
		}

		return true;
	}

	public function denyEdit($editId) {
		$retVal = false;
		debug($editId);
		// Grab the fields that will need to updated
		$tagEditVersion = $this -> findEdit($editId);
		debug($tagEditVersion);
		// Right now we can really only add or edit
		if ($tagEditVersion['Action']['action_type_id'] === '1') {//Add
			// If we are adding, we need to check and see if the attribute is new or
			// existing.
			// If it is new, then we will also be deleting that
			if ($this -> deleteEdit($tagEditVersion)) {
				$retVal = true;
			}

		} else if ($tagEditVersion['Action']['action_type_id'] === '2') {// Edit
			if ($this -> deleteEdit($tagEditVersion)) {
				$retVal = true;
			}

		} else if ($tagEditVersion['Action']['action_type_id'] === '4') {// Delete
			// If we are deny a delete, then we are keeping it out there
			// so just delete the edit
			if ($this -> deleteEdit($tagEditVersion)) {
				$retVal = true;
			}

		}

		if ($retVal) {
			$collectible = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $tagEditVersion['CollectiblesTagEdit']['collectible_id'])));
			$message = 'We have denied the following tag you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $tagEditVersion['CollectiblesTagEdit']['collectible_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$this -> notifyUser($tagEditVersion['CollectiblesTagEdit']['edit_user_id'], $message);
		}

		return $retVal;
	}

	/**
	 * If it is an add
	 */
	public function add($data, $user, $autoUpdate = false) {
		$retVal = $this -> buildDefaultResponse();
		$this -> Tag -> set($data['CollectiblesTag']);
		$validCollectible = true;
	

		if ($this -> Tag -> validates()) {
			// just in case
			unset($data['CollectiblesTag']['id']);
			$data = $this -> Tag -> processTag($data);

			// Now let's check to see if we need to update this based
			// on collectible status
			// If we are already auto updating, no need to check
			if ($autoUpdate === 'false' || $autoUpdate === false) {
				$isDraft = $this -> Collectible -> isStatusDraft($data['CollectiblesTag']['collectible_id']);
				debug($isDraft);
				if ($isDraft) {
					$autoUpdate = true;
				}
			}

			if ($autoUpdate === true || $autoUpdate === 'true') {

				$revision = $this -> Revision -> buildRevision($user['User']['id'], $this -> Revision -> ADD, null);
				$data = array_merge($data, $revision);
				debug($data);
				if ($this -> saveAll($data, array('validate' => false))) {
					$id = $this -> id;
					$collectiblesTag = $this -> find('first', array('contain' => array('Tag'), 'conditions' => array('CollectiblesTag.id' => $id)));

					$retVal['response']['data'] = $collectiblesTag['CollectiblesTag'];
					$retVal['response']['isSuccess'] = true;
				} else {

				}
			} else {
				$action = array();
				$action['Action']['action_type_id'] = 1;

				if ($this -> saveEdit($data, null, $user['User']['id'], $action)) {
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['data']['isEdit'] = true;
				} else {

				}
			}

		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> Tag -> validationErrors, 'Tag');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	public function remove($data, $user, $autoUpdate = false) {
		$retVal = $this -> buildDefaultResponse();
		// There will be an ['Attribute']['reason'] - input field
		// if this attribute is tied to a collectible, are we replacing
		// with an existing attriute? Or removing completely, which will
		// remove all references
		$action = array();
		$action['Action']['action_type_id'] = 4;
		$action['Action']['reason'] = '';

		debug($data);

		$currentVersion = $this -> findById($data['CollectiblesTag']['id']);
		debug($currentVersion);
		// Now let's check to see if we need to update this based
		// on collectible status
		// If we are already auto updating, no need to check
		if ($autoUpdate === 'false' || $autoUpdate === false) {
			$isDraft = $this -> Collectible -> isStatusDraft($currentVersion['CollectiblesTag']['collectible_id']);
			debug($isDraft);
			if ($isDraft) {
				$autoUpdate = true;
			}
		}

		if ($autoUpdate === true || $autoUpdate === 'true') {
			if ($this -> delete($data['CollectiblesTag']['id'])) {
				$retVal['response']['isSuccess'] = true;
			} else {
				$retVal['response']['isSuccess'] = false;
			}
		} else {
			// Doing this so that we have a record of the current version
			if ($this -> saveEdit($currentVersion, $data['CollectiblesTag']['id'], $user['User']['id'], $action)) {
				$retVal['response']['isSuccess'] = true;
				$retVal['response']['data']['isEdit'] = true;
			} else {
				$retVal['response']['isSuccess'] = false;
			}
		}
		return $retVal;
	}

}
?>