<?php

/**
 * This should also handle the logic for committing the edit to the main model I think
 *
 * Based on the actions it can do something specific and we can have different callbacks if
 * necessary
 */
class EditableBehavior extends ModelBehavior {

	/**
	 * Shadow table prefix
	 * Only change this value if it causes table name crashes
	 *
	 * @access private
	 * @var string
	 */
	private $edit_suffix = '_edits';
	/**
	 * Defaul setting values
	 *
	 * @access private
	 * @var array
	 */
	private $defaults = array('useDbConfig' => null, 'model' => null, 'modelAssociations' => null);

	/**
	 * Configure the behavior through the Model::actsAs property
	 *
	 * @param object $Model
	 * @param array $config
	 */
	public function setup(&$Model, $settings = null) {
		if (is_array($settings)) {
			$this -> settings[$Model -> alias] = array_merge($this -> defaults, $settings);
		} else {
			$this -> settings[$Model -> alias] = $this -> defaults;
		}
		$this -> createShadowModel($Model);
		$Model -> Behaviors -> attach('Containable');
		if (isset($this -> settings[$Model -> alias]['behaviors'])) {
			foreach ($this -> settings[$Model -> alias]['behaviors'] as $key => $value) {
				$Model -> EditModel -> Behaviors -> load($key, $value);
			}
		}
	}

	/**
	 * Returns a generic model that maps to the current $Model's shadow table.
	 *
	 * @param object $Model
	 * @return boolean
	 */
	private function createShadowModel(&$Model) {

		if (is_null($this -> settings[$Model -> alias]['useDbConfig'])) {
			$dbConfig = $Model -> useDbConfig;
		} else {
			$dbConfig = $this -> settings[$Model -> alias]['useDbConfig'];
		}
		$db = &ConnectionManager::getDataSource($dbConfig);
		if ($Model -> useTable) {
			$shadow_table = $Model -> useTable;
		} else {
			$shadow_table = Inflector::tableize($Model -> name);
		}
		$shadow_table = $shadow_table . $this -> edit_suffix;
		$prefix = $Model -> tablePrefix ? $Model -> tablePrefix : $db -> config['prefix'];
		$full_table_name = $prefix . $shadow_table;

		$existing_tables = $db -> listSources();

		if (!in_array($full_table_name, $existing_tables)) {
			$Model -> EditModel = false;
			return false;
		}

		$useShadowModel = $this -> settings[$Model -> alias]['model'];

		if (is_string($useShadowModel) && App::import('model', $useShadowModel)) {
			$Model -> EditModel = new $useShadowModel(false, $shadow_table, $dbConfig);
		} else {
			$Model -> EditModel = new Model(false, $shadow_table, $dbConfig);
		}
		if ($Model -> tablePrefix) {
			$Model -> EditModel -> tablePrefix = $Model -> tablePrefix;
		}

		/*
		 * Updated 1/7/12 after cakephp 2.0 upgrade, this was done
		 * because this shadow model gets added as an associated
		 * model and the alias was the same name, so when validations
		 * where being done and passing the validations to
		 * the view, it was overwriting the validations (this is done
		 * in controller when calling the render)
		 *
		 */
		$Model -> EditModel -> alias = $Model -> alias . 'Edit';
		$Model -> EditModel -> primaryKey = 'id';
		return true;
	}

