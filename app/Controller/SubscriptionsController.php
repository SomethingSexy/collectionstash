<?php
class SubscriptionsController extends AppController {
	public $helpers = array('Html', 'Minify', 'Js');

	// public function test() {
	//
	// // $subscriptions = $this -> Subscription -> find("all", array('contain' => 'EntityType', 'joins' => array('table' => 'entity_types', 'alias' => 'EntityType', 'type' => 'inner', 'conditions' => array('Subscription.entity_type_id = EntityType.id'))));
	// $entityType = $this -> Subscription -> EntityType -> find("first", array('contain'=> false, 'conditions' => array('EntityType.type' => 'stash', 'EntityType.type_id' => 1)));
	// debug($entityType);
	// $subscriptions = $this -> Subscription -> find("all", array('contain' => array('User'), 'conditions' => array('Subscription.entity_type_id' => $entityType['EntityType']['id'])));
	// debug($subscriptions);
	// }
	
	public function subscribe(){
		
	}

}
?>