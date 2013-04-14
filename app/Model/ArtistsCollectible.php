<?php
class ArtistsCollectible extends AppModel {
	public $name = 'ArtistsCollectible';
	public $belongsTo = array('Collectible', 'Artist' => array('counterCache' => true), 'Revision');
	public $actsAs = array('Containable', 'Editable' => array('type' => 'artist', 'model' => 'ArtistsCollectibleEdit'));

	function publishEdit($tagEditId, $notes = null) {
		//Grab out edit collectible
		$tagEditVersion = $this -> findEdit($tagEditId);
		//reformat it for us, unsetting some stuff we do not need
		$tagFields = array();
		if ($tagEditVersion['Action']['action_type_id'] === '1') {
			$tag = array();
			$tag['ArtistsCollectible']['artist_id'] = $tagEditVersion['ArtistsCollectibleEdit']['artist_id'];
			$tag['ArtistsCollectible']['collectible_id'] = $tagEditVersion['ArtistsCollectibleEdit']['collectible_id'];
			// Setting this as an add because it was added to the new table..not sure this is right
			$tag['Revision']['action'] = 'A';
			$tag['Revision']['user_id'] = $tagEditVersion['ArtistsCollectibleEdit']['edit_user_id'];
			if ($this -> saveAll($tag, array('validate' => false))) {
				$retVal = true;
			}

		} else if ($tagEditVersion['Action']['action_type_id'] === '4') {
			// At this point this has to have been approved, so delete it
			if (!$this -> delete($tagEditVersion['ArtistsCollectibleEdit']['base_id'])) {
				return false;
			}
		}

		if ($retVal) {
			$collectible = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $tagEditVersion['ArtistsCollectibleEdit']['collectible_id'])));
			$message = 'We have approved the following artist you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $tagEditVersion['ArtistsCollectibleEdit']['collectible_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$this -> notifyUser($tagEditVersion['ArtistsCollectibleEdit']['edit_user_id'], $message);
		}

		return true;
	}

	public function denyEdit($editId) {
		$retVal = false;
		// Grab the fields that will need to updated
		$tagEditVersion = $this -> findEdit($editId);
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
			$collectible = $this -> Collectible -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $tagEditVersion['ArtistsCollectibleEdit']['collectible_id'])));
			$message = 'We have denied the following artist you submitted a change to <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $tagEditVersion['ArtistsCollectibleEdit']['collectible_id'] . '">' . $collectible['Collectible']['name'] . '</a>';
			$this -> notifyUser($tagEditVersion['ArtistsCollectibleEdit']['edit_user_id'], $message);
		}

		return $retVal;
	}

	/**
	 * If it is an add
	 */
	public function add($data, $user, $autoUpdate = false) {
		$retVal = $this -> buildDefaultResponse();
		$this -> Artist -> set($data['ArtistsCollectible']);
		$validCollectible = true;

		if ($this -> Artist -> validates()) {
			// just in case
			unset($data['ArtistsCollectible']['id']);
			$data = $this -> Artist -> processArtist($data);

			// Now let's check to see if we need to update this based
			// on collectible status
			// If we are already auto updating, no need to check
			if ($autoUpdate === 'false' || $autoUpdate === false) {
				$autoUpdate = $this -> Collectible -> allowAutoUpdate($data['ArtistsCollectible']['collectible_id'], $user);
			}

			if ($autoUpdate === true || $autoUpdate === 'true') {

				$revision = $this -> Revision -> buildRevision($user['User']['id'], $this -> Revision -> ADD, null);
				$data = array_merge($data, $revision);
				if ($this -> saveAll($data, array('validate' => false))) {
					$id = $this -> id;
					$collectiblesTag = $this -> find('first', array('contain' => array('Artist'), 'conditions' => array('ArtistsCollectible.id' => $id)));

					$retVal['response']['data'] = $collectiblesTag['ArtistsCollectible'];
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
			$errors = $this -> convertErrorsJSON($this -> Artist -> validationErrors, 'Artist');
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

		$currentVersion = $this -> findById($data['ArtistsCollectible']['id']);
		// Now let's check to see if we need to update this based
		// on collectible status
		// If we are already auto updating, no need to check
		if ($autoUpdate === 'false' || $autoUpdate === false) {
			$autoUpdate = $this -> Collectible -> allowAutoUpdate($currentVersion['ArtistsCollectible']['collectible_id'], $user);
		}

		if ($autoUpdate === true || $autoUpdate === 'true') {
			if ($this -> delete($data['ArtistsCollectible']['id'])) {
				$retVal['response']['isSuccess'] = true;
			} else {
				$retVal['response']['isSuccess'] = false;
			}
		} else {
			// Doing this so that we have a record of the current version
			if ($this -> saveEdit($currentVersion, $data['ArtistsCollectible']['id'], $user['User']['id'], $action)) {
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