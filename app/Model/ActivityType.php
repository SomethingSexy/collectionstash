<?php
class ActivityType extends AppModel {
	public $name = 'ActivityType';
	public $actsAs = array('Containable');
	public $hasMany = array('Activity');
}
?>
