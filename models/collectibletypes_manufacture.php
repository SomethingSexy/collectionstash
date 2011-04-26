<?php
class CollectibletypesManufacture extends AppModel {
	var $name = 'CollectibletypesManufacture';
	var $belongsTo = array('Manufacture', 'Collectibletype');
	var $actsAs = array('Containable');

	public function getCollectibleTypeByManufactureId($manufacutre_id) {
		$collectibleTypes = $this -> find("list", array('fields' => array('CollectibletypesManufacture.collectibletype_id', 'Collectibletype.name'), 'conditions' => array('CollectibletypesManufacture.manufacture_id' => $manufacutre_id), 'recursive' => 0));
		debug($collectibleTypes);

		return $collectibleTypes;
	}

}
?>
