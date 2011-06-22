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

	public function getManufactureData($manufactureId) {
		$returnData = array();

		$manufactures = $this -> find('list', array('order' => array('Manufacture.title' => 'ASC')));
		//Grab all licenses for this manufacture
		$licenses = $this -> LicensesManufacture -> getLicensesByManufactureId($manufactureId);
		reset($licenses);
		$this -> set(compact('licenses'));

		//grab all collectible types for this manufacture
		$collectibletypes = $this -> CollectibletypesManufacture -> getCollectibleTypeByManufactureId($manufactureId);
		$returnData['manufactures'] = $manufactures;
		$returnData['licenses'] = $licenses;
		// $returnData['series'] = $series;
		$returnData['collectibletypes'] = $collectibletypes;

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
