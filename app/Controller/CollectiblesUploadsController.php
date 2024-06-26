<?php
App::uses('Sanitize', 'Utility');
class CollectiblesUploadsController extends AppController {

	public $helpers = array('Html', 'Form', 'Js', 'FileUpload.FileUpload', 'Minify', 'CollectibleDetail');

	public $components = array('Image');

	/**
	 * This method will return all images for a given collectible in a JSON format
	 *
	 * This will be used to populate the dialog
	 *
	 * If the user is logged in, this should also return any pending uploads
	 */
	public function view($collectibleId) {
		// as of now you need to be logged int o view this
		if (!$this -> isLoggedIn()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to edit photos.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}

		$returnData = array();
		$returnData['response'] = array();
		$returnData['response']['isSuccess'] = true;
		$returnData['response']['message'] = '';
		$returnData['response']['code'] = 0;
		$returnData['response']['errors'] = array();
		$returnData['response']['data'] = array();
		$returnData['response']['data']['files'] = array();

		$uploads = $this -> CollectiblesUpload -> find("all", array('contain' => array('Upload'), 'conditions' => array('CollectiblesUpload.collectible_id' => $collectibleId)));
		$pending = $this -> CollectiblesUpload -> findPendingEdits(array('CollectiblesUploadEdit.collectible_id' => $collectibleId));

		foreach ($pending as $key => $value) {
			if ($value['Action']['action_type_id'] === '4') {
				foreach ($uploads as $key => $upload) {
					if ($upload['CollectiblesUpload']['id'] === $value['CollectiblesUploadEdit']['base_id']) {
						unset($uploads[$key]);
						break;
					}
				}
			}

			$resizedImg = $this -> Image -> image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
			$img = $this -> Image -> image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 0, 'height' => 0, 'imagePathOnly' => true));
			$uploadResponse = array();
			$uploadResponse['url'] = $img['path'];
			$uploadResponse['thumbnailUrl'] = $resizedImg['path'];
			$uploadResponse['type'] = $value['Upload']['type'];
			$uploadResponse['size'] = intval($value['Upload']['size']);
			$uploadResponse['name'] = $value['Upload']['name'];
			$uploadResponse['deleteUrl'] = '/collectibles_uploads/remove/' . $value['CollectiblesUploadEdit']['id'] . '/true';
			$uploadResponse['deleteType'] = 'POST';
			$uploadResponse['id'] = $value['CollectiblesUploadEdit']['collectible_id'];

			// Given the action, detemine what type of pending it is
			$uploadResponse['pending'] = true;
			if ($value['Action']['action_type_id'] === '1') {
				$uploadResponse['pendingText'] = __('Pending Approval');
			} else if ($value['Action']['action_type_id'] === '4') {
				$uploadResponse['pendingText'] = __('Pending Removal');
			}

			if ($value['CollectiblesUploadEdit']['edit_user_id'] === $this -> getUserId()) {
				$uploadResponse['owner'] = true;
			} else {
				$uploadResponse['owner'] = false;
			}

			if ($uploadResponse['owner'] && $value['Action']['action_type_id'] !== '4') {
				$uploadResponse['allowDelete'] = true;
			} else {
				$uploadResponse['allowDelete'] = false;
			}

