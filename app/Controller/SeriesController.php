<?php
App::uses('Sanitize', 'Utility');
class SeriesController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify', 'Tree');

	public function get($manufacturerId, $mode = null) {
		$returnData = $this -> Series -> getSeriesByManufacturer($manufacturerId);

		$this -> autoRender = false;
		$view = new View($this, false);
		$view -> set('series', $returnData['response']['data']);

		/* Grab output into variable without the view actually outputting! */
		if (!is_null($mode) && $mode === 'edit') {
			$view_output = $view -> render('tree_edit');
			$returnData['response']['data'] = $view_output;
		} else {
			$view_output = $view -> render('tree');
			$returnData['response']['data'] = $view_output;
		}

		$this -> autoRender = true;

		$this -> set(compact('returnData'));
	}

	public function admin_list() {
		$stuff = $this -> Series -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id'), 'order' => 'lft ASC'));
		$this -> set('stuff', $stuff);
		$this -> layout = 'fluid';
	}

	/**
	 * For adding by users
	 */
	public function add() {
		if (!$this -> isLoggedIn()) {
			$this -> response -> statusCode(401);
			$this -> set('returnData', array());
			return;
		}
		if ($this -> request -> isPost()) {// create
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			$response = $this -> Series -> add($this -> request -> data, $this -> getUser(), true);

			if (!$response['response']['isSuccess']) {
				$this -> response -> statusCode(400);
			}

			$this -> set('returnData', $response);
		} else if ($this -> request -> isPut()) {//update

		}
	}

	public function admin_add() {

		$invalidRequest = false;
		$invalidSave = false;
		$invalidPost = false;
		$isSuccess = false;
		if ($this -> isLoggedIn() && $this -> isUserAdmin()) {
			//Make sure it a post, if not don't accept it
			if ($this -> request -> is('post')) {
				if (!empty($this -> request -> data)) {
					if ($this -> Series -> save($this -> request -> data)) {
						$isSuccess = true;

					} else {
						$invalidSave = true;
					}
				} else {
					$invalidPost = true;
				}
			} else {
				$invalidPost = true;
			}
		} else {
			$invalidRequest = true;

		}
		if ($this -> request -> isAjax()) {
			$data = array();
			if ($invalidSave) {
				$data['success'] = array('isSuccess' => false);
				$data['isTimeOut'] = false;
				$data['data'] = array();
				$data['errors'] = array($this -> Series -> validationErrors);
			} else if ($invalidPost) {
				$data['success'] = array('isSuccess' => false);
				$data['isTimeOut'] = false;
				$data['data'] = array();
				$data['errors'][0] = array('invalidRequest' => 'The request was invalid.');
			} else if ($invalidRequest) {
				//If they are not logged in and are trying to access this then just time them out
				$data['success'] = array('isSuccess' => false);
				$data['isTimeOut'] = true;
				$data['data'] = array();
			} else {
				//successful
				$data['success'] = array('isSuccess' => true);
				$data['isTimeOut'] = false;
				$data['data'] = array('id' => $this -> Series -> id);
			}
			//better way to handle this?
			$this -> set('aSeriesData', $data);
			$this -> render('admin_add_ajax');
		} else {
			if ($isSuccess) {
				$this -> redirect(array('action' => 'list'));
			}
		}

		$this -> layout = 'fluid';
	}

	public function admin_remove() {
		$data = array();
		if ($this -> isLoggedIn() && $this -> isUserAdmin()) {
			if (!empty($this -> request -> data) && $this -> request -> is('post')) {
				$this -> Series -> id = $this -> request -> data['Series']['id'];
				if ($this -> Series -> delete()) {
					$data['success'] = array('isSuccess' => true);
					$data['isTimeOut'] = false;
					$data['data'] = array();

				} else {
					$data['success'] = array('isSuccess' => false);
					$data['isTimeOut'] = false;
					$data['errors'] = array($this -> Series -> validationErrors);
				}
			} else {
				$data['success'] = array('isSuccess' => false);
				$data['isTimeOut'] = false;
				$data['data'] = array();
				$data['errors'][0] = array('invalidRequest' => 'The request was invalid.');
			}
		} else {
			$data['success'] = array('isSuccess' => false);
			$data['isTimeOut'] = false;
			$data['data'] = array();
			$data['errors'][0] = array('invalidRequest' => 'The request was invalid.');
		}

		$this -> set('aSeriesData', $data);
	}

}
?>