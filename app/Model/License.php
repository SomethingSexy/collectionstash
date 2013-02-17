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

}
?>
