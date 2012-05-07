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

	public function subscribe() {
		$data = array();
		//must be logged in to post comment
		if (!$this -> isLoggedIn()) {
			$data['success'] = array('isSuccess' => false);
			$data['error']['message'] = __('You must be logged in to post a comment.');
			$this -> set('subscribe', $data);
			return;
		}
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$entityTypeId = $this -> request -> data['Subscription']['entity_type_id'];
			$subscribed = $this -> request -> data['Subscription']['subscribed'];
			$userId = $this -> getUserId();

			if ($this -> Subscription -> addSubscription($entityTypeId, $userId, $subscribed)) {
				$subscriptions = $this -> getSubscriptions();
				if($subscribed === 'true') {
					// When you log in, it is pulling in the id of the subscription as the value
					// Not sure it really matters
					$subscriptions[$entityTypeId] = $this -> Subscription -> id;		
				} else {
					unset($subscriptions[$entityTypeId]);	
				}
				
				$this -> Session -> write('subscriptions', $subscriptions);
				
				$data['success'] = array('isSuccess' => true);
				$this -> set('subscribe', $data);
				return;
			} else {
				$data['success'] = array('isSuccess' => false);
				$data['error'] = array('message', __('Invalid request.'));
				$this -> set('subscribe', $data);
				return;
			}

		} else {
			$data['success'] = array('isSuccess' => false);
			$data['error'] = array('message', __('Invalid request.'));
			$this -> set('subscribe', $data);
			return;
		}
	}

}
?>