<?php
class Series extends AppModel {
	var $name = 'Series';
	var $useTable = 'series';
	var $hasMany = array('Collectible', 'LicensesManufacturesSeries');
	var $actsAs = array('Tree','Containable');
	
	

}
?>
