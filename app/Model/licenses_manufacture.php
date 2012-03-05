<?php
class LicensesManufacture extends AppModel {
	public $name = 'LicensesManufacture';
	public $belongsTo = array('Manufacture', 'License');
	public $actsAs = array('Containable');

	/**
	 * This returns a "list" of licenses by manufacturer, so the key is the id
	 */
	public function getLicensesByManufactureId($manufactureId) {
		$licenses = $this -> find('all', array('conditions' => array('LicensesManufacture.manufacture_id' => $manufactureId), 'fields' => array('License.name', 'License.id'), 'order' => array('License.name' => 'ASC')));
		$licenseList = array();

		foreach ($licenses as $license) {
			$licenseList[$license['License']['id']] = $license['License']['name'];
		}

		return $licenseList;
	}

	/**
	 * This returns the full license object
	 */
	public function getFullLicensesByManufactureId($manufactureId) {
		$licenses = $this -> find('all', array('contain' => array('License'), 'conditions' => array('LicensesManufacture.manufacture_id' => $manufactureId), 'order' => array('License.name' => 'ASC')));

		return $licenses;
	}

	/**
	 * Given a manufactureId and a license Id, this method returns the LicenseManufacture object
	 */
	public function getLicenseManufacture($manufactureId, $licenseId) {
		return $this -> find("first", array('conditions' => array('LicensesManufacture.manufacture_id' => $manufactureId, 'LicensesManufacture.license_id' => $licenseId), 'contain' => false));
	}

	/**
	 * This method will return a count of the licenses that the given
	 * manufacture has.
	 */
	public function getLicenseCount($manufactureId) {
		$licenseCount = $this -> find("count", array('conditions' => array('Manufacture.id' => $manufactureId)));
		return $licenseCount;
	}

	/**
	 * This method returns all licenses that are not currently associated with the given manufacture
	 */
	public function getLicensesNotAssMan($manufactureId) {
		return $this -> License -> find('all', array('order' => array('License.name' => 'ASC'), 'contain' => array('LicensesManufacture'), 'conditions' => array('not exists ' . '(SELECT *
			FROM licenses_manufactures
			WHERE licenses_manufactures.license_id = license.id
			AND licenses_manufactures.manufacture_id =' . $manufactureId . ')')));
	}

}
?>