<?php
/**
 * The AttributesCollectible and Upload need to on here so that the save automatically works.  I am not sure
 * how I like them on there but it works for now.
 *
 * 7/31/12 - TC: Need to expand this out so that I have my base edit table and then an edits_collectibles and an edits_attributes
 * 				When revamping, should I have the associated tables assigned to the edit_collectibles id or should they also get their own table...
 *
 * 				Current thought is to have an collectibles_edits table and a attributes_edits table...keep them completely separate from each other
 * 					- all of the collectible associated tables would then point to collectibles_edit_id
 *
 * 				Ugh, what if I add type back to either "Attribute" or "Collectile"? then I can get rid of the other EditsCollectibles]
 * 				It will make the behavior a lot easier.  We will just have to do lookups on everything
 */
class Edit extends AppModel {
	public $name = 'Edit';
	public $actsAs = array('Containable');
	public $belongsTo = array('User' => array('counterCache' => true, 'counterScope' => array('Edit.status' => 1)));

	public function afterFind($results, $primary = false) {
		if ($primary) {
			foreach ($results as $key => $val) {
				if (isset($val['Edit']['id'])) {
					$results[$key]['Edits'] = array();
					debug($val);
					$this -> bindModel(array('belongsTo' => array('Collectible')));
					$collectibleEdit = $this -> Collectible -> findEditsByEditId($val['Edit']['id']);
					$this -> unbindModel(array('belongsTo' => array('Collectible')));
					if (!empty($collectibleEdit)) {
						//Since there is only 1 allowed, just assume for now
						$collectibleEdit[0]['CollectibleEdit']['edit_type'] = 'Collectible';
						array_push($results[$key]['Edits'], $collectibleEdit[0]['CollectibleEdit']);

					}

					$this -> bindModel(array('belongsTo' => array('Attribute')));
					$attribute = $this -> Attribute -> findEditsByEditId($val['Edit']['id']);
					debug($attribute);
					$this -> unbindModel(array('belongsTo' => array('Attribute')));
					if (!empty($attribute)) {
						//Since there is only 1 allowed, just assume for now
						$attribute[0]['AttributeEdit']['edit_type'] = 'Attribute';
						array_push($results[$key]['Edits'], $attribute[0]['AttributeEdit']);

					}

					// This is when we are editing a collectibles attributes
					$this -> bindModel(array('belongsTo' => array('AttributesCollectible')));
					$attributes = $this -> AttributesCollectible -> findEditsByEditId($val['Edit']['id']);
					$this -> unbindModel(array('belongsTo' => array('AttributesCollectible')));
					if (!empty($attributes)) {

						//Only allowing one per for attributes right now, if we tie them together
						//the we will need to update this.
						$attributes[0]['AttributesCollectibleEdit']['edit_type'] = 'AttributesCollectible';
						//Since there is only 1 allowed, just assume for now
						array_push($results[$key]['Edits'], $attributes[0]['AttributesCollectibleEdit']);
					}

					// Not sure this is going to be applicable right now
					$this -> bindModel(array('belongsTo' => array('Upload')));
					$upload = $this -> Upload -> findEditsByEditId($val['Edit']['id']);
					$this -> unbindModel(array('belongsTo' => array('Upload')));
					if (!empty($upload)) {
						$upload[0]['UploadEdit']['edit_type'] = 'Upload';
						//Since there is only 1 allowed, just assume for now
						array_push($results[$key]['Edits'], $upload[0]['UploadEdit']);
					}

					$this -> bindModel(array('belongsTo' => array('CollectiblesTag')));
					$tags = $this -> CollectiblesTag -> findEditsByEditId($val['Edit']['id']);
					$this -> unbindModel(array('belongsTo' => array('CollectiblesTag')));
					if (!empty($tags)) {
						$tags[0]['CollectiblesTagEdit']['edit_type'] = 'Tag';
						//Since there is only 1 allowed, just assume for now
						array_push($results[$key]['Edits'], $tags[0]['CollectiblesTagEdit']);
					}

					$this -> bindModel(array('belongsTo' => array('CollectiblesUpload')));
					$collectiblesUploads = $this -> CollectiblesUpload -> findEditsByEditId($val['Edit']['id']);
					$this -> unbindModel(array('belongsTo' => array('CollectiblesUpload')));
					if (!empty($collectiblesUploads)) {
						$collectiblesUploads[0]['CollectiblesUploadEdit']['edit_type'] = 'CollectiblesUpload';
						//Since there is only 1 allowed, just assume for now
						array_push($results[$key]['Edits'], $collectiblesUploads[0]['CollectiblesUploadEdit']);
					}
				}
			}
		}

		return $results;
	}

