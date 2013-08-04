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

	function afterFind($results, $primary = false) {
		if ($results) {
			// If it is primary handle all of these things
			if ($primary) {
				foreach ($results as $key => $val) {
					if (isset($val['Notification'])) {
						if (isset($val['Notification']['created'])) {
							$datetime = strtotime($val['Notification']['created']);
							$datetime = date("m/d/y", $datetime);
							$results[$key]['Notification']['created'] = $datetime;
						}
					}
				}
			} else {
				if (isset($results[$this -> primaryKey])) {

					if (isset($results['created'])) {
						$datetime = strtotime($results['created']);
						$datetime = date("m/d/y", $datetime);
						$results['created'] = $datetime;
					}
				} else {

					foreach ($results as $key => $val) {

						if (isset($val['Notification'])) {
							if (isset($val['Notification']['created'])) {
								$datetime = strtotime($val['Notification']['created']);
								$datetime = date("m/d/y", $datetime);
								$results[$key]['Notification']['created'] = $datetime;
							}
						}
					}
				}
			}

		}
		return $results;
	}

	/**
	 * This method will return the count of unread notifications per user
	 */
	public function getCountUnreadNotifications($userId) {
		return $this -> find('count', array('conditions' => array('Notification.user_id' => $userId, 'Notification.read' => false)));
	}

	public function getCountNotifications($user) {
		return $this -> find('count', array('conditions' => array('Notification.user_id' => $user['User']['id'])));
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

	/**
	 * Returns all notifications for a given user
	 */
	public function getNotifications($user, $options = array()) {
		if (isset($options['conditions'])) {
			$options = array_merge($options['conditions'], array('Notification.user_id' => $user['User']['id']));
		} else {
			$options['conditions'] = array('Notification.user_id' => $user['User']['id']);
		}

		if (!isset($options['contain'])) {
			$options['contain'] = false;
		}

		$notifications = $this -> find("all", $options);

		return $notifications;
	}

}
