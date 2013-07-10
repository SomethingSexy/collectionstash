<?php
class CollectiblePriceFact extends AppModel {
	public $name = 'CollectiblePriceFact';
	public $hasMany = array('Collectible' => array('dependent' => true));
	public $actsAs = array('Containable');
}
?>
