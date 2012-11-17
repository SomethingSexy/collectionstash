<?php
class Action extends AppModel {
	public $name = 'Action';
	public $belongsTo = array('ActionType');
	public $actsAs = array('Containable');
}
?>
