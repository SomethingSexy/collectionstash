<?php
class Manufacture extends AppModel {
	public $name = 'Manufacture';
	public $belongsTo = array('Series');
	public $hasMany = array('Collectible' => array('className' => 'Collectible', 'foreignKey' => 'manufacture_id'), 'LicensesManufacture', 'CollectibletypesManufacture');
	public $actsAs = array('Containable');

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
		$manufactures = $this -> find("all", array('order' => array('Manufacture.title' => 'ASC'), 'contain' => false));
		debug($manufactures);
		return $manufactures;
	}
	//Commented out 1/18/12 - not sure it is being used anymore
	// public function getManufactureListData() {
		// $returnData = array();
		// $manufactures = $this -> find('list', array('order' => array('Manufacture.title' => 'ASC')));
		// reset($manufactures);
		// //Safety - sets pointer to top of array
		// $firstMan = key($manufactures);
		// // Returns the first key of it
		// $licenses = $this -> LicensesManufacture -> getLicensesByManufactureId($firstMan);
		// reset($licenses);
		// $firstLic = key($licenses);
		// $series = $this -> LicensesManufacture -> LicensesManufacturesSeries -> getSeriesByLicenseManufactureId($firstLic);
		// $collectibletypes = $this -> CollectibletypesManufacture -> getCollectibleTypeByManufactureId($firstMan);
// 
		// $returnData['manufactures'] = $manufactures;
		// $returnData['licenses'] = $licenses;
		// $returnData['series'] = $series;
		// $returnData['collectibletypes'] = $collectibletypes;
// 
		// return $returnData;
	// }

	/**
	 * Given a manufactureId and a license Id, this method returns all of the series
	 * for that combination
	 */
	public function getSeries($manufactureId) {
		$series = $this -> find('all', array('conditions' => array('Manufacture.id' => $manufactureId), 'contain' => array('Series'), 'fields' => array('Series.name', 'Series.id')));
		
		$seriesList = array();

		foreach ($series as $serie) {
			$seriesList[$serie['Series']['id']] = $serie['Series']['name'];
		}

		return $seriesList;
	}

	/**
	 *
	 */
	public function getSeriesLevels($manufactureId, $seriesId = null) {

		/*
		 * Grab this everytime because we are going to need it
		 */
		// $licenseManufacturer = $this -> LicensesManufacture -> getLicenseManufacture($manufactureId, $licenseId);
		// debug($licenseManufacturer);
		$returnData = array();
		$returnData['selected'] = array();
		/*
		 * Check to make sure we returned something, or something was found
		 *
		 *
		 * We are returning the level count so it is easier for the front end to
		 * handle this
		 */
		if (!is_null($manufactureId) && !empty($manufactureId)) {
			/*
			 * If the series id is null then we want to get the main level
			 */
			if (is_null($seriesId)) {
				$series = $this -> getSeries($manufactureId);
				/*
				 * Since we are returning the top layer, set it as series L0
				 */
				$returnData['L0'] = $series;

				$returnData['levelCount'] = 1;
			} else {
				/*
				 * If it is not null then we need to get the level
				 */
				debug($seriesId);
				$paths = $this -> Series -> getPath($seriesId, array(), true);
				$lastKey = 0;
				debug($paths);
				foreach ($paths as $key => $value) {
					$processedSeries = array();
					/*
					 * If there is no parent and in most cases this should be the first one and only
					 * one, just get all of the main level series for this one
					 */
					if (is_null($value['Series']['parent_id'])) {
						$series = $this -> getSeries($manufactureId);
						$processedSeries = $series;
					} else {
						/*
						 * If it not, then we are going to grab the parent_id and grab all of the children
						 * of that one to grab that level.
						 */
						$series = $this -> Series -> children($value['Series']['parent_id'], true);
						$processedSeries = $this -> processSeries($series);
					}
					debug($processedSeries);
					$returnData['selected']['L' . $key] = $value['Series']['id'];
					$returnData['L' . $key] = $processedSeries;
					$lastKey = $key;
				}

				/*
				 * Ok so at this point, we have one selected, we retrieved all parent levels now we need to see if the one selected
				 * has any children to return
				 */
				$series = $this -> Series -> children($seriesId, true);
				debug($series);
				if (!empty($series)) {
					$processedChildrenSeries = $this -> processSeries($series);
					$returnData['L' . ++$lastKey] = $processedChildrenSeries;
				}
				//To get the true, count we need to add one more since it is
				//using array index
				$returnData['levelCount'] = ++$lastKey;
			}
		}

		return $returnData;

	}

	private function processSeries($series) {
		$processedSeries = array();
		foreach ($series as $key => $value) {
			$processedSeries[$value['Series']['id']] = $value['Series']['name'];
		}
		return $processedSeries;
	}

}
?>
