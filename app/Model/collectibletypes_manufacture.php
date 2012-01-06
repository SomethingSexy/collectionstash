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

	public function getAllCollectibleTypeByManufactureId($manufacutre_id) {
		$manColTypes = $this -> find("all", array('fields' => array('CollectibletypesManufacture.collectibletype_id', 'Collectibletype.name', 'Collectibletype.id'), 'conditions' => array('CollectibletypesManufacture.manufacture_id' => $manufacutre_id), 'recursive' => 0, 'order' => array('Collectibletype.name' => 'ASC')));
		return $manColTypes;
	}
	/**
	 * This method will return all of the valid children for the given collectible type and manufacturer.
	 * 
	 * It will make sure that all of the children of this collectible type are valid for this manufacturer
	 */
	public function getCollectibleTypesChildren($manufactureId, $collectibleTypeId) {
		$manSpecificTypes = $this -> getAllCollectibleTypeByManufactureId($manufactureId);
		$collectibleTypes = $this -> Collectibletype -> children($collectibleTypeId, true, array('Collectibletype.id', 'Collectibletype.name'));
		$processedReturnChildren = array();
		//Loop through all of these children and make sure they are valid, there HAS to be a better way to do this
		foreach ($collectibleTypes as $key => $collectibleType) {	
			$typeId = $collectibleType['Collectibletype']['id'];	
			$isValid = false;
			foreach ($manSpecificTypes as $key => $manType) {
				if($manType['Collectibletype']['id'] === $typeId) {
					$isValid = true;
				}	
			}
			
			if($isValid){
				array_push($processedReturnChildren, $collectibleType);
			}
		}
		
		return $processedReturnChildren;
	}

	/**
	 * This will return arrays of each level of collectibles types from the path of the given collectible type id.  It
	 *  will also return the collectible that is selected in the array
	 */
	public function getCollectibleTypesPaths($manufactureId, $collectibleTypeId) {
		//Grab all of the manufacture specific types	
		$manSpecificTypes = $this -> getAllCollectibleTypeByManufactureId($manufactureId);
	
		//This will get all main level collectible types for this manufacture
		$collectibletypes = $this -> getCollectibleTypeByManufactureId($manufactureId);
		//Set the baseline as L0, there should ALWAYS be a L0.
		$returnData['collectibletypes_L0'] = $collectibletypes;

		$returnData['selectedTypes'] = array();
		//Now check for the path of this collectible type, we do this to get all of the potential paths we want to, to select this collectible
		//Right now we are assuming that when this manufacturer was setup, that if they added a child type, the parent type was added
		$paths = $this -> Collectibletype -> getPath($collectibleTypeId);
		$lastValue = 0;
		//This lists out paths in order, the first being the high level
		//Loop through each path, and grab the parent of the children to get that data, we will add subsequent children as L1, L2, L3.
		foreach ($paths as $key => $value) {
			$returnData['selectedTypes']['L' . $key] = $value['Collectibletype']['id'];
			//We already grabbed the base line types
			if (!is_null($value['Collectibletype']['parent_id'])) {
				$levelTypes = $this -> Collectibletype -> children($value['Collectibletype']['parent_id']);
				$levelListTypes = array();
				$returnData['collectibletypes_L' . $key] = $levelTypes;
			}
			$lastValue = $key;
		}
		//finally get the children , if any of the one that was selected.
		$returnChildren = $this -> Collectibletype -> children($collectibleTypeId);
		$processedReturnChildren = array();
		//Loop through all of these children and make sure they are valid, there HAS to be a better way to do this
		foreach ($returnChildren as $key => $collectibleType) {	
			$typeId = $collectibleType['Collectibletype']['id'];	
			$isValid = false;
			foreach ($manSpecificTypes as $key => $manType) {
				if($manType['Collectibletype']['id'] === $typeId) {
					$isValid = true;
				}	
			}
			
			if($isValid){
				array_push($processedReturnChildren, $collectibleType);
			}
		}
		$returnData['collectibletypes_L' . ++$lastValue] = $processedReturnChildren;

		return $returnData;
	}

	/**
	 * This method will return the number of collectible types that this manufacture has.
	 */
	public function getCollectibletypeCount($manufactureId) {
		$collectibletypeCount = $this -> find("count", array('conditions' => array('Manufacture.id' => $manufactureId)));
		return $collectibletypeCount;
	}

}
?>
