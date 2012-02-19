<?php
/**
 * TODO: This really needs to be a behavior
 */
class CollectibleEdit extends AppModel {
	var $name = 'CollectibleEdit';
	var $actsAs = array('Containable');

	var $belongsTo = array('Collectible', 'SpecializedType', 'Manufacture' => array('className' => 'Manufacture', 'foreignKey' => 'manufacture_id'), 'Collectibletype' => array('className' => 'Collectibletype', 'foreignKey' => 'collectibletype_id'), 'License' => array('className' => 'License', 'foreignKey' => 'license_id'), 'Series' => array('className' => 'Series', 'foreignKey' => 'series_id'), 'Scale');

	private static $editCompareFields = array('name', 'manufacture_id', 'specialized_type_id', 'collectibletype_id', 'description', 'msrp', 'edition_size', 'numbered', 'upc', 'product_width', 'product_depth', 'license_id', 'series_id', 'variant', 'url', 'exclusive', 'retailer_id', 'variant_collectible_id', 'product_length', 'product_weight', 'scale_id', 'release', 'limited', 'code', 'pieces');

	function beforeSave() {
		//Update Edition Size stuff
		$editionSize = $this -> data['CollectibleEdit']['edition_size'];
		$limited = $this -> data['CollectibleEdit']['edition_size'];

		if (trim($editionSize) != '' && !$limited) {
			$editionSize = '';
		}

		//For whatever reason, cakephp year the put another array under the field
		if (isset($this -> data['CollectibleEdit']['release']['year'])) {
			$year = $this -> data['CollectibleEdit']['release']['year'];
			$this -> data['CollectibleEdit']['release'] = $year;
		}

		//Check to see if these are set, if they are not, default them to false
		if (!isset($this -> data['CollectibleEdit']['exclusive'])) {
			$this -> data['CollectibleEdit']['exclusive'] = 0;
		}
		if (!isset($this -> data['CollectibleEdit']['variant'])) {
			$this -> data['CollectibleEdit']['variant'] = 0;
		}

		$this -> data['CollectibleEdit']['msrp'] = str_replace('$', '', $this -> data['CollectibleEdit']['msrp']);
		$this -> data['CollectibleEdit']['msrp'] = str_replace(',', '', $this -> data['CollectibleEdit']['msrp']);

		//It always should be but just double check
		//Trim the white space away from beginning and end, since this is a core search field, keep it clean
		if (isset($this -> data['Collectible']['name'])) {
			$this -> data['Collectible']['name'] = trim($this -> data['Collectible']['name']);
		}

		return true;
	}

	function doAfterFind($results) {
		//Cleans up default no set years
		if (isset($results['release']) && $results['release'] === '0000') {
			$results['release'] = '';
		}
		if (isset($results['series_id']) && !empty($results['series_id'])) {
			$fullSeriesPath = $this -> Series -> buildSeriesPathName($results['series_id']);
			$results['seriesPath'] = $fullSeriesPath;
		}

		return $results;
	}

	/**
	 * This function will compare to versions of the collectible, the edit version
	 * and the current version of the collectible.
	 *
	 * Future Enhancements
	 * 	- Make this more automated...calls to the DB
	 *  - Store the list of fields that we want to compare against somewhere
	 *  - Behavior
	 *  - At some point, this is going to have to be based on collectible type...gonna have to roll that beast out
	 */
	function compareEdit(&$collectibleEdit, $collectible) {
		//TODO update this so that the changes are in their own array and not mixed in.
		foreach (static::$editCompareFields as $field) {
			$editFieldValue = $collectibleEdit[$field];
			$currentFieldValue = $collectible[$field];
			if ($editFieldValue !== $currentFieldValue) {
				$collectibleEdit[$field . '_changed'] = true;
			}
		}
		debug($collectibleEdit);
	}

	/**
	 * This will get the collectible reading for saving from an edit
	 *
	 * If $includeChanges is true, then we will get the current version of the collectible
	 * and check to see what is different.  This is going to show the differences between this edit
	 * and the latest version of the collectible we are editing.  This should be fine for now but this behavior might need to
	 * be updated in the future.  Not sure this is a good long term solution.  But at the time of someone editing this collectible
	 * they will see what they are saving against...unless someone swoops in and does a save inbetween a user getting this object and doing
	 * a save.
	 */
	function getUpdateFields($collectibleEditId, $includeChanges = false, $notes = null) {
		//Grab out edit collectible
		$collectibleEditVersion = $this -> find("first", array('contain' => false, 'conditions' => array('CollectibleEdit.id' => $collectibleEditId)));
		debug($collectibleEditVersion);
		if ($includeChanges) {
			$currentVersionCollectible = $this -> Collectible -> find("first", array('contain' => false, 'conditions' => array('Collectible.id' => $collectibleEditVersion['CollectibleEdit']['collectible_id'])));
			debug($currentVersionCollectible);
			$this -> compareEdit($collectibleEditVersion['CollectibleEdit'], $currentVersionCollectible['Collectible']);
		}

		//reformat it for us, unsetting some stuff we do not need
		$collectible = array();
		$collectible['Collectible'] = $collectibleEditVersion['CollectibleEdit'];
		/*
		 * Lets build our update array based on what has changed from the latest version of the collectible(as of now:)) and the one we are editing.  We only
		 * want to submit those changes, no need to update the rest.  We might overwrite changes here by accident.  If this becomes a problem then we will have
		 * to indicate at the edit process, exactly what the user changed so we do not do any accidental updates...TODO
		 */
		$changedString = '_changed';
		$updateFields = array();
		$changed = false;
		foreach ($collectible['Collectible'] as $key => $value) {
			if (substr_compare($key, $changedString, -strlen($changedString), strlen($changedString)) === 0) {
				//product_width_changed
				//0, 14
				//total length - (_changed) length
				$field = substr($key, 0, strlen($key) - strlen($changedString));
				//$updateFields[$field] = $collectible['Collectible'][$field];
				//$updateFields['Collectible.'.$field] = '\''.$collectible['Collectible'][$field].'\'';
				$updateFields['Collectible'][$field] = $collectible['Collectible'][$field];
				$changed = true;
			}
		}

		if ($changed) {
			$updateFields['Revision']['action'] = 'E';
			if (!is_null($notes)) {
				$updateFields['Revision']['notes'] = $notes;
			}
			//Make sure I grab the user id that did this edit
			$updateFields['Revision']['user_id'] = $collectible['Collectible']['edit_user_id'];
			$updateFields['Collectible']['id'] = $collectible['Collectible']['collectible_id'];
		}

		return $updateFields;
	}

}
?>
