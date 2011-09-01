<?php
class AttributesCollectiblesEdit extends AppModel {
	var $name = 'AttributesCollectiblesEdit';
	//var $useTable = 'accessories_collectibles';
	var $belongsTo = array('AttributesCollectible');
	var $actsAs = array('Containable');

}
?>