	/**
	 * If successful, it will return the edit data in the main model form
	 */
	public function saveEdit($Model, $editData, $editId, $userId, $action = null) {
		$multipleSave = false;
		$Model -> EditModel -> bindModel(array('belongsTo' => array('Action')));		/*
		 *
		 */
		if (isset($editData[$Model -> alias])) {
			$editData[$Model -> alias]['edit_user_id'] = $userId;

			$saveEdit = array();
			$editData = $Model -> beforeSaveEdit($editData);
			$saveEdit[$Model -> EditModel -> alias] = $editData[$Model -> alias];
			$saveEdit[$Model -> EditModel -> alias]['base_id'] = $editId;
			// Make sure we are not accidently setting the id
			unset($saveEdit[$Model -> EditModel -> alias]['id']);
			if (!is_null($action) && isset($action['Action'])) {
				$saveEdit['Action'] = $action['Action'];
			}

			//If it is a one model save then we can just do the edit here
			$saveEdit['Edit']['user_id'] = $userId;
		} else {
			$multipleSave = true;
			$saveEdit = array();
			foreach ($editData as $key => $value) {
				$editItem = $Model -> beforeSaveEdit($value);
				$editItem['edit_user_id'] = $userId;
				// $editItem['action'] = $action;
				array_push($saveEdit, $editItem);
			}
		}
		$succesful = true;
		$retVal = array();
		$Model -> EditModel -> bindModel(array('belongsTo' => array('Edit')));		if (!$multipleSave) {
			$Model -> EditModel -> create();
			debug($saveEdit);
			if ($Model -> EditModel -> saveAll($saveEdit, array('validate' => false, 'deep' => true))) {
				$id = $Model -> EditModel -> id;
				//Grab the one we just submitted
				$savedEdit = $Model -> findEdit($id);
				$returnEdit = $savedEdit;
				$returnEdit[$Model -> alias] = $savedEdit[$Model -> EditModel -> alias];
				unset($returnEdit[$Model -> EditModel -> alias]);
				unset($returnEdit[$Model -> alias . 'Edit']);
				$retVal = $returnEdit;
			} else {
				$succesful = false;
			}
		} else {
			$edit = array();
			$edit['Edit']['user_id'] = $userId;
			$Model -> EditModel -> Edit -> create();
			if ($Model -> EditModel -> Edit -> save($edit)) {
				$editId = $Model -> EditModel -> Edit -> id;
				foreach ($saveEdit as $key => &$value) {
					$value['edit_id'] = $editId;
				}
				if ($Model -> EditModel -> saveAll($saveEdit, array('validate' => false, 'deep' => true))) {

				} else {
					$Model -> EditModel -> Edit -> delete($editId);
					$succesful = false;
				}

			} else {
				$succesful = false;
			}
		}
		$Model -> EditModel -> unbindModel(array('belongsTo' => array('Edit')));
		$Model -> EditModel -> unbindModel(array('belongsTo' => array('Action')));
		if ($succesful) {
			return $retVal;
		} else {
			return false;
		}
	}

	public function findEdit($Model, $id) {
		$associations = $this -> settings[$Model -> alias]['modelAssociations'];
		if (isset($associations) && is_array($associations)) {
			foreach ($associations as $type => $listOfAss) {
				foreach ($listOfAss as $key => $ass) {
					$Model -> EditModel -> bindModel(array($type => array($ass)));
				}
			}
		}
		$Model -> EditModel -> bindModel(array('belongsTo' => array('Action', 'User' => array('foreignKey' => 'edit_user_id'))));		// TODO: Figure out why this is not returning action type for me already		return $Model -> EditModel -> find("first", array('contain' => array('Action' => array('ActionType')), 'conditions' => array($Model -> EditModel -> alias . '.id' => $id)));
	}

	public function findEditsByEditId($Model, $edit_id) {
		$Model -> EditModel -> bindModel(array('belongsTo' => array('Action')));
		return $Model -> EditModel -> find("all", array('contain' => array('Action' => array('ActionType')), 'conditions' => array($Model -> EditModel -> alias . '.edit_id' => $edit_id)));
	}

	/**
	 * This function will compare to versions of the collectible, the edit version
	 * and the current version of the collectible.
	 *
	 * Future Enhancements
	 *  - Make this more automated...calls to the DB
	 *  - Store the list of fields that we want to compare against somewhere
	 *  - Behavior
	 *  - At some point, this is going to have to be based on collectible type...gonna have to roll that beast out
	 */
	public function compareEdit($Model, $edit, $base) {
		$returnCompare = $edit;
		$returnCompare[$Model -> alias] = $base[$Model -> alias];
		//TODO update this so that the changes are in their own array and not mixed in.
		foreach ($this -> settings[$Model -> alias]['compare'] as $field) {
			$returnCompare[$Model -> alias][$field] = $edit[$Model -> EditModel -> alias][$field];
			$editFieldValue = $edit[$Model -> EditModel -> alias][$field];
			$currentFieldValue = $base[$Model -> alias][$field];
			if ($editFieldValue !== $currentFieldValue) {
				$returnCompare[$Model -> alias][$field . '_changed'] = true;
			}
		}
		return $returnCompare;
	}

