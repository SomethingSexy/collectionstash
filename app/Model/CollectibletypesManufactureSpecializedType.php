<?php
//Wow this is the most annoying name ever
class CollectibletypesManufactureSpecializedType extends AppModel {
	var $name = 'CollectibletypesManufactureSpecializedType';
	var $belongsTo = array('CollectibletypesManufacture', 'SpecializedType');
	var $actsAs = array('Containable');

	public function getSpecializedTypes($manufactureId, $collectibleTypeId) {
		$specializedTypesList = array();

		$collectibleTypeManufacturer = $this -> CollectibletypesManufacture -> find("first", array('contain' => false, 'conditions' => array('CollectibletypesManufacture.manufacture_id' => $manufactureId, 'CollectibletypesManufacture.collectibletype_id' => $collectibleTypeId)));
		debug($collectibleTypeManufacturer);
		if (!empty($collectibleTypeManufacturer)) {
			$specializedTypes = $this -> find("all", array('order' => array('SpecializedType.name' => 'ASC'), 'contain'=>'SpecializedType', 'conditions'=> array('CollectibletypesManufactureSpecializedType.collectibletypes_manufacture_id' => $collectibleTypeManufacturer['CollectibletypesManufacture']['id'])));
			debug($specializedTypes);

			foreach ($specializedTypes as $key => $value) {
				$specializedTypesList[$value['SpecializedType']['id']] = $value['SpecializedType']['name'];
			}
			debug($specializedTypesList);
		}

		return $specializedTypesList;
	}

}
?>