<?php
class License extends AppModel {
	public $name = 'License';
	public $hasMany = array('Collectible', 'LicensesManufacture' => array('dependent' => true));
	public $actsAs = array('Containable');

	public $validate = array(
	//name field
	'name' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Name is required.'), 'maxLength' => array('rule' => array('maxLength', 200), 'message' => 'Invalid length.')));

	public function getLicenses() {
		return $this -> find('all', array('contain' => false, 'order' => array('License.name' => 'ASC')));
	}

	/**
	 * This should be the main find for a manufacturer, it will handle caching eventually.
	 */
	public function findByLicenseId($id) {
		return $this -> find('first', array('conditions' => array('License.id' => $id), 'contain' => false));
	}

}
?>
