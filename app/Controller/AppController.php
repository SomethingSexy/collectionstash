<?php
App::uses('AuthComponent', 'Controller/Component');
class AppController extends Controller {
	// Since we are specifying the auto login and auth here, we need to pull in session as well
	public $components = array('Session', 'AutoLogin', 'Auth' => array('authenticate' => array('Form')));

	public function beforeFilter() {
		// Configure our auto login stuff
		$this -> AutoLogin -> settings = array(
		// Model settings
		'model' => 'User', 'username' => 'username', 'password' => 'password',

		// Controller settings
		'plugin' => '', 'controller' => 'Users', 'loginAction' => 'login', 'logoutAction' => 'logout',

		// Cookie settings
		'cookieName' => 'rememberMe', 'expires' => '+1 month',

		// Process logic
		'active' => true, 'redirect' => true, 'requirePrompt' => true);

		// Since I am not using auth to it's fullest right now
		// we need to allow all, the individual methods will
		// figure out if they need a user to be logged in
		$this -> Auth -> allow();

		if ($this -> request -> isAjax()) {
			Configure::write('debug', 0);			$this -> layout = 'ajax';
		} else {
			$this -> layout = 'default';
		}
		if (AuthComponent::user('id')) {
			$this -> set('isLoggedIn', true);
			$this -> set('username', $this -> getUsername());
			if ($this -> isUserAdmin()) {
				$this -> set('isUserAdmin', true);
			} else {
				$this -> set('isUserAdmin', false);
			}
		} else {
			$this -> set('isLoggedIn', false);
			$this -> set('isUserAdmin', false);
		}

		$this -> set('subscriptions', $this -> getSubscriptions());
		//Since this gets set for every request, setting this here for the default
		$this -> set('title_for_layout', 'Collection Stash');
		$this -> set('description_for_layout', 'Your collectible database and storage system.');
		$this -> set('keywords_for_layout', 'statue collection, action figure collection, toy collection, collectible databse, action figure, toy, stash, storage');
		//This stores off any request parameters per request, can be used to recreate urls later
		$requestParams = '?';
		if (isset($this -> request -> query)) {
			foreach ($this->request->query as $key => $value) {
				if ($key !== 'ext' && $key !== 'url') {
					$requestParams = $requestParams . $key . '=' . $value;
				}
			}
		}
		$this -> set(compact('requestParams'));
	}

	public function getUser() {
		$authUser = AuthComponent::user();
		$user['User'] = $authUser;
		return $user;
	}

	public function getUsername() {
		$user = $this -> getUser();
		return $user['User']['username'];
	}

	public function isLoggedIn() {
		$user = $this -> getUser();
		if (isset($user['User']) && !empty($user['User'])) {
			return true;
		} else {
			return false;
		}
	}

