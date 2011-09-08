<?php
class AttributesCollectiblesController extends AppController {

	var $name = 'AttributesCollectibles';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	/*
	 * In the future when doing this edits, we are going to have to make sure that these parts are not being
	 * used in a custom when we delete them
	 */
	function edit($id = null) {
		$this -> checkLogIn();

		if (!$id && !is_numeric($id) && empty($this -> data)) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			//TODO go somewhere
			$this -> redirect($this -> referer());
		}

		if (!empty($this -> data)) {
			debug($this -> data);
			if (isset($this -> data['AttributesCollectible'])) {
				$isValid = true;
				//TODO this does not seem right
				foreach ($this -> data['AttributesCollectible'] as $key => $attribue) {
					//If it is being deleted, I do not care to validate it.
					if ($attribue['action'] !== 'D') {
						$this -> AttributesCollectible -> set($attribue);
						//debug($this -> AttributesCollectible);
						if ($this -> AttributesCollectible -> validates()) {

						} else {
							//If one is invalid set it to false
							$isValid = false;
							debug($this -> AttributesCollectible -> invalidFields());
							$this -> set('errors', $this -> AttributesCollectible -> validationErrors);
						}
					}
				}
				//if everything is valid, then lets do our updates
				if ($isValid) {
					//TODO move this to the model, validate we are removing the correct attributes?
					foreach ($this -> data['AttributesCollectible'] as $key => $attribue) {
						//debug($this -> AttributesCollectible);
						if ($attribue['action'] === 'D') {
							$this -> AttributesCollectible -> id = $attribue['id'];
							//If we are deleting then set the active state to zero, I believe this is for history purposes.
							//however, it looks like it is changing the originally added one...need to verify this.
							if ($this -> AttributesCollectible -> save(array('active' => 0), false, array('active'))) {

							}
						} else if ($attribue['action'] === 'N') {
							$attribue['collectible_id'] = $id;
							$this -> AttributesCollectible -> create();
							$this -> AttributesCollectible -> set($attribue);
							if ($this -> AttributesCollectible -> save()) {

							}
						} else if ($attribue['action'] === 'E') {
							$this -> AttributesCollectible -> id = $attribue['id'];
							if ($this -> AttributesCollectible -> saveField('description', $attribue['description'], false)) {

							}
						}

					}
					$attributes = $this -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.collectible_id' => $id, 'AttributesCollectible.active' => 1), 'contain' => 'Attribute'));
					$this -> data = $attributes;
					$this -> Session -> setFlash(__('You have succesfully updated the attributes.', true), null, null, 'success');
				} else {
					$errorAttributes = array();
					foreach ($this->data['AttributesCollectible'] as $key => $attribue) {
						$attributesCollectible = array();
						$attributesCollectible['AttributesCollectible'] = $attribue;
						$attributesCollectible['Attribute'] = array();
						$attributesCollectible['Attribute']['name'] = $attribue['name'];
						array_push($errorAttributes, $attributesCollectible);
					}
					debug($errorAttributes);
					$this -> data = $errorAttributes;
				}
			}
		} else {
			$this -> set('collectibleId', $id);
			$this -> Session -> write('collectible.edit-id', $id);
			//Submit the deletes as deletes....then loop throuh each one to either delete or add
			$attributes = $this -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.collectible_id' => $id, 'AttributesCollectible.active' => 1), 'contain' => 'Attribute'));
			debug($attributes);
			//$this -> set('attributes', $attributes);
			$this -> data = $attributes;
		}

		$collectibleId = $this -> Session -> read('collectible.edit-id');
		$this -> set(compact('collectibleId'));
	}

	/**
	 * This method will return the history for a given attributes collectible
	 */
	function history($id = null) {
		$this -> checkLogIn();
		if ($id && is_numeric($id)) {
			//Date and timestamp of update and user who did the update
			$this -> AttributesCollectible -> id = $id;
			$history = $this -> AttributesCollectible -> revisions(null, true);
			//As of 9/7/11, because of the way we have to add an attributes collectible, the first revision is going to be bogus.
			//Pop it off here until we can update the revision behavior so that we can specific a save to not add a revision.
			$lastHistory= end($history);
			if ($lastHistory['AttributesCollectible']['revision_id'] === '0') {
				array_pop($history);
			}
			reset($history);
			debug($history);
			$this -> set(compact('history'));

		} else {
			$this -> redirect($this -> referer());
		}
	}

}
?>