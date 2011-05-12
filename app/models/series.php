<?php
class Series extends AppModel {
	var $name = 'Series';
	var $useTable = 'series';
	var $hasMany = array('Collectible' => array('className' => 'Collectible', 'foreignKey' => 'license_id'), 'LicensesManufacturesSeries');
	var $actsAs = array('Tree','Containable');
}
?>
