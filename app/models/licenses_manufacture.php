<?php
class LicensesManufacture extends AppModel {
	var $name = 'LicensesManufacture';
	var $belongsTo = array('Manufacture', 'License');
	var $actsAs = array('Containable');
	var $hasMany = array('LicensesManufacturesSeries');
	
	/**
	 * This returns a "list" of licenses by manufacturer, so the key is the id
	 */
	public function getLicensesByManufactureId($manufactureId) {
		$licenses = $this -> find('all', array('conditions' => array('LicensesManufacture.manufacture_id' => $manufactureId), 'fields' => array('License.name', 'License.id'), 'order'=>array('License.name'=>'ASC')));
		$licenseList = array();

		foreach($licenses as $license) {
			$licenseList[$license['License']['id']] = $license['License']['name'];
		}

		return $licenseList;
	}
	/**
	 * This returns the full license object
	 */
	public function getFullLicensesByManufactureId($manufactureId) {
		$licenses = $this -> find('all', array('contain'=> array('License'), 'conditions' => array('LicensesManufacture.manufacture_id' => $manufactureId), 'fields' => array('License.name', 'License.id'), 'order'=>array('License.name'=>'ASC')));

		return $licenses;
	}	
	
	public function getSeries($manufactureId, $licenseId) {
		$license = $this -> find("first", array('conditions' => array('LicensesManufacture.manufacture_id' => $manufactureId, 'LicensesManufacture.license_id' => $licenseId), 'order'=>array('License.name'=>'ASC')));
		debug($license);
		//Grab all series for this license...should I just return all for all licenses and send that down the request?
		$series = $this -> LicensesManufacturesSeries -> getSeriesByLicenseManufactureId($license['LicensesManufacture']['id']);
		debug($series);		
		
		return $series;
	}
	/**
	 * This method will return a count of the licenses that the given
	 * manufacture has.
	 */
	public function getLicenseCount($manufactureId){
		$licenseCount = $this -> find("count", array('conditions'=>array('Manufacture.id' => $manufactureId)));
		return $licenseCount;
	}
}

?>