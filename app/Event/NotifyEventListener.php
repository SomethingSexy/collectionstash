<?php
App::uses('CakeEventListener', 'Event');
class NotifyEventListener implements CakeEventListener {

	public function implementedEvents() {
		return array('Model.Subscription.notify' => 'notifyUser', );
	}

	/**
	 * This executes anytime that subscription gets triggered and we need to
	 * notify the user that something happened'
	 *
	 * This will get passed the user id and the message that we are going to
	 * notify
	 */
	public function notifyUser($event) {
		$subscriptions = $event -> data['subscriptions'];
		$message = $event -> data['message'];

		// We will be loading the Notify model and then updating it
		// I think the subject will be the controller/model whatever the subscription was
		// $event -> subject ->

		$event -> subject -> loadModel('Notification');

		// This could kick off a shit ton of events, depending on how many people.
		// We might want to do a bulk notify
		$data = array();
		foreach ($subscriptions as $key => $subscription) {
			array_push($data, array('user_id'=> $subscription['Subscription']['user_id'], 'message'=> $message));
		}

		$event -> subject -> Notification -> saveAll($data);

		CakeLog::write('info', 'Notify Bitches');
	}

}
?>