	/**
	 * This will deny the edit, which I need to decide if I will just delete
	 * everything completely or update the status
	 */
	public function denyEdit($editId, $sendEmail = true) {
		// This will need to call the specific model, to handle clean up
		// In some cases we won't have a direct link

		$edit = $this -> find("first", array('conditions' => array('Edit.id' => $editId)));

		//save off the user id of the user who did the edit
		$userId = $edit['Edit']['user_id'];

		// Start the transaction here because
		// each model will handle saving itself
		$dataSource = $this -> getDataSource();
		$dataSource -> begin();
		$success = true;
		foreach ($edit['Edits'] as $key => $value) {
			debug($value);
			if ($success) {
				// new model only handles attributes for now
				if ($value['edit_type'] === 'Attribute') {
					$this -> bindModel(array('belongsTo' => array('Attribute')));
					// At this point it has to be true, if this method returns
					// false then we will end up stopping everything
					$success = $this -> Attribute -> denyEdit($value['id']);
					$this -> unbindModel(array('belongsTo' => array('Attribute')));
				} else if ($value['edit_type'] === 'AttributesCollectible') {
					$this -> bindModel(array('belongsTo' => array('AttributesCollectible')));
					$success = $this -> AttributesCollectible -> denyEdit($value['id']);
					$this -> unbindModel(array('belongsTo' => array('AttributesCollectible')));
				} else if ($value['edit_type'] === 'CollectiblesUpload') {
					$this -> bindModel(array('belongsTo' => array('CollectiblesUpload')));
					$success = $this -> CollectiblesUpload -> denyEdit($value['id']);
					$this -> unbindModel(array('belongsTo' => array('CollectiblesUpload')));
				} else if ($value['edit_type'] === 'Tag') {
					$this -> bindModel(array('belongsTo' => array('CollectiblesTag')));
					$success = $this -> CollectiblesTag -> denyEdit($value['id']);
					$this -> unbindModel(array('belongsTo' => array('CollectiblesTag')));
				} else if ($value['edit_type'] === 'Collectible') {
					$this -> bindModel(array('belongsTo' => array('Collectible')));
					$success = $this -> Collectible -> denyEdit($value['id'], $approvalUserId);
					$this -> unbindModel(array('belongsTo' => array('Collectible')));
				}
			}

		}
		// check to make sure all of our edits were successful first
		if ($success) {
			if ($this -> delete($editId)) {
				$dataSource -> commit();
				return true;
			} else {
				$dataSource -> rollback();
				return false;
			}
		} else {
			$dataSource -> rollback();
			return false;
		}
	}

	// This method will handle publishing an edit
	// An edit might contain multiple different "Edits"
	// so we will need to loop through all of them and then
	// process them individually
	public function publishEdit($editId, $approvalUserId) {
		// Let's grab the edit
		$edit = $this -> find("first", array('conditions' => array('Edit.id' => $editId)));

		//save off the user id of the user who did the edit
		$userId = $edit['Edit']['user_id'];

		// Start the transaction here because
		// each model will handle saving itself
		$dataSource = $this -> getDataSource();
		$dataSource -> begin();
		$success = true;
		foreach ($edit['Edits'] as $key => $value) {
			debug($value);
			if ($success) {
				// new model only handles attributes for now
				if ($value['edit_type'] === 'Attribute') {
					$this -> bindModel(array('belongsTo' => array('Attribute')));
					// At this point it has to be true, if this method returns
					// false then we will end up stopping everything
					$success = $this -> Attribute -> publishEdit($value['id'], $approvalUserId);
					$this -> unbindModel(array('belongsTo' => array('Attribute')));
				} else if ($value['edit_type'] === 'AttributesCollectible') {
					$this -> bindModel(array('belongsTo' => array('AttributesCollectible')));
					$success = $this -> AttributesCollectible -> publishEdit($value['id'], $approvalUserId);
					$this -> unbindModel(array('belongsTo' => array('AttributesCollectible')));
				} else if ($value['edit_type'] === 'CollectiblesUpload') {
					$this -> bindModel(array('belongsTo' => array('CollectiblesUpload')));
					$success = $this -> CollectiblesUpload -> publishEdit($value['id'], $approvalUserId);
					$this -> unbindModel(array('belongsTo' => array('CollectiblesUpload')));
				} else if ($value['edit_type'] === 'Tag') {
					$this -> bindModel(array('belongsTo' => array('CollectiblesTag')));
					$success = $this -> CollectiblesTag -> publishEdit($value['id'], $approvalUserId);
					$this -> unbindModel(array('belongsTo' => array('CollectiblesTag')));
				} else if ($value['edit_type'] === 'Collectible') {
					$this -> bindModel(array('belongsTo' => array('Collectible')));
					$success = $this -> Collectible -> publishEdit($value['id'], $approvalUserId);
					$this -> unbindModel(array('belongsTo' => array('Collectible')));
				}
			}

		}
		// check to make sure all of our edits were successful first
		if ($success) {
			$editFields = array();
			$editFields['id'] = $editId;
			// Update the status that we approved it
			$editFields['status'] = 1;
			if ($this -> saveAll($editFields, array('validate' => false))) {
				$dataSource -> commit();
				return true;
			} else {
				$dataSource -> rollback();
				return false;
			}
		} else {
			$dataSource -> rollback();
			return false;
		}
	}

}
?>
