<?php
App::uses('Sanitize', 'Utility');
class NotificationsController extends AppController {

	public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');

	/**
	 * restful method to retrieve notifications
	 *
	 */
	public function notifications() {
		$this -> checkLogIn();
		$user = $this -> getUser();
		$this -> paginate = array('limit' => 25, 'order' => array('Notification.created' => 'desc'), 'conditions' => array('Notification.user_id' => $user['User']['id']));
		$notifications = $this -> paginate('Notification');
		$this -> set(compact('notifications'));
	}

	/**
	 * restful method to retrieve, update, and delete notifications
	 *
	 * We will need to reset the number of notifications in the session
	 *
	 * unless we have something on the client side come reset it automatically
	 *
	 */
	public function notification($id) {

	}

}
?>
