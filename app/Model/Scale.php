<?php
class Scale extends AppModel {
	var $name = 'Scale';
	var $hasMany = array('Collectible');
	var $actsAs = array('Containable');
}
?>
