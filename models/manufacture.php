<?php
class Manufacture extends AppModel {
	var $name = 'Manufacture';
	var $hasMany = array('Collectible' => array('className' => 'Collectible', 'foreignKey' => 'manufacture_id'), 'LicensesManufacture', 'CollectibletypesManufacture');
	var $actsAs = array('Containable');

	public function getManufactureNameById($manufactureId) {
		$manufacture = $this -> find('first', array('conditions' => array('Manufacture.id' => $manufactureId), 'fields' => array('Manufacture.title'), 'contain' => false));
		debug($manufacture);
		return $manufacture['Manufacture']['title'];
	}

	public function getManufactureSearchData() {
		$manufactures = $this -> find("all", array('contain' => false));
		debug($manufactures);
		return $manufactures;
	}

}
?>
