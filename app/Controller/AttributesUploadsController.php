<?php
App::uses('Sanitize', 'Utility');
class AttributesUploadsController extends AppController {

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
			$error = array('message' => __('You must be logged in to edit photos'));
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

		$uploads = $this -> AttributesUpload -> find("all", array('contain' => array('Upload'), 'conditions' => array('AttributesUpload.attribute_id' => $collectibleId)));
		$pending = $this -> AttributesUpload -> findPendingEdits(array('AttributesUploadEdit.attribute_id' => $collectibleId));

		foreach ($pending as $key => $value) {
			if ($value['Action']['action_type_id'] === '4') {
				foreach ($uploads as $key => $upload) {
					if ($upload['AttributesUpload']['id'] === $value['AttributesUploadEdit']['base_id']) {
						unset($uploads[$key]);
						break;
					}
				}
			}

			$resizedImg = $this -> Image -> image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
			$img = $this -> Image -> image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 0, 'height' => 0, 'imagePathOnly' => true));
			$uploadResponse = array();
			$uploadResponse['url'] = $img['path'];
			$uploadResponse['thumbnail_url'] = $resizedImg['path'];
			$uploadResponse['type'] = $value['Upload']['type'];
			$uploadResponse['size'] = intval($value['Upload']['size']);
			$uploadResponse['name'] = $value['Upload']['name'];
			$uploadResponse['delete_url'] = '/attributes_uploads/remove/' . $value['AttributesUploadEdit']['id'] . '/true';
			$uploadResponse['delete_type'] = 'POST';
			//$uploadResponse['id'] = $value['AttributesUploadEdit']['attribute_id'];

			// Given the action, detemine what type of pending it is
			$uploadResponse['pending'] = true;
			if ($value['Action']['action_type_id'] === '1') {
				$uploadResponse['pendingText'] = __('Pending Approval');
			} else if ($value['Action']['action_type_id'] === '4') {
				$uploadResponse['pendingText'] = __('Pending Removal');
			}

			if ($value['AttributesUploadEdit']['edit_user_id'] === $this -> getUserId()) {
				$uploadResponse['owner'] = true;
			} else {
				$uploadResponse['owner'] = false;
			}

			if ($uploadResponse['owner'] && $value['Action']['action_type_id'] !== '4') {
				$uploadResponse['allowDelete'] = true;
			} else {
				$uploadResponse['allowDelete'] = false;
			}

			array_push($returnData['response']['data'], $uploadResponse);
		}

		foreach ($uploads as $key => $value) {
			$resizedImg = $this -> Image -> image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
			$img = $this -> Image -> image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 0, 'height' => 0, 'imagePathOnly' => true));
			$uploadResponse = array();
			$uploadResponse['url'] = $img['path'];
			$uploadResponse['thumbnail_url'] = $resizedImg['path'];
			$uploadResponse['type'] = $value['Upload']['type'];
			$uploadResponse['size'] = intval($value['Upload']['size']);
			$uploadResponse['name'] = $value['Upload']['name'];
			$uploadResponse['delete_url'] = '/attributes_uploads/remove/' . $value['AttributesUpload']['id'] . '/false';
			$uploadResponse['delete_type'] = 'POST';
			//$uploadResponse['id'] = $value['AttributesUpload']['attribute_id'];
			$uploadResponse['pending'] = false;
			$uploadResponse['allowDelete'] = true;
			$uploadResponse['primary'] = $value['AttributesUpload']['primary'];

			array_push($returnData['response']['data'], $uploadResponse);
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
		if ($pending === 'true') {
			$edit = $this -> AttributesUpload -> findEdit($id);
			$this -> loadModel('Edit');
			//TODO Check to make sure the person deleting it is the owner
			// Going through the edit model because it will handle stuff for us
			$this -> Edit -> denyEdit($edit['AttributesUploadEdit']['edit_id'], false);
		} else {
			$upload = array();
			$upload['AttributesUpload']['id'] = $id;
			$response = $this -> AttributesUpload -> remove($upload, $this -> getUserId(), false);
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
			$response = $this -> AttributesUpload -> add($this -> request -> data, $this -> getUserId());
			if ($response) {
				if ($response['response']['isSuccess']) {
					$upload = $response['response']['data'];
					// We need to return the data in a specific format for the upload plugin to handle
					// it propertly
					$resizedImg = $this -> Image -> image($upload['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
					$img = $this -> Image -> image($upload['Upload']['name'], array('uploadDir' => 'files', 'width' => 0, 'height' => 0, 'imagePathOnly' => true));
					$retunData = array();
					$uploadResponse = array();
					$uploadResponse['url'] = $img['path'];
					$uploadResponse['thumbnail_url'] = $resizedImg['path'];
					$uploadResponse['type'] = $upload['Upload']['type'];
					$uploadResponse['size'] = intval($upload['Upload']['size']);
					$uploadResponse['name'] = $upload['Upload']['name'];
					// this should be the id of the new pending collectible

					$uploadResponse['delete_type'] = 'POST';
					$uploadResponse['id'] = $this -> request -> data['AttributesUpload']['attribute_id'];

					if ($upload['isEdit']) {
						$uploadResponse['pending'] = true;
						$uploadResponse['pendingText'] = __('Pending Approval');
					} else {
						$uploadResponse['pending'] = false;
					}

					$uploadResponse['delete_url'] = '/attributes_uploads/remove/' . $upload['AttributesUpload']['id'] . '/'. $uploadResponse['pending'];

					//TODO: Need to figure these two properties out
					$uploadResponse['owner'] = true;
					$uploadResponse['allowDelete'] = true;

					array_push($retunData, $uploadResponse);
					$this -> set('returnData', $retunData);
				} else {
					// Need to figure out how the plugin handles errors
					$this -> set('returnData', $response);
				}

			} else {
				//Something really fucked up
				$data['isSuccess'] = false;
				$data['errors'] = array('message', __('Invalid request.'));
				$this -> set('returnData', $data);
			}
		} else {
			$data['isSuccess'] = false;
			$data['errors'] = array('message', __('Invalid request.'));
			$this -> set('returnData', $data);
			return;
		}

	public function uploads($collectibleId) {
		$returnData = array();

		if (isset($collectibleId) && is_numeric($collectibleId)) {
			$returnData = $this -> AttributesUpload -> find('all', array('contain' => array('Upload'), 'conditions' => array('AttributesUpload.attribute_id' => $collectibleId)));
		}

		$this -> set(compact('returnData'));
	}

	public function admin_approval($editId = null, $collectibleUploadEditId = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($editId && is_numeric($editId) && $collectibleUploadEditId && is_numeric($collectibleUploadEditId)) {
			$this -> set('collectibleUploadEditId', $collectibleUploadEditId);
			$this -> set('editId', $editId);
			if (empty($this -> request -> data)) {
				$collectibleUpload = $this -> AttributesUpload -> getEditForApproval($collectibleUploadEditId);

				if ($collectibleUpload) {
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