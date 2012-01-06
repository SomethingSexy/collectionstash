<?php
class AttributesCollectiblesEdit extends AppModel {
	var $name = 'AttributesCollectiblesEdit';
	//var $useTable = 'accessories_collectibles';
	var $belongsTo = array('AttributesCollectible', 'Attribute');
	var $actsAs = array('Containable');

	function getUpdateFields($attributeEditId, $notes = null) {
		//Grab out edit collectible
		$attributeEditVersion = $this -> find("first", array('contain' => false, 'conditions' => array('AttributesCollectiblesEdit.id' => $attributeEditId)));
		//reformat it for us, unsetting some stuff we do not need

		$attributeFields = array();
		if ($attributeEditVersion['AttributesCollectiblesEdit']['action'] === 'A') {
			$attributeFields['AttributesCollectible']['description'] = $attributeEditVersion['AttributesCollectiblesEdit']['description'];
			$attributeFields['AttributesCollectible']['active'] = 1;
			$attributeFields['AttributesCollectible']['attribute_id'] = $attributeEditVersion['AttributesCollectiblesEdit']['attribute_id'];
			$attributeFields['AttributesCollectible']['collectible_id'] = $attributeEditVersion['AttributesCollectiblesEdit']['collectible_id'];
			$attributeFields['Revision']['action'] = 'A';
		} else if ($attributeEditVersion['AttributesCollectiblesEdit']['action'] === 'D') {
			//For deletes, lets set the status to 0, that means it is not active
			$attributeFields['AttributesCollectible']['active'] = 0;
			$attributeFields['AttributesCollectible']['id'] = $attributeEditVersion['AttributesCollectiblesEdit']['attributes_collectible_id'];
			$attributeFields['Revision']['action'] = 'D';
		} else if ($attributeEditVersion['AttributesCollectiblesEdit']['action'] === 'E') {
			//The only thing we can edit right now is the description
			$attributeFields['AttributesCollectible']['description'] = $attributeEditVersion['AttributesCollectiblesEdit']['description'];
			$attributeFields['AttributesCollectible']['id'] = $attributeEditVersion['AttributesCollectiblesEdit']['attributes_collectible_id'];
			$attributeFields['Revision']['action'] = 'E';
		}

		if (!is_null($notes)) {
			$attributeFields['Revision']['notes'] = $notes;
		}
		//Make sure I grab the user id that did this edit
		$attributeFields['Revision']['user_id'] = $attributeEditVersion['AttributesCollectiblesEdit']['edit_user_id'];
		return $attributeFields;
	}

}
?>
