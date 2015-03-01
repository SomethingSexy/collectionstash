<?php
/**
 * This model tells us what entities a user is subscribed to
 *
 * Should we also indicate here, what they are subscribed to? Comments, the entity itself
 *
 * For example
 * 	- If you are subscribed to the Stash, anytime something is added, you will be notified
 * 	- If you are subscribed to a collectible, anytime something is edited with that collectible, you will be notified.
 * 	- Should you be notified of different comments that are made as well?
 *
 * 	- I think if your settings say that you auto suscribe when posting comments, it would just be for the comments so you would suscribe to that entity, but say comments only
 *
 *
 * A subscription will indicate what they are subscribing to, the whole entity, comments or both
 *
 * Because comments don't have their own entity, if someone wants to subscribe to the comments, we will have to code
 * it in a way that they are subscribed to the entity but for the comments only.
 *
 * Then when a comment has been posted for an entity, we will kick off an event and it will check the subcriptions for
 * that entity that are specific to comments or both
 *
 */
class Subscription extends AppModel {
	public $name = 'Subscription';
	public $belongsTo = array('User' => array('fields' => array('id', 'username', 'email')));
	public $hasOne = array('UserSubscription', 'CollectibleSubscription');
	public $actsAs = array('Containable');

	/**
	 * This will add a subscription to the given model, model id and the user who is adding ths subscription
	 */
	public function addSubscription($entityTypeId, $user_id, $subscribed = null) {
		$subscription = array();
		// Doing this here, it really shouln't be a big deal since this will be done by user for their own stuff
		$alreadyExist = $this -> find("first", array('conditions' => array('Subscription.entity_type_id' => $entityTypeId, 'Subscription.user_id' => $user_id)));

		if (!empty($alreadyExist)) {
			$subscription['Subscription']['id'] = $alreadyExist['Subscription']['id'];
		}

		if ($subscribed === null || $subscribed === 'true') {
			$subscription['Subscription']['subscribed'] = 1;
		} else {
			$subscription['Subscription']['subscribed'] = 0;
		}
		$subscription['Subscription']['entity_type_id'] = $entityTypeId;
		$subscription['Subscription']['user_id'] = $user_id;

		if ($this -> save($subscription)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * This will return all subscriptions for a given user
	 */
	public function getSubscriptions($user_id) {
		return $this -> find("list", array('conditions' => array('Subscription.user_id' => $user_id, 'Subscription.subscribed' => 1), 'fields' => array('Subscription.entity_type_id', 'Subscription.id')));
	}

}
?>