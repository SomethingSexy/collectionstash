<?php
class Merchant extends AppModel {
	var $name = 'Merchant';
	var $hasMany = array('CollectiblesUser' => array('className' => 'CollectiblesUser', 'foreignKey' => 'merchant_id'));
	var $actsAs = array('Containable');
}
?>
