<?php
App::uses('CakeEventListener', 'Event');
App::uses('View', 'View');
/**
 * Because we want the notification and the email to look exactly the same, or at least have the same content.  We need to generate
 * the main content in the notification itself.  They can't use the same layout but they can use the same content.  We can pregen
 */
class NotifyEventListener implements CakeEventListener {

	public function implementedEvents() {
		return array('Controller.Subscription.notify' => 'notifyUserController', 'Model.Subscription.notify' => 'notifyUserModel');
	}

	/**
	 * This executes anytime that subscription gets triggered and we need to
	 * notify the user that something happened'
	 *
	 * This will get passed the user id and the message that we are going to
	 * notify
	 */
	public function notifyUserController($event) {
		$subscriptions = $event -> data['subscriptions'];

		// We will be loading the Notify model and then updating it
		// I think the subject will be the controller/model whatever the subscription was
		// $event -> subject ->
		$event -> subject -> loadModel('Notification');

		// This could kick off a shit ton of events, depending on how many people.
		// We might want to do a bulk notify
		$data = array();
		foreach ($subscriptions as $key => $subscription) {
			array_push($data, array('user_id' => $subscription['Subscription']['user_id'], 'message' => $subscription['Subscription']['message'], 'subject' => $subscription['Subscription']['subject'], 'notification_type' => $subscription['Subscription']['notification_type'], 'notification_json_data' => $subscription['Subscription']['notification_json_data']));
		}

		$event -> subject -> Notification -> saveAll($data);
	}

	public function notifyUserModel($event) {
		$subscriptions = $event -> data['subscriptions'];

		// We will be loading the Notify model and then updating it
		// I think the subject will be the controller/model whatever the subscription was
		// $event -> subject ->

		// This could kick off a shit ton of events, depending on how many people.
		// We might want to do a bulk notify
		$data = array();
		foreach ($subscriptions as $key => $subscription) {
			array_push($data, array('user_id' => $subscription['Subscription']['user_id'], 'message' => $subscription['Subscription']['message'], 'subject' => $subscription['Subscription']['subject'], 'notification_type' => $subscription['Subscription']['notification_type'], 'notification_json_data' => $subscription['Subscription']['notification_json_data']));
		}
		$event -> subject -> bindModel(array('belongsTo' => array('Notification')));
		$event -> subject -> Notification -> saveAll($data);
		$event -> subject -> unbindModel(array('belongsTo' => array('Notification')));
	}

}
?>