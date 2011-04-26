<?php
class License extends AppModel {
	var $name = 'License';
	var $hasMany = array('Collectible' => array('className' => 'Collectible', 'foreignKey' => 'license_id'), 'LicensesManufacture');
	var $actsAs = array('Containable');
}
?>
