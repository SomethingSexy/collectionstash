<?php
App::uses('AuthComponent', 'Controller/Component');

class AppController extends Controller {

	public function beforeFilter() {
		if ($this -> request -> isAjax()) {
			Configure::write('debug', 0);
			$this -> layout = 'ajax';
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

	public function setSearchData() {
		/*
		 * This data is read off of the search.ctp to draw the filter boxes, if you update
		 * this make sure you update the corresponding search.ctp files
		 *
		 * TODO: Need to update this so that if a specific manufacture is selected
		 * we only return those collectible types
		 */
		$manufactures = $this -> Session -> read('Manufacture_Search.filter');

		if (!isset($manufactures)) {
			$this -> loadModel('Manufacture');
			$manufactures = $this -> Manufacture -> getManufactureSearchData();
			$this -> Session -> write('Manufacture_Search.filter', $manufactures);
		}

		// debug($manufactures);

		// $collectibleTypes = $this -> Session -> read('CollectibleType_Search.filter');

		//For now, we allow one manufacture filter, if that is set regardless lets also
		//reget the types
		if (isset($this -> request -> data['Search']['Manufacture']['Filter'])) {
			$this -> loadModel('CollectibletypesManufacture');
			//Since we only allow one, for now but the interface is setup to handle multiple do this
			reset($this -> request -> data['Search']['Manufacture']['Filter']);
			// make sure array pointer is at first element
			$firstKey = key($this -> request -> data['Search']['Manufacture']['Filter']);

			$collectibleTypes = $this -> CollectibletypesManufacture -> getAllCollectibleTypeByManufactureId($firstKey);
			$this -> Session -> write('CollectibleType_Search.filter', $collectibleTypes);

			$this -> loadModel('LicensesManufacture');

			$licenses = $this -> LicensesManufacture -> getFullLicensesByManufactureId($firstKey);
			// debug($licenses);
			$this -> Session -> write('License_Search.filter', $licenses);

		} else {
			$this -> loadModel('Collectibletype');
			$collectibleTypes = $this -> Collectibletype -> getCollectibleTypeSearchData();
			$this -> Session -> write('CollectibleType_Search.filter', $collectibleTypes);

			$this -> loadModel('License');
			$licenses = $this -> License -> getLicenses();
			$this -> Session -> write('License_Search.filter', $licenses);
		}

		if (isset($this -> request -> data['Search']['Tag']['Filter'])) {
			reset($this -> request -> data['Search']['Tag']['Filter']);
			// make sure array pointer is at first element
			$firstKey = key($this -> request -> data['Search']['Tag']['Filter']);
			$this -> loadModel('Tag');
			$tag = $this -> Tag -> find("first", array('conditions' => array('Tag.id' => $firstKey)));
			$this -> Session -> write('Tag_Search.filter', $tag);
		} else {
			//If a tag is not set on this search, remove this from the session to be safe
			$this -> Session -> delete('Tag_Search.filter');
		}

		// debug($collectibleTypes);
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
	 */
	public function searchCollectible($conditions = null) {
		//TODO clean up this code
		$this -> loadModel('Collectible');
		// debug($this->request->data);
		$saveSearchFilters = array();
		if (isset($this -> request -> query['q'])) {
			$this -> request -> data['Search'] = array();
			$this -> request -> data['Search']['search'] = '';
			$this -> request -> data['Search']['search'] = $this -> request -> query['q'];
		}
		if (isset($this -> request -> query['m'])) {
			//find all of this license
			$this -> request -> data['Search']['Manufacture'] = array();
			$this -> request -> data['Search']['Manufacture']['Filter'] = array();
			$this -> request -> data['Search']['Manufacture']['Filter'][$this -> request -> query['m']] = 1;
			$saveSearchFilters['manufacturer'] = $this -> request -> query['m'];
		}
		if (isset($this -> request -> query['l'])) {
			//find all of this license
			$this -> request -> data['Search']['License'] = array();
			$this -> request -> data['Search']['License']['Filter'] = array();
			$this -> request -> data['Search']['License']['Filter'][$this -> request -> query['l']] = 1;
			$saveSearchFilters['license'] = $this -> request -> query['l'];
		}
		if (isset($this -> request -> query['ct'])) {
			//find all of this license
			$this -> request -> data['Search']['CollectibleType'] = array();
			$this -> request -> data['Search']['CollectibleType']['Filter'] = array();
			$this -> request -> data['Search']['CollectibleType']['Filter'][$this -> request -> query['ct']] = 1;
			$saveSearchFilters['collectibletype'] = $this -> request -> query['ct'];
		}
		if (isset($this -> request -> query['t'])) {
			//find all of this license
			$this -> request -> data['Search']['Tag'] = array();
			$this -> request -> data['Search']['Tag']['Filter'] = array();
			$this -> request -> data['Search']['Tag']['Filter'][$this -> request -> query['t']] = 1;
			$saveSearchFilters['tag'] = $this -> request -> query['t'];
		}
		if (isset($this -> request -> query['o'])) {
			//find all of this license
			$this -> request -> data['Search']['Order'] = array();
			$this -> request -> data['Search']['Order']['Filter'] = array();
			//Just setting a single filter for these now
			$this -> request -> data['Search']['Order']['Filter'] = $this -> request -> query['o'];
			$saveSearchFilters['order'] = $this -> request -> query['o'];
		} else {
			$saveSearchFilters['order'] = 'a';
		}
		if (isset($this -> request -> data['Search']['search']) && trim($this -> request -> data['Search']['search']) !== '') {
			$search = $this -> request -> data['Search']['search'];
			$search = ltrim($search);
			$search = rtrim($search);
			$saveSearchFilters['search'] = $search;
		}

		$this -> set(compact('saveSearchFilters'));
		$this -> setSearchData();
		//    array(
		//       'OR'=>array(
		//          array('Company.status' => 'active'),
		//          'NOT'=>array(
		//             array('Company.status'=> array('inactive', 'suspended'))
		//          )
		//       )
		//   )
		// )
		// array(
		// 'OR' => array(
		//    array('Collectible.manufacture_id' => 'Future Holdings'),
		//    array('Collectible.manufacture_id' => 'Future Holdings')
		// ));
		$filters = array();
		$manFilters = array();
		array_push($manFilters, array('AND' => array()));
		//	debug($manFilters);
		array_push($manFilters[0]['AND'], array('OR' => array()));
		$filtersSet = false;
		$manufactureFilterSet = false;
		if (isset($this -> request -> data['Search']['Manufacture'])) {
			foreach ($this -> request -> data['Search']['Manufacture']['Filter'] as $key => $value) {
				if ($value != 0) {
					array_push($manFilters[0]['AND'][0]['OR'], array('Collectible.manufacture_id' => $key));
					$filtersSet = true;
					$manufactureFilterSet = true;
				}
			}
		}

		if ($manufactureFilterSet) {
			array_push($filters, $manFilters);
		}
		//debug($filters);

		$typeFilters = array();
		array_push($typeFilters, array('AND' => array()));
		array_push($typeFilters[0]['AND'], array('OR' => array()));
		$collectibletypeFilterSet = false;
		if (isset($this -> request -> data['Search']['CollectibleType'])) {
			foreach ($this-> request -> data['Search']['CollectibleType']['Filter'] as $key => $value) {
				if ($value != 0) {
					array_push($typeFilters[0]['AND'][0]['OR'], array('Collectible.collectibletype_id' => $key));
					$filtersSet = true;
					$collectibletypeFilterSet = true;
				}
			}
		}
		//debug($typeFilters);
		if ($collectibletypeFilterSet) {
			array_push($filters, $typeFilters);
		}
		//debug($filters);

		$licenseFilters = array();
		array_push($licenseFilters, array('AND' => array()));
		array_push($licenseFilters[0]['AND'], array('OR' => array()));
		$licenseFilterSet = false;
		if (isset($this -> request -> data['Search']['License'])) {
			foreach ($this -> request -> data['Search']['License']['Filter'] as $key => $value) {
				if ($value != 0) {
					array_push($licenseFilters[0]['AND'][0]['OR'], array('Collectible.license_id' => $key));
					$filtersSet = true;
					$licenseFilterSet = true;
				}
			}
		}
		if ($licenseFilterSet) {
			array_push($filters, $licenseFilters);
		}
		//debug($filters);

		$tagFilters = array();
		$tagFilterSet = false;
		if (isset($this -> request -> data['Search']['Tag'])) {
			foreach ($this -> request -> data['Search']['Tag']['Filter'] as $key => $value) {
				if ($value != 0) {
					array_push($tagFilters, array('Tag.id' => $key));
					$filtersSet = true;
					$tagFilterSet = true;

				}
			}
		}

		if (isset($this -> request -> data['Search']['Order'])) {
			$orderType = $this -> request -> data['Search']['Order']['Filter'];
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
		} else {
			//If nothing is set, use alphabetical order as the default
			$order = array();
			$order['Collectible.name'] = 'ASC';
		}

		/*
		 * If this is set, write it to the session so that we know
		 * we are using a tag filter to search on.  We need to make
		 * sure the search is correct when this is happening.  If it is not
		 * set, make sure we delete it.
		 */
		if ($tagFilterSet) {
			array_push($filters, $tagFilters);

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
		if (isset($tagFilterSet) && $tagFilterSet) {
			array_push($joins, array('table' => 'collectibles_tags', 'alias' => 'CollectiblesTag', 'type' => 'inner', 'conditions' => array('Collectible.id = CollectiblesTag.collectible_id')));
			array_push($joins, array('table' => 'tags', 'alias' => 'Tag', 'type' => 'inner', 'conditions' => array('CollectiblesTag.tag_id = Tag.id')));
		}

		$listSize = Configure::read('Settings.Search.list-size');

		array_push($conditions, array('Collectible.state' => '0'));
		//See if a search was set
		if (isset($search)) {
			//Is the search an empty string?
			if ($search == '') {
				$this -> paginate = array("joins" => $joins, 'order' => $order, "conditions" => array($conditions, $filters), "contain" => array('SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'Upload', 'CollectiblesTag' => array('Tag')), 'limit' => $listSize);
			} else {
				//Using like for now because switch to InnoDB
				$test = array();
				array_push($test, array('AND' => array()));
				array_push($test[0]['AND'], array('OR' => array()));
				array_push($test[0]['AND'][0]['OR'], array('Collectible.name LIKE' => '%' . $search . '%'));
				array_push($test[0]['AND'][0]['OR'], array('License.name LIKE' => '%' . $search . '%'));

				array_push($conditions, $test);
				$this -> paginate = array("joins" => $joins, 'order' => $order, "conditions" => array($conditions, $filters), "contain" => array('SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'Upload', 'CollectiblesTag' => array('Tag')), 'limit' => $listSize);
			}
		} else {
			//This a search based on filters, not a search string
			$this -> paginate = array("joins" => $joins, 'order' => $order, "contain" => array('SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'Upload', 'CollectiblesTag' => array('Tag')), 'conditions' => array($conditions, $filters), 'limit' => $listSize);
		}

		$data = $this -> paginate('Collectible');
		$this -> set('collectibles', $data);
	}

}
?>