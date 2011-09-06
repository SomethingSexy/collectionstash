<?php
class CollectiblesTag extends AppModel {

	var $name = 'CollectiblesTag';
	var $belongsTo = array('Collectible', 'Tag' => array('counterCache' => true));
	var $actsAs = array('Containable');

}
?>