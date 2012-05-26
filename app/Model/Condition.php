<?php
class Condition extends AppModel {
	var $name = 'Condition';
	var $hasMany = array('CollectiblesUser' => array('className' => 'CollectiblesUser', 'foreignKey' => 'condition_id'));
	var $actsAs = array('Containable');
}
?>
