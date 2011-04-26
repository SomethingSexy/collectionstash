<?php
class AttributesCollectible extends AppModel {
	var $name = 'AttributesCollectible';
	//var $useTable = 'accessories_collectibles';
	var $belongsTo = array('Attribute', 'Collectible');
	var $actsAs = array('Containable');
}
?>
