<?php
class Point extends AppModel {
	public $name = 'Point';
	public $actsAs = array('Containable');
	public $belongsTo = array('ActivityType');
}
?>
