<?php
class LicensesManufacture extends AppModel {
	var $name = 'LicensesManufacture';
	var $belongsTo = array('Manufacture', 'License');
	var $actsAs = array('Containable');
	var $hasMany = array('LicensesManufacturesSeries');
	public function getLicensesByManufactureId($manufactureId) {
		$licenses = $this -> find('all', array('conditions' => array('LicensesManufacture.manufacture_id' => $manufactureId), 'fields' => array('License.name', 'License.id')));
		$licenseList = array();

		foreach($licenses as $license) {
			$licenseList[$license['License']['id']] = $license['License']['name'];
		}

		return $licenseList;
	}
}

?>