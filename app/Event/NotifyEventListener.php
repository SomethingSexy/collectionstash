<?php
App::uses('CakeEventListener', 'Event');
class NotifyEventListener implements CakeEventListener {

	public function implementedEvents() {
		return array('Event.Subscription.trigger' => 'notifyUser',
		);
	}

	/**
	 * This executes anytime that subscription gets triggered and we need to 
	 * notify the user that something happened'
	 * 
	 * This will get passed the user id and the message that we are going to
	 * notify
	 */
	public function notifyUser($event) {
		// $event -> data['userId'];
		// $event -> data['message'];
		
		// We will be loading the Notify model and then updating it
		// I think the subject will be the controller/model whatever the subscription was
		// $event -> subject ->
		
		CakeLog::write('info', 'Notify Bitches');
	}

}
?>