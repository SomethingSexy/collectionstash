<?php
class Manufacture extends AppModel {
	var $name = 'Manufacture';
	var $hasMany = array('Collectible' => array('className' => 'Collectible', 'foreignKey' => 'manufacture_id'), 'LicensesManufacture', 'CollectibletypesManufacture');
	var $actsAs = array('Containable');

	public function getManufactureList() {
		return $this -> find('list', array('order' => array('Manufacture.title' => 'ASC')));
	}

	public function getManufactures() {
		return $this -> find('all', array('contain' => false, 'order' => array('Manufacture.title' => 'ASC')));
	}

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

	public function getManufactureData($manufactureId, $collectibleTypeId = null) {
		$returnData = array();

		$manufactures = $this -> find('list', array('order' => array('Manufacture.title' => 'ASC')));
		//Grab all licenses for this manufacture
		$licenses = $this -> LicensesManufacture -> getLicensesByManufactureId($manufactureId);
		reset($licenses);
		$this -> set(compact('licenses'));

		//grab all collectible types for this manufacture

		//This will get all main level collectible types for this manufacture
		$collectibletypes = $this -> CollectibletypesManufacture -> getCollectibleTypeByManufactureId($manufactureId);
		//Update this to say collectibletypes_level1
		$returnData['collectibletypes'] = $collectibletypes;
		
		if (!is_null($collectibleTypeId)) {
			$returnData['selectedTypes'] = array();
			//If we have a collectible type set, then we need to find the path for that type, and make sure that
			//we return the main list and what is set
			//TODO: At some point this should be recusrive, but this might actually be pretty automated to handle all sorts of levels in the future
			//Loop through each level, and get the children of that level.
			//We will store these as collectibletypes_level1, .._level2, and so on
			//We will also store what id of that level should be selected so we know how to go back
			$paths = $this -> CollectibletypesManufacture -> Collectibletype -> getpath($collectibleTypeId);
			debug($paths);
			//This lists out paths in order, the first being the high level
			foreach ($paths as $key => $value) {
				$returnData['selectedTypes']['L'.$key] = $value['Collectibletype']['id'];
				if (!is_null($value['Collectibletype']['parent_id'])) {
					$levelTypes = $this -> CollectibletypesManufacture -> Collectibletype -> children($value['Collectibletype']['parent_id']);
					$levelListTypes = array();
					foreach ($levelTypes as $levelKey => $levelvalue) {
						$levelListTypes[$levelKey] = $levelvalue['Collectibletype']['name'];
					}
					
					$returnData['collectibletypes_L'.$key] = $levelListTypes;
				}
			}
			
		}

		$returnData['manufactures'] = $manufactures;
		$returnData['licenses'] = $licenses;
		// $returnData['series'] = $series;
		debug($returnData);
		return $returnData;
	}

	public function getManufactureListData() {
		$returnData = array();
		$manufactures = $this -> find('list', array('order' => array('Manufacture.title' => 'ASC')));
		reset($manufactures);
		//Safety - sets pointer to top of array
		$firstMan = key($manufactures);
		// Returns the first key of it
		$licenses = $this -> LicensesManufacture -> getLicensesByManufactureId($firstMan);
		reset($licenses);
		$firstLic = key($licenses);
		$series = $this -> LicensesManufacture -> LicensesManufacturesSeries -> getSeriesByLicenseManufactureId($firstLic);
		$collectibletypes = $this -> CollectibletypesManufacture -> getCollectibleTypeByManufactureId($firstMan);

		$returnData['manufactures'] = $manufactures;
		$returnData['licenses'] = $licenses;
		$returnData['series'] = $series;
		$returnData['collectibletypes'] = $collectibletypes;

		return $returnData;
	}

}
?>
