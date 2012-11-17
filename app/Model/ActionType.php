<?php
class ActionType extends AppModel {
	public $name = 'ActionType';
	public $hasMany = array('Action');
	public $actsAs = array('Containable');
}
?>
