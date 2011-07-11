<?php
class Tag extends AppModel {
	var $name = 'Tag';
	var $hasMany = array('CollectiblesTag');   
	var $actsAs = array('Containable');
	
}
?>
