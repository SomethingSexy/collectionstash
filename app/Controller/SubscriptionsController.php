<?php
/**
 * Subscriptions will turn into friends...or I just remove subscriptions from the UI because I really don't want to support that for now
 * 
 * 
 * Then we will add Favorites...another Stash type maybe? or a new table.  Now that stash has history, I am not sure I want to make favorites a stash type, might get too messy
 */
class SubscriptionsController extends AppController {
	public $helpers = array('Html', 'Minify', 'Js');

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
				if ($subscribed === 'true') {
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