	public function isUserAdmin() {
		$user = $this -> getUser();

		if ($user['User']['admin'] == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function getUserId() {
		$user = $this -> getUser();
		return $user['User']['id'];
	}

	public function getSubscriptions() {
		$subscriptions = $this -> Session -> read('subscriptions');

		if ($subscriptions === null) {
			return array();
		} else {
			return $subscriptions;
		}
	}

	public function handleNotLoggedIn() {
		$this -> Session -> setFlash('Your session has timed out.');
		$this -> redirect(array('admin' => false, 'controller' => 'users', 'action' => 'login'), null, true);
	}

	/**
	 * This method will check if the user is logged in, if they are not it will
	 * auto redirect them.
	 */
	public function checkLogIn() {
		if (!$this -> isLoggedIn()) {
			$this -> handleNotLoggedIn();
		}
	}

	public function checkAdmin() {
		if (!$this -> isUserAdmin()) {
			$this -> handleNotLoggedIn();
		}
	}

	/**
	 * This is the insane search method to search on a collectible.
	 *
	 * Enhancements: Determine what filters might be set so we only do the contains on necessary ones, not all
	 *
	 * This should really be a component
	 */
	public function searchCollectible($conditions = null) {
		$this -> loadModel('Collectible');

		$saveSearchFilters = array();
		// handle this one separately for now as well
		if (isset($this -> request -> query['q'])) {
			$this -> request -> data['Search'] = array();
			$this -> request -> data['Search']['search'] = '';
			$this -> request -> data['Search']['search'] = $this -> request -> query['q'];
		}
		// handle this one separately
		if (isset($this -> request -> query['o'])) {

		} else {
			$this -> request -> query['o'] = 'a';
		}

		if (isset($this -> request -> data['Search']['search']) && trim($this -> request -> data['Search']['search']) !== '') {
			$search = $this -> request -> data['Search']['search'];
			$search = ltrim($search);
			$search = rtrim($search);
			$saveSearchFilters['search'] = $search;
		}

		// Here I need to check the query string for all possible filters
		debug($this -> request -> query);

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

		debug($saveSearchFilters);

		if (isset($saveSearchFilters['t'])) {
			reset($saveSearchFilters['t']);
			// make sure array pointer is at first element
			$firstKey = $saveSearchFilters['t'][0];
			$this -> loadModel('Tag');
			$tag = $this -> Tag -> find("first", array('contain' => false, 'conditions' => array('Tag.id' => $firstKey)));
			$saveSearchFilters['tag'] = $tag['Tag'];
		}

		debug($currentFilters);
		//If nothing is set, use alphabetical order as the default
		$order = array();
		$order['Collectible.name'] = 'ASC';
		$tableFilters = array();
		foreach ($currentFilters['Search'] as $filterKey => $filterGroup) {
			// if the one we are looking at is a custom
			if (!isset($this -> filters[$filterKey]['custom']) || !$this -> filters[$filterKey]['custom']) {
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
			} else if ($filterKey === 'o') {
				// there can only be on
				$orderType = $filterGroup[0];
				//reset order
				$order = array();
				switch ($orderType) {
					case "n" :
						$order['Collectible.modified'] = 'desc';
						break;
					case "o" :
						$order['Collectible.created'] = 'ASC';
						break;
					case "a" :
						$order['Collectible.name'] = 'ASC';
						break;
					case "d" :
						$order['Collectible.name'] = 'desc';
						break;
					default :
						$order['Collectible.name'] = 'ASC';
				}
			}
		}

		if (!isset($filters)) {
			$filters = array();
		}

		//Some conditions were added
		if (!is_array($conditions)) {
			$conditions = array();
		}

		$joins = array();
		//Do some special logic here for tags because of how they are setup.
		if (isset($saveSearchFilters['t']) && $saveSearchFilters['t']) {
			array_push($joins, array('table' => 'collectibles_tags', 'alias' => 'CollectiblesTag', 'type' => 'inner', 'conditions' => array('Collectible.id = CollectiblesTag.collectible_id')));
			array_push($joins, array('table' => 'tags', 'alias' => 'Tag', 'type' => 'inner', 'conditions' => array('CollectiblesTag.tag_id = Tag.id')));
		}

		$listSize = Configure::read('Settings.Search.list-size');

		// When doing this search, we only want to see the active ones
		array_push($conditions, array('Collectible.status_id' => '4'));
		//See if a search was set
		if (isset($search)) {
			//Is the search an empty string?
			if ($search == '') {
				$this -> paginate = array("joins" => $joins, 'order' => $order, "conditions" => array($conditions, $tableFilters), "contain" => array('Scale', 'ArtistsCollectible' => array('Artist'), 'AttributesCollectible' => array('Attribute' => array('AttributeCategory', 'Scale', 'Manufacture', 'AttributesUpload' => array('Upload'))), 'SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')), 'limit' => $listSize);
			} else {
				//Using like for now because switch to InnoDB
				$test = array();
				array_push($test, array('AND' => array()));
				array_push($test[0]['AND'], array('OR' => array()));

				//array_push($test[0]['AND'][0]['OR'], array('Collectible.name LIKE' => '%' . $search . '%'));

				$names = explode(' ', $search);
				$regSearch = array();
				foreach ($names as $key => $value) {
					// in case any weird characters get in there that this will trim
					$name = trim($value);
					array_push($regSearch, array('Collectible.name REGEXP' => '[[:<:]]' . $name . '[[:>:]]'));
				}
				array_push($test[0]['AND'][0]['OR'], $regSearch);
				// keep this one a standard like
				array_push($test[0]['AND'][0]['OR'], array('License.name LIKE' => '%' . $search . '%'));

				array_push($conditions, $test);
				$this -> paginate = array("joins" => $joins, 'order' => $order, "conditions" => array($conditions, $tableFilters), "contain" => array('Scale', 'ArtistsCollectible' => array('Artist'), 'AttributesCollectible' => array('Attribute' => array('AttributeCategory', 'Scale', 'Manufacture', 'AttributesUpload' => array('Upload'))), 'SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')), 'limit' => $listSize);
			}
		} else {
			//This a search based on filters, not a search string
			$this -> paginate = array("joins" => $joins, 'order' => $order, "contain" => array('Scale', 'ArtistsCollectible' => array('Artist'), 'AttributesCollectible' => array('Attribute' => array('AttributeCategory', 'Scale', 'Manufacture', 'AttributesUpload' => array('Upload'))), 'SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')), 'conditions' => array($conditions, $tableFilters), 'limit' => $listSize);
		}

