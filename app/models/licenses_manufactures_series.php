<?php
class LicensesManufacturesSeries extends AppModel {
	var $name = 'LicensesManufacturesSeries';
	var $belongsTo = array('LicensesManufacture', 'Series');
	var $actsAs = array('Containable');

	/**
	 * This will return a list of series that is associated with this licenseManufactureId.
	 *
	 * The return object will be a list with the series id as the key and the series name as the value
	 */
	public function getSeriesByLicenseManufactureId($licenseManufactureId) {
		debug($licenseManufactureId);
		$series = $this -> find('all', array('conditions' => array('LicensesManufacturesSeries.licenses_manufacture_id' => $licenseManufactureId), 'fields' => array('Series.name', 'Series.id')));
		debug($series);
		$seriesList = array();

		foreach ($series as $serie) {
			$seriesList[$serie['Series']['id']] = $serie['Series']['name'];
		}

		return $seriesList;
	}

	/**
	 *
	 */
	public function getSeriesLevels($manufactureId, $licenseId, $seriesId = null) {

		/*
		 * Grab this everytime because we are going to need it
		 */
		$licenseManufacturer = $this -> LicensesManufacture -> getLicenseManufacture($manufactureId, $licenseId);
		debug($licenseManufacturer);
		$returnData = array();
		$returnData['selected'] = array();
		/*
		 * Check to make sure we returned something, or something was found
		 * 
		 * 
		 * We are returning the level count so it is easier for the front end to
		 * handle this
		 */
		if (!is_null($licenseManufacturer) && !empty($licenseManufacturer)) {
			/*
			 * If the series id is null then we want to get the main level
			 */
			if (is_null($seriesId)) {
				$series = $this -> getSeriesByLicenseManufactureId($licenseManufacturer['LicensesManufacture']['id']);
				/*
				 * Since we are returning the top layer, set it as series L0
				 */
				$returnData['L0'] = $series;
				
				$returnData['levelCount'] = 1;
			} else {
				/*
				 * If it is not null then we need to get the level
				 */
				$paths = $this -> Series -> getpath($seriesId);
				$lastKey = 0;
				debug($paths);
				foreach ($paths as $key => $value) {
					$processedSeries = array();
					/*
					 * If there is no parent and in most cases this should be the first one and only
					 * one, just get all of the main level series for this one
					 */
					if (is_null($value['Series']['parent_id'])) {
						$series = $this -> getSeriesByLicenseManufactureId($licenseManufacturer['LicensesManufacture']['id']);
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