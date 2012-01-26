<?php
class LicensesManufacturesSeries extends AppModel {
	var $name = 'LicensesManufacturesSeries';
	var $belongsTo = array('LicensesManufacture', 'Series');
	var $actsAs = array('Containable');



	/**
	 * This method will return whether or not this LicenseManfuacturer has a series
	 */
	public function hasSeries($licenseManufactureId) {
		$count = $this -> find('count', array('conditions' => array('LicensesManufacturesSeries.licenses_manufacture_id' => $licenseManufactureId), 'contain' => false));
		debug($count);
		$hasSeries = false;

		if ($count > 0) {
			$hasSeries = true;
		}

		return $hasSeries;
	}

	

}
?>