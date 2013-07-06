<?php
/**
 * This is the main controller used for all interactions with an attribute
 *
 * When trying to delete an attribute and we want to link it to an existin collectible
 *
 * Let's add the ability to search on a collectible and select an attribute from
 * that collectible
 */
App::uses('Sanitize', 'Utility');
class AttributesController extends AppController {

	//'CollectibleDetail' should really be named Field Helper or something
	public $helpers = array('Html', 'Js', 'Minify', 'Tree', 'CollectibleDetail', 'FileUpload.FileUpload');

	// For attribute we might need some custom logic because it should return all underneath the selected category
	public $filters = array('m' => array('model' => 'Attribute', 'id' => 'manufacture_id'), 'c' => array('model' => 'Attribute', 'id' => 'attribute_category_id'), 's' => array('model' => 'Attribute', 'id' => 'scale_id'), 'a' => array('model' => 'Attribute', 'id' => 'artist_id'));

	/**
	 * This method is used by the main catelog page to view all of the attributes
	 */
	public function index() {
		// Here I need to check the query string for all possible filters

		$saveSearchFilters = array();
		$currentFilters = array();
		$currentFilters['Search'] = array();
		foreach ($this -> filters as $filterkey => $filter) {
			if (isset($this -> request -> query[$filterkey])) {

				$queryValue = $this -> request -> query[$filterkey];
				if (strpos($queryValue, ',') !== false) {
					$queryValue = rtrim($queryValue, ",");
					$queryValue = explode(",", $queryValue);
				} else {
					$queryValue = array($queryValue);
				}

				$currentFilters['Search'][$filterkey] = array();
				foreach ($queryValue as $key => $value) {
					array_push($currentFilters['Search'][$filterkey], $value);
					if (!isset($saveSearchFilters[$filterkey])) {
						$saveSearchFilters[$filterkey] = array();
					}
					array_push($saveSearchFilters[$filterkey], $value);
				}

			}
		}

		$tableFilters = array();
		foreach ($currentFilters['Search'] as $filterKey => $filterGroup) {
			$modelFilters = array();
			array_push($modelFilters, array('AND' => array()));
			array_push($modelFilters[0]['AND'], array('OR' => array()));
			$filtersSet = false;

			foreach ($filterGroup as $key => $value) {
				if ($value != 0) {
					array_push($modelFilters[0]['AND'][0]['OR'], array($this -> filters[$filterKey]['model'] . '.' . $this -> filters[$filterKey]['id'] => $value));
					$filtersSet = true;
				}
			}

			if ($filtersSet) {
				array_push($tableFilters, $modelFilters);
			}
		}

		// TODO: Right now this is returning stuff that has not been approved yet...which is fine
		$this -> paginate = array('conditions' => array($tableFilters, 'status_id' => 4, 'type' => 'mass'), 'contain' => array('AttributesUpload' => array('Upload'), 'AttributeCategory', 'Manufacture', 'Artist', 'Scale', 'AttributesCollectible' => array('Collectible' => array('fields' => array('id', 'name')))), 'order' => array('Attribute.attribute_category_id' => 'ASC'), 'limit' => 50);
		$attributes = $this -> paginate('Attribute');
		$this -> set(compact('attributes'));

		$filters = $this -> _getFilters();

		$this -> set(compact('filters'));
		$this -> set(compact('saveSearchFilters'));

		if ($this -> request -> isAjax()) {
			$this -> render('searchJson');
		}
	}

	/**
	 * This will be the main view page to view all of the details of
	 * an attribute
	 */
	public function view($id = null) {
		if (is_null($id) || !is_numeric($id)) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect(array('action' => 'index'));
		}

		$attribute = $this -> Attribute -> find('first', array('conditions' => array('Attribute.id' => $id)));

