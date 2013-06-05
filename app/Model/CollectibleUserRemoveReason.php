<?php
class CollectibleUserRemoveReason extends AppModel {
	public $name = 'CollectibleUserRemoveReason';
	public $actsAs = array('Containable');
	public $hasMany = array('Activity');
}
?>
