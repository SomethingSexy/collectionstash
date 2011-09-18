<?php
class CollectibletypesManufacture extends AppModel {
	var $name = 'CollectibletypesManufacture';
	var $belongsTo = array('Manufacture', 'Collectibletype');
	var $hasMany = array('CollectibletypesManufactureSpecializedType');
	var $actsAs = array('Containable');

	/**
	 * Return a list of main level collectible types by manufacturer
	 */
	public function getCollectibleTypeByManufactureId($manufacutre_id) {
		//TODO: This would be probably be better using a join

		//Grab all top level collectible types
		$topLevelCollectibleTypes = $this -> Collectibletype -> getListTopLevelTypes();
		//This is all collectible types for this manufacturer
		$manColTypes = $this -> find("list", array('fields' => array('CollectibletypesManufacture.collectibletype_id', 'Collectibletype.name'), 'conditions' => array('CollectibletypesManufacture.manufacture_id' => $manufacutre_id), 'recursive' => 0, 'order' => array('Collectibletype.name' => 'ASC')));
		//Now filter these, might be better to update this to some sort of join/in query
		$collectibleTypes = array();
		foreach ($manColTypes as $key => $value) {
			if (array_key_exists($key, $topLevelCollectibleTypes)) {
				$collectibleTypes[$key] = $value;
			}
		}

		return $collectibleTypes;
	}

	/**
	 * This will return arrays of each level of collectibles types from the path of the given collectible type id.  It
	 *  will also return the collectible that is selected in the array
	 */
	public function getCollectibleTypesPaths($manufactureId, $collectibleTypeId) {
		//This will get all main level collectible types for this manufacture
		$collectibletypes = $this -> getCollectibleTypeByManufactureId($manufactureId);
		//Set the baseline as L0, there should ALWAYS be a L0.
		$returnData['collectibletypes_L0'] = $collectibletypes;

		$returnData['selectedTypes'] = array();
		//Now check for the path of this collectible type, we do this to get all of the potential paths we want to, to select this collectible
		//Right now we are assuming that when this manufacturer was setup, that if they added a child type, the parent type was added
		$paths = $this -> Collectibletype -> getpath($collectibleTypeId);
		$lastValue = 0;
		//This lists out paths in order, the first being the high level
		//Loop through each path, and grab the parent of the children to get that data, we will add subsequent children as L1, L2, L3.
		foreach ($paths as $key => $value) {
			$returnData['selectedTypes']['L' . $key] = $value['Collectibletype']['id'];
			//We already grabbed the base line types
			if (!is_null($value['Collectibletype']['parent_id'])) {
				$levelTypes = $this -> Collectibletype -> children($value['Collectibletype']['parent_id']);
				$levelListTypes = array();
				//return this as a simple id/name
				// foreach ($levelTypes as $levelKey => $levelvalue) {
					// $levelListTypes[$levelvalue['Collectibletype']['id']] = $levelvalue['Collectibletype']['name'];
				// }

				$returnData['collectibletypes_L' . $key] = $levelTypes;
			}
			$lastValue = $key;
		}
		//finally get the children , if any of the one that was selected.
		$returnChildren = $this -> Collectibletype -> children($collectibleTypeId);
		// $levelListTypes = array();
		// foreach ($returnChildren as $levelKey => $levelvalue) {
			// $levelListTypes[$levelvalue['Collectibletype']['id']] = $levelvalue['Collectibletype']['name'];
		// }

		$returnData['collectibletypes_L' . ++$lastValue] = $returnChildren;

		return $returnData;
	}

}
?>