		if (!empty($attribute) && $attribute['Attribute']['status_id'] === '4') {
			$this -> set(compact('attribute'));
		} else {
			$this -> render('viewMissing');
		}
	}

	/**
	 *
	 */
	public function add() {
		$data = array();
		//must be logged in to post comment
		if (!$this -> isLoggedIn()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to post a comment.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$this -> request -> data = Sanitize::clean($this -> request -> data);

			$response = $this -> Attribute -> addAttribute($this -> request -> data, $this -> getUser());
			if ($response) {
				$this -> set('returnData', $response);
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
	}

	/**
	 * This will submit a removal.
	 * 
	 * This can only be used with JSON for now :)
	 *
	 * TODO: Update this so that if they are removing a pending one that they
	 * added, it automatically gets deleted
	 */
	public function remove() {
		if (!$this -> isLoggedIn()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to update this item.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$this -> request -> data = $this -> request -> input('json_decode', true);
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			debug($this -> request -> data);

			$response = $this -> Attribute -> remove($this -> request -> data, $this -> getUser());

			if ($response) {
				$this -> set('returnData', $response);
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
	}

	public function admin_remove() {
		if (!$this -> isLoggedIn() || !$this -> isUserAdmin()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to update this item.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			debug($this -> request -> data);

			$response = $this -> Attribute -> remove($this -> request -> data, $this -> getUser(), true);

			if ($response) {
				$this -> set('returnData', $response);
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
	}

	public function admin_update() {
		if (!$this -> isLoggedIn() || !$this -> isUserAdmin()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to update this item.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			debug($this -> request -> data);

			$response = $this -> Attribute -> update($this -> request -> data, $this -> getUser(), true);
			debug($response);
			if ($response) {
				$this -> set('returnData', $response);
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
	}

	/**
	 *
	 */
	public function update() {
		if (!$this -> isLoggedIn()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to update this item.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}
		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			debug($this -> request -> data);

			$response = $this -> Attribute -> update($this -> request -> data, $this -> getUser());

			if ($response) {
				$this -> set('returnData', $response);
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
	}

	function admin_index($standalone = false) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($standalone) {
			// Only return ones that are not tied to a collectible
			$this -> paginate = array('standalone', 'conditions' => array('Attribute.status_id' => 2));
		} else {
			// TODO: Only return ones that are tied to something
			// Might be easier to find all and then filter :)
			$this -> paginate = array('collectible', 'conditions' => array('Attribute.status_id' => 2));
		}

		$attributes = $this -> paginate('Attribute');

		$this -> set('attributes', $attributes);
	}

	function admin_view($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();

		if (!$id) {
			$this -> Session -> setFlash(__('Invalid Item', true));
			$this -> redirect(array('action' => 'index'));
		}

		$attribute = $this -> Attribute -> find('first', array('conditions' => array('Attribute.id' => $id)));
		debug($attribute);
		$this -> set('attribute', $attribute);
	}

	/**
	 * Attributes can be added at two places
	 * 	- Stand alone
	 * 	- Or when being added new when adding a collectible
	 *
	 * 	- When they are being directly attached to a collectible, if the attribute is denied then we will want to automatically delete the link
	 */
	function admin_approve($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		$this -> request -> data = Sanitize::clean($this -> request -> data);
		if ($id && is_numeric($id) && isset($this -> request -> data['Approval']['approve'])) {
			$response = $this -> Attribute -> approve($id, $this -> request -> data, $this -> getUserId());
			if ($response['response']['isSuccess'] === true) {
				if ($response['response']['code'] === 1) {
					$this -> Session -> setFlash(__('The item was successfully approved.', true), null, null, 'success');
				} else if ($response['response']['code'] === 2) {
					$this -> Session -> setFlash(__('The item was successfully denied.', true), null, null, 'success');
				}
				$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
			} else {
				$this -> Session -> setFlash(__('There was a problem approving the item.', true), null, null, 'error');
				$this -> redirect(array('admin' => true, 'action' => 'view', $id), null, true);
			}
		} else {
			$this -> Session -> setFlash(__('Invalid item.', true), null, null, 'error');
			$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
		}

	}

	function admin_approval($editId = null, $attributeEditId = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($editId && is_numeric($editId) && $attributeEditId && is_numeric($attributeEditId)) {
			$this -> set('$attributeEditId', $attributeEditId);
			$this -> set('editId', $editId);
			if (empty($this -> request -> data)) {
				// Remember that the "Attribute" being returned from here is really the AttributeEdit, so we need to
				// use the base_id
				$attribute = $this -> Attribute -> getEditForApproval($attributeEditId);
				debug($attribute);
				if ($attribute) {
					// We also want to find all collectibles that this attribute is currently tied too
					// Because this is an edit we want the base id
					if (isset($attribute['AttributeEdit'])) {
						$attributesCollectible = $this -> Attribute -> AttributesCollectible -> find("all", array('conditions' => array('AttributesCollectible.attribute_id' => $attribute['AttributeEdit']['base_id'])));
					} else {
						$attributesCollectible = $this -> Attribute -> AttributesCollectible -> find("all", array('conditions' => array('AttributesCollectible.attribute_id' => $attribute['Attribute']['base_id'])));
					}

					debug($attributesCollectible);
					$this -> set(compact('attributesCollectible'));
					$this -> set(compact('attribute'));
				} else {
					//uh fuck you
					$this -> redirect('/');
				}
			}
		} else {
			$this -> redirect('/');
		}
	}

	public function isValid() {
		if (!$this -> isLoggedIn()) {
			$data['response'] = array();
			$data['response']['isSuccess'] = false;
			$error = array('message' => __('You must be logged in to update this item.'));
			$error['inline'] = false;
			$data['response']['errors'] = array();
			array_push($data['response']['errors'], $error);
			$this -> set('returnData', $data);
			return;
		}

		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$this -> request -> data = Sanitize::clean($this -> request -> data);

			$response = $this -> Attribute -> validateAttrbitue($this -> request -> data);

			if ($response) {
				$this -> set('returnData', $response);
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
	}

	/**
	 *
	 */
	private function _getFilters() {
		$searchFilterGroups = array();
		$manufacturers = $this -> Attribute -> Manufacture -> find("all", array('order' => array('Manufacture.title' => 'ASC'), 'contain' => false));
		$artists = $this -> Attribute -> Artist -> find("all", array('order' => array('Artist.name' => 'ASC'), 'contain' => false));
		$categories = $this -> Attribute -> AttributeCategory -> find("all", array('contain' => false));
		$scales = $this -> Attribute -> Scale -> find("all", array('contain' => false));

		$manufacturerSearchGroup = array();
		$manufacturerSearchGroup['filters'] = array();
		$manufacturerSearchGroup['selected'] = array();
		$manufacturerSearchGroup['label'] = __('Manufacturer');
		$manufacturerSearchGroup['type'] = 'm';
		$manufacturerSearchGroup['allowMultiple'] = 'true';

		foreach ($manufacturers as $key => $value) {
			$serachFilter = array();
			$serachFilter['id'] = $value['Manufacture']['id'];
			$serachFilter['label'] = $value['Manufacture']['title'];
			array_push($manufacturerSearchGroup['filters'], $serachFilter);
		}
		array_push($searchFilterGroups, $manufacturerSearchGroup);

		$artistSearchGroup = array();
		$artistSearchGroup['filters'] = array();
		$artistSearchGroup['selected'] = array();
		$artistSearchGroup['label'] = __('Artist');
		$artistSearchGroup['type'] = 'a';
		$artistSearchGroup['allowMultiple'] = 'true';

		foreach ($artists as $key => $value) {
			$serachFilter = array();
			$serachFilter['id'] = $value['Artist']['id'];
			$serachFilter['label'] = $value['Artist']['name'];
			array_push($artistSearchGroup['filters'], $serachFilter);
		}
		array_push($searchFilterGroups, $artistSearchGroup);

		$categorySearchGroup = array();
		$categorySearchGroup['filters'] = array();
		$categorySearchGroup['selected'] = array();
		$categorySearchGroup['label'] = __('Categories');
		$categorySearchGroup['type'] = 'c';
		$categorySearchGroup['allowMultiple'] = 'true';
		foreach ($categories as $key => $value) {
			$serachFilter = array();
			$serachFilter['id'] = $value['AttributeCategory']['id'];
			$serachFilter['label'] = $value['AttributeCategory']['path_name'];
			array_push($categorySearchGroup['filters'], $serachFilter);
		}
		array_push($searchFilterGroups, $categorySearchGroup);

		$scaleSearchGroup = array();
		$scaleSearchGroup['filters'] = array();
		$scaleSearchGroup['selected'] = array();
		$scaleSearchGroup['label'] = __('Scales');
		$scaleSearchGroup['type'] = 's';
		$scaleSearchGroup['allowMultiple'] = 'true';
		foreach ($scales as $key => $value) {
			$serachFilter = array();
			$serachFilter['id'] = $value['Scale']['id'];
			$serachFilter['label'] = $value['Scale']['scale'];
			array_push($scaleSearchGroup['filters'], $serachFilter);
		}
		array_push($searchFilterGroups, $scaleSearchGroup);

		return $searchFilterGroups;
	}

}
?>