			array_push($returnData['response']['data']['files'], $uploadResponse);
		}

		foreach ($uploads as $key => $value) {
			$resizedImg = $this -> Image -> image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
			$img = $this -> Image -> image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 0, 'height' => 0, 'imagePathOnly' => true));
			$uploadResponse = array();
			$uploadResponse['url'] = $img['path'];
			$uploadResponse['thumbnailUrl'] = $resizedImg['path'];
			$uploadResponse['type'] = $value['Upload']['type'];
			$uploadResponse['size'] = intval($value['Upload']['size']);
			$uploadResponse['name'] = $value['Upload']['name'];
			$uploadResponse['deleteUrl'] = '/collectibles_uploads/remove/' . $value['CollectiblesUpload']['id'] . '/false';
			$uploadResponse['deleteType'] = 'POST';
			$uploadResponse['id'] = $value['CollectiblesUpload']['collectible_id'];
			$uploadResponse['pending'] = false;
			$uploadResponse['allowDelete'] = true;
			$uploadResponse['primary'] = $value['CollectiblesUpload']['primary'];

			array_push($returnData['response']['data']['files'], $uploadResponse);
		}

		$this -> set('returnData', $returnData);
	}

	/**
	 * This will submit a delete edit OR if it is pending and the user logged in
	 * is the one deleting it, it will automatically delete it
	 */
	public function remove($id, $pending = false) {
		if (!$this -> isLoggedIn()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to remove a photo.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}
		/*
		 * If it is pending, we will look up the edit, check to see if it was done by that person
		 * if so then we will delete it
		 */
		if ($pending === 'true' || $pending === '1') {
			$edit = $this -> CollectiblesUpload -> findEdit($id);
			$this -> loadModel('Edit');
			//TODO Check to make sure the person deleting it is the owner
			// Going through the edit model because it will handle stuff for us
			$this -> Edit -> denyEdit($edit['CollectiblesUploadEdit']['edit_id'], false);
		} else {
			$upload = array();
			$upload['CollectiblesUpload']['id'] = $id;
			$response = $this -> CollectiblesUpload -> remove($upload, $this -> getUser(), false);
			if ($response) {
				if ($response['response']['isSuccess']) {
					$retunData = array();

					$this -> set('returnData', $retunData);
				} else {
					// Need to figure out how the plugin handles errors
				}

			} else {
				//Something really fucked up
				$data['isSuccess'] = false;
				$data['errors'] = array('message', __('Invalid request.'));
				$this -> set('returnData', $data);
			}
		}
	}

	/**
	 *
	 */
	public function upload() {
		$this -> layout = 'ajax';
		$data = array();
		//must be logged in to post comment
		if (!$this -> isLoggedIn()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to add a photo.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$response = $this -> CollectiblesUpload -> add($this -> request -> data, $this -> getUser());
			if ($response) {
				if ($response['response']['isSuccess']) {
					$upload = $response['response']['data'];
					// We need to return the data in a specific format for the upload plugin to handle
					// it propertly
					// when uploading, also resize one to 400x400 to use for main thumbnails, that way we don't have to do it when accessing.
					$this -> Image -> image($upload['Upload']['name'], array('uploadDir' => 'files', 'width' => 400, 'height' => 400, 'imagePathOnly' => true));
					// this one is used by the upload dialog
					$resizedImg = $this -> Image -> image($upload['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
					// also return the full image
					$img = $this -> Image -> image($upload['Upload']['name'], array('uploadDir' => 'files', 'width' => 0, 'height' => 0, 'imagePathOnly' => true));
					$retunData = array();
					$retunData['files'] = array();
					$uploadResponse = array();
					$uploadResponse['url'] = $img['path'];
					$uploadResponse['thumbnailUrl'] = $resizedImg['path'];
					$uploadResponse['type'] = $upload['Upload']['type'];
					$uploadResponse['size'] = intval($upload['Upload']['size']);
					$uploadResponse['name'] = $upload['Upload']['name'];
					// this should be the id of the new pending collectible

					$uploadResponse['deleteType'] = 'POST';
					$uploadResponse['id'] = $this -> request -> data['CollectiblesUpload']['collectible_id'];

					if ($upload['isEdit']) {
						$uploadResponse['pending'] = true;
						$uploadResponse['pendingText'] = __('Pending Approval');
					} else {
						$uploadResponse['pending'] = false;
					}

					$uploadResponse['deleteUrl'] = '/collectibles_uploads/remove/' . $upload['CollectiblesUpload']['id'] . '/'. $uploadResponse['pending'];

					//TODO: Need to figure these two properties out
					$uploadResponse['owner'] = true;
					$uploadResponse['allowDelete'] = true;

					array_push($retunData['files'], $uploadResponse);
					$this -> set('returnData', $retunData);
				} else {
					$this->response->statusCode(400);
					// Need to figure out how the plugin handles errors
					$this -> set('returnData', $response['response']['data']);
				}

			} else {
				$this->response->statusCode(400);
				$this -> set('returnData',  __('Invalid request.'));
			}
		} else {
			$this->response->statusCode(400);
			$this -> set('returnData',  __('Invalid request.'));
			return;
		}

	}

	public function uploads($collectibleId) {
		$returnData = array();

		if (isset($collectibleId) && is_numeric($collectibleId)) {
			$returnData = $this -> CollectiblesUpload -> find('all', array('contain' => array('Upload'), 'conditions' => array('CollectiblesUpload.collectible_id' => $collectibleId)));
		}

		$this -> set(compact('returnData'));
	}

	public function admin_approval($editId = null, $collectibleUploadEditId = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($editId && is_numeric($editId) && $collectibleUploadEditId && is_numeric($collectibleUploadEditId)) {
			$this -> set('$collectibleUploadEditId', $collectibleUploadEditId);
			$this -> set('editId', $editId);
			if (empty($this -> request -> data)) {
				$collectibleUpload = $this -> CollectiblesUpload -> getEditForApproval($collectibleUploadEditId);

				if ($collectibleUpload) {
					debug($collectibleUpload);
					$this -> set(compact('collectibleUpload'));

				} else {
					//uh fuck you
					$this -> redirect('/');
				}
			}

		} else {
			$this -> redirect('/');
		}
	}

}
?>