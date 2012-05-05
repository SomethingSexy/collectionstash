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
			$type = $this -> request -> data['Subscribe']['type'];
			$typeId = $this -> request -> data['Subscribe']['type_id'];
			$userId = $this -> getUserId();

			if ($this -> Subscription -> addSubscription($type, $typeId, $userId)) {

			} else {

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