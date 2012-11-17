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

}
?>