		$data = $this -> paginate('Collectible');

		$this -> set('collectibles', $data);

		$filters = $this -> _getFilters($saveSearchFilters);
		$this -> set(compact('filters'));
		$this -> set(compact('saveSearchFilters'));

		return $data;
	}

	private function _getFilters($searchFilters) {

		$searchFilterGroups = array();
		$this -> loadModel('Manufacture');
		$manufacturers = $this -> Manufacture -> find("all", array('order' => array('Manufacture.title' => 'ASC'), 'contain' => false));
		//$categories = $this -> Attribute -> AttributeCategory -> find("all", array('contain' => false));
		//$scales = $this -> Attribute -> Scale -> find("all", array('contain' => false));

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

		// If a manufacturer is selected, let's filter our the filter data :)
		if (isset($searchFilters['m'])) {

			$this -> loadModel('CollectibletypesManufacture');

			$collectibleTypes = $this -> CollectibletypesManufacture -> find('all', array('order' => array('Collectibletype.name' => 'ASC'), 'contain' => array('Collectibletype'), 'conditions' => array('CollectibletypesManufacture.manufacture_id' => $searchFilters['m'])));
			$collectibleTypesSearchGroup = array();
			$collectibleTypesSearchGroup['filters'] = array();
			$collectibleTypesSearchGroup['selected'] = array();
			$collectibleTypesSearchGroup['label'] = __('Platform');
			$collectibleTypesSearchGroup['type'] = 'ct';
			$collectibleTypesSearchGroup['allowMultiple'] = 'true';

			foreach ($collectibleTypes as $key => $value) {
				$serachFilter = array();
				$serachFilter['id'] = $value['Collectibletype']['id'];
				$serachFilter['label'] = $value['Collectibletype']['name'];
				array_push($collectibleTypesSearchGroup['filters'], $serachFilter);
			}
			$collectibleTypesSearchGroup['filters'] = $this -> my_array_unique($collectibleTypesSearchGroup['filters']);
			array_push($searchFilterGroups, $collectibleTypesSearchGroup);

			$this -> loadModel('LicensesManufacture');

			$licenses = $this -> LicensesManufacture -> find('all', array('order' => array('License.name' => 'ASC'), 'contain' => array('License'), 'conditions' => array('LicensesManufacture.manufacture_id' => $searchFilters['m'])));
			$licenseSearchGroup = array();
			$licenseSearchGroup['filters'] = array();
			$licenseSearchGroup['selected'] = array();
			$licenseSearchGroup['label'] = __('Brand');
			$licenseSearchGroup['type'] = 'l';
			$licenseSearchGroup['allowMultiple'] = 'true';

			foreach ($licenses as $key => $value) {
				$serachFilter = array();
				$serachFilter['id'] = $value['License']['id'];
				$serachFilter['label'] = $value['License']['name'];
				array_push($licenseSearchGroup['filters'], $serachFilter);
			}
			$licenseSearchGroup['filters'] = $this -> my_array_unique($licenseSearchGroup['filters']);
			array_push($searchFilterGroups, $licenseSearchGroup);

		} else {
			$this -> loadModel('Collectibletype');
			$collectibleTypes = $this -> Collectibletype -> find("all", array('contain' => false, 'order' => array('Collectibletype.name' => 'ASC')));

			$collectibleTypesSearchGroup = array();
			$collectibleTypesSearchGroup['filters'] = array();
			$collectibleTypesSearchGroup['selected'] = array();
			$collectibleTypesSearchGroup['label'] = __('Platform');
			$collectibleTypesSearchGroup['type'] = 'ct';
			$collectibleTypesSearchGroup['allowMultiple'] = 'true';

			foreach ($collectibleTypes as $key => $value) {
				$serachFilter = array();
				$serachFilter['id'] = $value['Collectibletype']['id'];
				$serachFilter['label'] = $value['Collectibletype']['name'];
				array_push($collectibleTypesSearchGroup['filters'], $serachFilter);
			}
			array_push($searchFilterGroups, $collectibleTypesSearchGroup);

			$this -> loadModel('License');
			$licenses = $this -> License -> find("all", array('contain' => false, 'order' => array('License.name' => 'ASC')));

			$licenseSearchGroup = array();
			$licenseSearchGroup['filters'] = array();
			$licenseSearchGroup['selected'] = array();
			$licenseSearchGroup['label'] = __('Brand');
			$licenseSearchGroup['type'] = 'l';
			$licenseSearchGroup['allowMultiple'] = 'true';

			foreach ($licenses as $key => $value) {
				$serachFilter = array();
				$serachFilter['id'] = $value['License']['id'];
				$serachFilter['label'] = $value['License']['name'];
				array_push($licenseSearchGroup['filters'], $serachFilter);
			}
			array_push($searchFilterGroups, $licenseSearchGroup);

		}

		// always add all scales
		$this -> loadModel('Scale');
		$scales = $this -> Scale -> find("all", array('contain' => false));

		$scaleSearchGroup = array();
		$scaleSearchGroup['filters'] = array();
		$scaleSearchGroup['selected'] = array();
		$scaleSearchGroup['label'] = __('Scale');
		$scaleSearchGroup['type'] = 's';
		$scaleSearchGroup['allowMultiple'] = 'true';

		foreach ($scales as $key => $value) {
			$serachFilter = array();
			$serachFilter['id'] = $value['Scale']['id'];
			$serachFilter['label'] = $value['Scale']['scale'];
			array_push($scaleSearchGroup['filters'], $serachFilter);
		}
		array_push($searchFilterGroups, $scaleSearchGroup);

		$orderSearchGroup = array();
		$orderSearchGroup['filters'] = array();
		$orderSearchGroup['selected'] = array();
		$orderSearchGroup['label'] = __('Order');
		$orderSearchGroup['type'] = 'o';
		$orderSearchGroup['allowMultiple'] = 'false';

		$serachFilter = array();
		$serachFilter['id'] = 'a';
		$serachFilter['label'] = 'Ascending';
		array_push($orderSearchGroup['filters'], $serachFilter);

		$serachFilter = array();
		$serachFilter['id'] = 'd';
		$serachFilter['label'] = 'Descending';
		array_push($orderSearchGroup['filters'], $serachFilter);

		$serachFilter = array();
		$serachFilter['id'] = 'n';
		$serachFilter['label'] = 'Newest';
		array_push($orderSearchGroup['filters'], $serachFilter);

		$serachFilter = array();
		$serachFilter['id'] = 'o';
		$serachFilter['label'] = 'Oldest';
		array_push($orderSearchGroup['filters'], $serachFilter);

		array_push($searchFilterGroups, $orderSearchGroup);

		return $searchFilterGroups;

	}

	function my_array_unique($array, $keep_key_assoc = false) {
		$duplicate_keys = array();
		$tmp = array();

		foreach ($array as $key => $val) {
			// convert objects to arrays, in_array() does not support objects
			if (is_object($val))
				$val = (array)$val;

			if (!in_array($val, $tmp))
				$tmp[] = $val;
			else
				$duplicate_keys[] = $key;
		}

		foreach ($duplicate_keys as $key)
			unset($array[$key]);

		return $keep_key_assoc ? $array : array_values($array);
	}

}
?>