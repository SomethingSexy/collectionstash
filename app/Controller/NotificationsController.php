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
		if (!$this -> isLoggedIn()) {
			$this -> response -> statusCode(401);
			return;
		}

		//update
		if ($this -> request -> isPut()) {
			$notification['Notification'] = $this -> request -> input('json_decode', true);
			$notification['Notification'] = Sanitize::clean($notification['Notification']);

			$response = $this -> Notification -> update($notification, $this -> getUser());

			$request = $this -> request -> input('json_decode');

			if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
				$this -> response -> statusCode(401);
			}

			$this -> set('returnData', $request);

			$totalNotifications = $this -> Notification -> getCountUnreadNotifications($this -> getUserId());
			$this -> Session -> write('notificationsCount', $totalNotifications);
		} else if ($this -> request -> isDelete()) {

			$notification = array();
			$notification['Notification']['id'] = $id;

			$response = $this -> Notification -> remove($notification, $this -> getUser());

			if (!$response['response']['isSuccess']) {
				$this -> response -> statusCode(400);
			}

			$this -> set('returnData', $response);

		} else if ($this -> request -> isGet()) {
			$returnData = $this -> Collectible -> getCollectible($id);
			$this -> set('returnData', $returnData['response']['data']['collectible']['Collectible']);
		}

	}

}
?>
