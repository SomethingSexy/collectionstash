<?php
class Collectibletype extends AppModel {
	var $name = 'Collectibletype';
	var $hasMany = array('Collectible' => array('className' => 'Collectible', 'foreignKey' => 'collectibletype_id'), 'CollectibletypesManufacture');
	var $actsAs = array('Tree', 'Containable');

	public $validate = array(
	//name field
	'name' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Name is required.'), 'maxLength' => array('rule' => array('maxLength', 200), 'message' => 'Invalid length.')));

	public function getCollectibleTypeSearchData() {
		$collectibleTypes = $this -> find("all", array('order' => array('Collectibletype.name' => 'ASC'), 'contain' => false));
		return $collectibleTypes;
	}

	public function getListTopLevelTypes() {
		$collectibleTypes = $this -> find("list", array('conditions' => array('Collectibletype.parent_id' => null)));
		return $collectibleTypes;
	}

}
?>
