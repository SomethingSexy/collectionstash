<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesWishListsController extends AppController {

	public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js');

	/**
	 * This will handle add, update, delete asynchronously
	 */
	public function collectible($id = null) {

		if (!$this -> isLoggedIn()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to add a collectible.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}

		if ($this -> request -> isPut()) {

		} else if ($this -> request -> isPost()) {
			$collectible['CollectiblesUser'] = $this -> request -> input('json_decode', true);
			$collectible['CollectiblesUser'] = Sanitize::clean($collectible['CollectiblesUser']);

			$response = $this -> CollectiblesUser -> add($collectible, $this -> getUser());

			$this -> set('returnData', $response);
		} else if ($this -> request -> isDelete()) {
			// for now this will handle deletes where the user is prompted
			// about the delete
			// we need to pull the query parameters
			$collectible['CollectiblesUser'] = array();
			$collectible['CollectiblesUser']['id'] = $id;
			$collectible['CollectiblesUser']['collectible_user_remove_reason_id'] = Sanitize::clean($this -> request -> query('collectible_user_remove_reason_id'));
			$collectible['CollectiblesUser']['sold_cost'] = Sanitize::clean($this -> request -> query('sold_cost'));
			$collectible['CollectiblesUser']['remove_date'] = Sanitize::clean($this -> request -> query('remove_date'));

			$response = $this -> CollectiblesUser -> remove($collectible, $this -> getUser());
			if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
				$this -> response -> statusCode(401);
			} else if (!$response['response']['isSuccess'] && $response['response']['code'] === 500) {
				$this -> response -> statusCode(500);
			}

			$this -> set('returnData', $response);
		} else if ($this -> request -> isGet()) {

		}
	}
}
?>