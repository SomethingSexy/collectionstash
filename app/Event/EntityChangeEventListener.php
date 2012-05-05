<?php
App::uses('CakeEventListener', 'Event');

/**
 * I might be able to put this into one Event called notify :)
 */
class EntityChangeEventListener implements CakeEventListener {

	public function implementedEvents() {
		return array('Model.Stash.Collectible.afterAdd' => 'collectibleAddedToStash',                   // assign event to function
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
		$subscriptions = $event -> subject -> Subscription -> find("all", array('contain' => array('User'), 'conditions' => array('Subscription.subscribed'=> 1, 'Subscription.entity_type_id' => $entityType['EntityType']['id'])));

		//Now take the stashId, and find all users that are subscribed to it

		if (!empty($subscriptions)) {
			// Ok now that an event has hired that someone is subscribed to, we need to send notification
			//
			// Right now I am thinking I will update the notification table with
			// the message
			//
			// The notification will have a read flag and a notify flag (the notify flag will be for emails)
			//
			// The notification table will be used in two different spots, it will be used
			// to build the message center
			//
			// It will also be used to kick off emails
			// I think part of this event will
			CakeLog::write('info', 'Event callback fuck' . $subscriptions[0]['Subscription']['id']);
			// This will do a bulk notify
			CakeEventManager::instance() -> dispatch(new CakeEvent('Model.Subscription.notify', $event -> subject, array('subscriptions' => $subscriptions, 'message' => __('User Updated Stash!'))));

		} else {
			CakeLog::write('info', 'No subscriptions');
		}

		// Eh is this the way to do it?
		// We will pass the user and the message

	}

}
?>