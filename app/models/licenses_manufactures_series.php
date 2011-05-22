<?php
class LicensesManufacturesSeries extends AppModel {
	var $name = 'LicensesManufacturesSeries';
	var $belongsTo = array('LicensesManufacture', 'Series');
	var $actsAs = array('Containable');

	public function getSeriesByLicenseManufactureId($licenseManufactureId) {
		debug($licenseManufactureId);	
		$series = $this -> find('all', array('conditions' => array('LicensesManufacturesSeries.licenses_manufacture_id' => $licenseManufactureId), 'fields' => array('Series.name', 'Series.id')));
		debug($series);
		$seriesList = array();

		foreach($series as $serie) {
			$seriesList[$serie['Series']['id']] = $serie['Series']['name'];
		}

		return $seriesList;
	}
	

}
?>