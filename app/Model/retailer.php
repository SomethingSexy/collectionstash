<?php
class Retailer extends AppModel {
	var $name = 'Retailer';
	var $hasMany = array('Collectible');
	var $actsAs = array('Containable');
	
	public function getRetailerList() {
		return $this -> find('list', array('order' => array('Retailer.name' => 'ASC')));
	}
}
?>