	/**
	 * This could get rolled up into a more generic method with options at some point
	 */
	public function getEditForApproval($Model, $editId) {
		$editVersion = $this -> findEdit($Model, $editId);

		//So what happens when the associated models have changed because the edit is different?
		$current = $Model -> find("first", array('contain' => false, 'conditions' => array($Model -> alias . '.id' => $editVersion[$Model -> EditModel -> alias]['base_id'])));

		// Based on action type I am going to return different things
		if (!empty($editVersion)) {
			if ($editVersion['Action']['action_type_id'] === '2') {
				$processedCurent = $this -> compareEdit($Model, $editVersion, $current);
			} else {
				//copy all
				$processedCurent = $editVersion;
				//rename
				$processedCurent[$Model -> alias] = $editVersion[$Model -> EditModel -> alias];
				//remove edit
				unset($processedCurent[$Model -> EditModel -> alias]);
			}

			return $processedCurent;
		} else {
			return false;
		}
	}

	public function getUpdateFields($collectibleEditId, $includeChanges = false, $notes = null) {
	}

	public function beforeSaveEdit($Model, $editData) {
		return $editData;
	}

	/**
	 * It will be this methods job to do with the EditsController is
	 * It wil grab the ncessary data to publish the edit, then commit it
	 * and update the edit record
	 *
	 * Depending on what is being done we might want some callbacks
	 *
	 * For instance, if I am removing an Attribute we might have some logic
	 * to then link existing collectibles to a different Attribute, we would
	 * need to handle that special case.  Would it then add extra data? I suppose
	 * it could be handled in the update fields, however if I am deleting I need to
	 * do a delete and then an update of existing things, so I would have to do
	 * the delete and then commit the changes
	 *
	 *
	 * We might want to try something like this:
	 *
	 * https://github.com/gmansilla/Transactions-and-Cakephp/blob/master/models/product.php
	 *
	 * To handle the transactions between deleting an attribute and then updating existing collectible attributes
	 *
	 * 	$dataSource = $this->getDataSource();
	 $dataSource->begin($this); //begin transaction
	 if ($User->saveField('balance', $balance - $product['Product']['price'] * $quantity, true)){ //if this query is OK then proceed with the next one
	 if($this->saveField('inventory', $product['Product']['inventory'] - $quantity, true)){ //if this query is OK along with the first one then we can commit all queries
	 return $dataSource->commit($this); //here is the line that commits all queries that we placed on the transaction
	 }
	 }
	 $dataSource->rollback($this);
	 *
	 */

	/**
	 * Handles deleting of the edit
	 */
	public function deleteEdit($Model, $edit) {
		$Model -> EditModel -> bindModel(array('belongsTo' => array('Action' => array('dependent' => true))));
		if ($Model -> EditModel -> delete($edit[$Model -> EditModel -> alias]['id'])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Given some conditions (these have to be determined by the caller), find all pending edits
	 */
	public function findPendingEdits($Model, $conditions) {
		$associations = $this -> settings[$Model -> alias]['modelAssociations'];
		if (isset($associations) && is_array($associations)) {
			foreach ($associations as $type => $listOfAss) {
				foreach ($listOfAss as $key => $ass) {
					$Model -> EditModel -> bindModel(array($type => array($ass)));
				}
			}
		}

		$joins = array( array('table' => 'edits', 'alias' => 'Edit', 'type' => 'inner', 'conditions' => array('Edit.status = 0', 'Edit.id = ' . $Model -> EditModel -> alias . '.edit_id')));

		return $Model -> EditModel -> find("all", array('joins' => $joins, 'contain' => array('Action' => array('ActionType')), 'conditions' => $conditions));
	}

}
?>