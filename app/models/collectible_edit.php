<?php
class CollectibleEdit extends AppModel {
	var $name = 'CollectibleEdit';
	var $actsAs = array('Containable');

	var $belongsTo = array('Collectible', 'Manufacture' => array('className' => 'Manufacture', 'foreignKey' => 'manufacture_id'), 'Collectibletype' => array('className' => 'Collectibletype', 'foreignKey' => 'collectibletype_id'), 'License' => array('className' => 'License', 'foreignKey' => 'license_id'), 'Series' => array('className' => 'Series', 'foreignKey' => 'series_id'), 'Scale');

	private static $editCompareFields = array('name', 'manufacture_id', 'collectibletype_id', 'description', 'msrp', 'edition_size', 'upc', 'product_width', 'product_depth', 'license_id', 'series_id', 'variant', 'url', 'exclusive', 'retailer_id', 'variant_collectible_id', 'product_length', 'product_weight', 'scale_id', 'release', 'limited', 'code');

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

		return true;
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
	 */
	function getEditCollectible($collectibleEditId) {
		//Grab out edit collectible
		$collectibleEditVersion = $this -> find("first", array('conditions' => array('CollectibleEdit.id' => $collectibleEditId)));
		//reformat it for us, unsetting some stuff we do not need
		$collectible = array();
		$collectible['Collectible'] = $collectibleEditVersion['CollectibleEdit'];
		unset($collectible['Collectible']['id']);
		unset($collectible['Collectible']['created']);
		unset($collectible['Collectible']['modified']);

		return $collectible;

	}

}
?>
