<?php
class CollectiblesTag extends AppModel {

	var $name = 'CollectiblesTag';
	var $belongsTo = array('Collectible', 'Tag');
	var $actsAs = array('Containable');
	
}
?>