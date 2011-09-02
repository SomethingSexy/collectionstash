<?php
class AttributesCollectiblesEdit extends AppModel {
	var $name = 'AttributesCollectiblesEdit';
	//var $useTable = 'accessories_collectibles';
	var $belongsTo = array('AttributesCollectible', 'Attribute');
	var $actsAs = array('Containable');

	function getAddAttribute($attributeEditId, $notes = null) {
		//Grab out edit attribute
		$attributeEditVersion = $this -> find("first", array('conditions' => array('AttributesCollectiblesEdit.id' => $attributeEditId)));
		//reformat it for us, unsetting some stuff we do not need
		$attribute = array();
		$attribute['AttributesCollectible'] = $attributeEditVersion['AttributesCollectiblesEdit'];
		unset($attribute['AttributesCollectible']['id']);
		unset($attribute['AttributesCollectible']['created']);
		unset($attribute['AttributesCollectible']['modified']);
		if (!is_null($notes)) {
			$upload['AttributesCollectible']['notes'] = $notes;
		}
		debug($attribute);
		return $attribute;
	}

	function getUpdateFields($attributeEditId, $notes = null) {
		//Grab out edit collectible
		$attributeEditVersion = $this -> find("first", array('contain' => false, 'conditions' => array('AttributesCollectiblesEdit.id' => $attributeEditId)));
		//reformat it for us, unsetting some stuff we do not need

		$attributeFields = array();
		//At this point this only handles Deletes and Edits, add should not go through here
		if ($attributeEditVersion['AttributesCollectiblesEdit']['action'] === 'D') {
			//For deletes, lets set the status to 0, that means it is not active
			$attributeFields['AttributesCollectible.active'] = '\'0\'';
			$attributeFields['AttributesCollectible.action'] = '\'D\'';
		} else if ($attributeEditVersion['AttributesCollectiblesEdit']['action'] === 'E') {
			//The only thing we can edit right now is the description
			$attributeFields['AttributesCollectible.description'] = '\'' . $attributeEditVersion['AttributesCollectiblesEdit']['description'] . '\'';
			$attributeFields['AttributesCollectible.action'] = '\'E\'';
		}
		if (!is_null($notes)) {
			$upload['AttributesCollectible.notes'] = '\'' . $notes .'\'';
		}
		return $attributeFields;
	}

}
?>
