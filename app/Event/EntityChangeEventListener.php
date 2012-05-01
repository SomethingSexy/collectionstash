<?php
App::uses('CakeEventListener', 'Event');

class EntityChangeEventListener implements CakeEventListener {

	public function implementedEvents() {
		return array('Model.Stash.Collectible.afterAdd' => 'collectibleAddedToStash',            // assign event to function
		);
	}

	/**
	 * This executes whenever a collectible as been added to someone's stash
	 */
	public function collectibleAddedToStash($event) {
		// $event->subject = the object the event was dispatched from
		// in this example $event->subject = BlogController

		//This is the id of the collectibleuser that was added
		$id = $event -> subject -> CollectiblesUser -> id;
		$stashId = $event -> data['stashId'];
		//Eh, this works
		$event -> subject -> loadModel('Subscription');
		//Grab the Entity Type for this stash
		$entityType = $event -> subject -> Subscription -> EntityType -> find("first", array('contain' => false, 'conditions' => array('EntityType.type' => 'stash', 'EntityType.type_id' => $stashId)));
		//now grab all of the Subscriptions 
		$subscriptions = $event -> subject -> Subscription -> find("all", array('contain' => array('User'), 'conditions' => array('Subscription.entity_type_id' => $entityType['EntityType']['id'])));

		//Now take the stashId, and find all users that are subscribed to it

		CakeLog::write('info', 'Event callback fuck' . $subscriptions[0]['Subscription']['id']);
	}

}
?>