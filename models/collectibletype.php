<?php
class Collectibletype extends AppModel {
	var $name = 'Collectibletype';
	var $hasMany = array('Collectible' => array('className' => 'Collectible', 'foreignKey' => 'collectibletype_id'), 'CollectibletypesManufacture');
	var $actsAs = array('Containable');

	public function getCollectibleTypeSearchData() {
		$collectibleTypes = $this -> find("all", array('contain' => false));
		debug($collectibleTypes);
		return $collectibleTypes;
	}

}
?>
