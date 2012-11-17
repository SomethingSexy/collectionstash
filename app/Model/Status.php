<?php
class Status extends AppModel {
    var $name = 'Status';
    var $actsAs = array('Containable');
	var $hasMany = array('Attribute');
}
?>
