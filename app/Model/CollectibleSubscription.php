<?php
class CollectibleSubscription extends AppModel {
	public $name = 'CollectibleSubscription';
	public $belongsTo = array('Collectible', 'Subscription');
	public $actsAs = array('Containable');
}
?>