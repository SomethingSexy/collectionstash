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
		$manColTypes = $this -> find("list", array('fields' => array('CollectibletypesManufacture.collectibletype_id', 'Collectibletype.name'), 'conditions' => array('CollectibletypesManufacture.manufacture_id' => $manufacutre_id), 'recursive' => 0, 'order'=>array('Collectibletype.name'=>'ASC')));
		//Now filter these, might be better to update this to some sort of join/in query
		$collectibleTypes = array();
		foreach ($manColTypes as $key => $value) {
			if (array_key_exists($key,$topLevelCollectibleTypes)){
				$collectibleTypes[$key] = $value;		
			}	
		}

		return $collectibleTypes;
	}

}
?>
