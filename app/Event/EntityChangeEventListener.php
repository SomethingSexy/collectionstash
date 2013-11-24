<?php
App::uses('CakeEventListener', 'Event');

/**
 * I might be able to put this into one Event called notify :)
 *
 * If this takes off and starts to be slow, it might be better to write all events
 * to a file or in memory and then have a cronjob run every so often to process
 */
class EntityChangeEventListener implements CakeEventListener {

	public function implementedEvents() {
		return array('Controller.Stash.Collectible.add' => 'collectibleAddedToStash', 'Controller.Comment.add' => 'commentAdded', 'Controller.Attribute.approve' => 'attributeApprove');
	}

	/**
	 * This will handle whenever a comment is added to an entity
	 */
	public function commentAdded($event) {
		//This is the entity type id
		$entityTypeId = $event -> data['entityTypeId'];
		//this is the id of the user who posted the comment
		$userId = $event -> data['userId'];
		$event -> subject -> loadModel('Subscription');
		//This will also return the model that the entity is for
		$entityType = $event -> subject -> Subscription -> EntityType -> getEntityCore($entityTypeId);
		$subscriptions = $event -> subject -> Subscription -> find("all", array('contain' => array('User'), 'conditions' => array('Subscription.subscribed' => 1, 'Subscription.entity_type_id' => $entityTypeId)));

		$message = __('A new comment has been posted to ');
		if ($entityType['EntityType']['type'] === 'stash') {
			$message .= $entityType['Stash']['User']['username'] . '\'s stash.';
		} else if ($entityType['EntityType']['type'] === 'collectible') {
			$message .= 'the collectible ' . $entityType['Collectible']['name'] . '.';
		}

		foreach ($subscriptions as $key => $subscription) {
			//If the subscription is the same as the owner of the stash, unset it
			if ($subscription['Subscription']['user_id'] === $userId) {
				unset($subscriptions[$key]);
			} else {
				if ($entityType['EntityType']['type'] === 'stash' && $entityType['Stash']['User']['id'] === $subscription['Subscription']['user_id']) {
					$message = __('A new comment has been posted to your Stash!');
				}

				$subscriptions[$key]['Subscription']['message'] = $message;
				$subscriptions[$key]['Subscription']['subject'] = __('A new coment has been posted.');
				//$subscriptions[$key]['Subscription']['notification_type'] = 'comment_add';
				//$subscriptions[$key]['Subscription']['notification_json_data'] = $templateData;
			}
		}

		if (!empty($subscriptions)) {
			CakeEventManager::instance() -> dispatch(new CakeEvent('Controller.Subscription.notify', $event -> subject, array('subscriptions' => $subscriptions)));
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
		$id = $event -> subject -> id;

		$stashId = $event -> data['stashId'];
		$collectibleUserId = $event -> data['collectibleUserId'];
		// Grab the stash
		$stash = $event -> subject -> User -> Subscription -> EntityType -> Stash -> find("first", array('contain' => array('User'), 'conditions' => array('Stash.id' => $stashId)));

		$collectibleUser = $event -> subject -> find('first', array('conditions' => array('CollectiblesUser.id' => $collectibleUserId), 'contain' => array('Condition', 'Merchant', 'Collectible' => array('Collectibletype', 'Manufacture', 'ArtistsCollectible' => array('Artist'), 'CollectiblesUpload' => array('Upload')))));
		$templateData = json_encode($collectibleUser);
		//now grab all of the Subscriptions
		$subscriptions = $event -> subject -> User -> Subscription -> find("all", array('contain' => array('User'), 'conditions' => array('Subscription.subscribed' => '1', 'Subscription.entity_type_id' => $stash['Stash']['entity_type_id'])));

		//Build the message
		$message = $stash['User']['username'];
		$message .= __(' has added the following collectible to their <a href="' . Configure::read('Settings.domain') . '/stash/' . $stash['User']['username'] . '">stash</a>.');

		foreach ($subscriptions as $key => $subscription) {
			//If the subscription is the same as the owner of the stash, unset it
			if ($subscription['Subscription']['user_id'] === $stash['Stash']['user_id']) {
				unset($subscriptions[$key]);
			} else {
				$subscriptions[$key]['Subscription']['message'] = $message;
				$subscriptions[$key]['Subscription']['subject'] = __($stash['User']['username'] . ' updated their stash.');
				$subscriptions[$key]['Subscription']['notification_type'] = 'stash_add';
				$subscriptions[$key]['Subscription']['notification_json_data'] = $templateData;
			}

		}
		CakeLog::write('info', count($subscriptions));
		if (!empty($subscriptions)) {
			CakeEventManager::instance() -> dispatch(new CakeEvent('Model.Subscription.notify', $event -> subject, array('subscriptions' => $subscriptions)));
		} else {
			CakeLog::write('info', 'No subscriptions');
		}
	}

	public function attributeApprove($event) {

	}

}
?>