<?php
App::uses('CakeEventListener', 'Event');

/**
 * I might be able to put this into one Event called notify :)
 */
class EntityChangeEventListener implements CakeEventListener {

	public function implementedEvents() {
		return array('Controller.Stash.Collectible.add' => 'collectibleAddedToStash', 'Controller.Comment.add' => 'commentAdded');
	}

	public function commentAdded($event) {
		//This is the entity type id
		$entityTypeId = $event -> data['entityTypeId'];
		//this is the id of the user who posted the comment
		$userId = $event -> data['userId'];
		$event -> subject -> loadModel('Subscription');
		$entityType = $event -> subject -> Subscription -> EntityType -> getEntityCore($entityTypeId);
		$subscriptions = $event -> subject -> Subscription -> find("all", array('contain' => array('User'), 'conditions' => array('Subscription.subscribed' => 1, 'Subscription.entity_type_id' => $entityTypeId)));
		if (!empty($subscriptions)) {

			foreach ($subscriptions as $key => $subscription) {
				//If the subscription is the same as the owner of the stash, unset it
				if ($subscription['Subscription']['user_id'] === $userId) {
					unset($subscriptions[$key]);
				}
			}

			/*
			 * Loop through all and remove notification if they posted the comment
			 */
			if ($entityType['EntityType']['type'] === 'stash') {

			}

			CakeEventManager::instance() -> dispatch(new CakeEvent('Model.Subscription.notify', $event -> subject, array('subscriptions' => $subscriptions, 'message' => __('Comment added!'))));
		} else {
			CakeLog::write('info', 'No subscriptions');
		}
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
		// Grab the stash
		$stash = $event -> subject -> Subscription -> EntityType -> Stash -> find("first", array('contain' => 'User', 'conditions' => array('Stash.id' => $stashId)));
		//now grab all of the Subscriptions
		$subscriptions = $event -> subject -> Subscription -> find("all", array('contain' => array('User'), 'conditions' => array('Subscription.subscribed' => 1, 'Subscription.entity_type_id' => $stash['Stash']['entity_type_id'])));

		if (!empty($subscriptions)) {
			//Build the message
			$message = $stash['User']['username'];
			$message .= __(' has added a new collectible to their stash!');

			foreach ($subscriptions as $key => $subscription) {
				//If the subscription is the same as the owner of the stash, unset it
				if ($subscription['Subscription']['user_id'] === $stash['Stash']['user_id']) {
					unset($subscriptions[$key]);
				}
			}

			// Since I auto subscribe the user to thier own stash, I don't want to send an email when they add a collectible

			// Ok now that an event has fired that someone is subscribed to, we need to send notification
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
			CakeEventManager::instance() -> dispatch(new CakeEvent('Model.Subscription.notify', $event -> subject, array('subscriptions' => $subscriptions, 'message' => $message)));

		} else {
			CakeLog::write('info', 'No subscriptions');
		}
	}

}
?>