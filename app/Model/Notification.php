<?php
/**
 * This is called notifications.  This will contain all possible notifications to a user.
 *
 * Notifications will be different than messages/conversations which might come later. This could potentially
 * notify a user of a new message. :)
 *
 * Notification won't know where it came from or what it is for.  It will just have a message to deliver to the user.
 *
 * How that is deliveried will be based on notification settins for that user
 *
 * The read flag will be used to determine if the user has read the notification
 *
 * The processed flag will be used to let us know if this notfication has been processed by some job
 *  	- Basically, have we done what we need to notify the user, whether that is to email them or not
 *
 */
class Notification extends AppModel {
	public $name = 'Notification';
	public $belongsTo = array('User' => array('fields' => array('id', 'username', 'email')));
	public $actsAs = array('Containable');

	/**
	 * This method will return the count of unread notifications per user
	 */
	public function getCountUnreadNotifications($userId) {
		return $this -> find('count', array('conditions' => array('Notification.user_id' => $userId, 'Notification.read' => false)));
	}

	/**
	 *
	 */
	public function update() {

	}

	/**
	 *
	 */
	public function remove() {

	}

}
