<?php
class AppController extends Controller {

	//var $components = array('RequestHandler');
	public function beforeFilter() {

		if ($this -> isLoggedIn()) {
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
		if (isset($this -> params['url'])) {
			foreach ($this->params['url'] as $key => $value) {
				if ($key !== 'ext' && $key !== 'url') {
					$requestParams = $requestParams . $key . '=' . $value;
				}
			}
		}
		$this -> set(compact('request_params'), $requestParams);
	}

	public function getUser() {
		return $this -> Session -> read('user');
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
		$manufactures = $this -> Session -> read('Manufacture_Search.filter');

		if (!isset($manufactures)) {
			$this -> loadModel('Manufacture');
			$manufactures = $this -> Manufacture -> getManufactureSearchData();
			$this -> Session -> write('Manufacture_Search.filter', $manufactures);
		}

		debug($manufactures);

		$collectibleTypes = $this -> Session -> read('CollectibleType_Search.filter');

		if (!isset($collectibleTypes)) {
			$this -> loadModel('Collectibletype');
			$collectibleTypes = $this -> Collectibletype -> getCollectibleTypeSearchData();
			$this -> Session -> write('CollectibleType_Search.filter', $collectibleTypes);
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

	public function searchCollectible($conditions = null) {
		//TODO clean up this code
		$this -> loadModel('Collectible');
		debug($this -> data);
		$this -> setSearchData();
		//This is my way of resetting the search, better way?
		//If you fail to reset the search you might get unreliable results, depending on what
		//you previously searched on
		if (!empty($this -> params['named']['initial'])) {
			if ($this -> params['named']['initial'] == 'yes') {
				$this -> Session -> delete('Collectibles.search');
				$this -> Session -> delete('Collectibles.filters');
				$this -> Session -> delete('Collectibles.userSearchFields');
				$this -> Session -> delete('Collectibles.tagFilter');
				debug($this -> data);
			}
		} else if (!empty($this -> params['url']['initial'])) {
			if ($this -> params['url']['initial'] == 'yes') {
				$this -> Session -> delete('Collectibles.search');
				$this -> Session -> delete('Collectibles.filters');
				$this -> Session -> delete('Collectibles.userSearchFields');
				$this -> Session -> delete('Collectibles.tagFilter');
				debug($this -> data);
			}
		}
		//Means I probably didn't post an actual search
		if (!empty($this -> data)) {
			if (isset($this -> data['Search']['search'])) {
				$search = $this -> data['Search']['search'];
			}

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
			debug($manFilters);
			array_push($manFilters[0]['AND'], array('OR' => array()));
			$filtersSet = false;
			$manufactureFilterSet = false;
			if (isset($this -> data['Search']['Manufacture'])) {
				foreach ($this->data['Search']['Manufacture']['Filter'] as $key => $value) {
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
			debug($filters);

			$typeFilters = array();
			array_push($typeFilters, array('AND' => array()));
			array_push($typeFilters[0]['AND'], array('OR' => array()));
			$collectibletypeFilterSet = false;
			if (isset($this -> data['Search']['CollectibleType'])) {
				foreach ($this->data['Search']['CollectibleType']['Filter'] as $key => $value) {
					if ($value != 0) {
						array_push($typeFilters[0]['AND'][0]['OR'], array('Collectible.collectibletype_id' => $key));
						$filtersSet = true;
						$collectibletypeFilterSet = true;
					}
				}
			}
			debug($typeFilters);
			if ($collectibletypeFilterSet) {
				array_push($filters, $typeFilters);
			}
			debug($filters);

			$licenseFilters = array();
			array_push($licenseFilters, array('AND' => array()));
			array_push($licenseFilters[0]['AND'], array('OR' => array()));
			$licenseFilterSet = false;
			if (isset($this -> data['Search']['License'])) {
				foreach ($this->data['Search']['License']['Filter'] as $key => $value) {
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
			debug($filters);

			$tagFilters = array();
			//array_push($tagFilters, array('AND' => array()));
			//array_push($tagFilters[0]['AND'], array('OR' => array()));
			$tagFilterSet = false;
			if (isset($this -> data['Search']['Tag'])) {
				foreach ($this->data['Search']['Tag']['Filter'] as $key => $value) {
					if ($value != 0) {
						array_push($tagFilters, array('Tag.id' => $key));
						$filtersSet = true;
						$tagFilterSet = true;

					}
				}
			}
			/*
			 * If this is set, write it to the session so that we know
			 * we are using a tag filter to search on.  We need to make
			 * sure the search is correct when this is happening.  If it is not
			 * set, make sure we delete it.
			 */
			if ($tagFilterSet) {
				array_push($filters, $tagFilters);
				$this -> Session -> write('Collectibles.tagFilter', true);
			} else {
				$this -> Session -> delete('Collectibles.tagFilter');
			}
			debug($filters);
			$this -> Session -> write('Collectibles.userSearchFields', $this -> data['Search']);
			debug($this -> data['Search']);
		} else {
			if ($this -> Session -> check('Collectibles.search')) {
				$search = $this -> Session -> read('Collectibles.search');
			}
			if ($this -> Session -> check('Collectibles.filters')) {
				$filters = $this -> Session -> read('Collectibles.filters');
			}
			if ($this -> Session -> check('Collectibles.tagFilter')) {
				$tagFilterSet = true;
			}
		}

		if (!isset($filters)) {
			$filters = array();
		}
		debug($filters);
		//Save off my filters, does this make sense here?
		$this -> Session -> write('Collectibles.filters', $filters);

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
		debug($joins);
		$listSize = Configure::read('Settings.Search.list-size');
		debug($listSize);
		array_push($conditions, array('Collectible.state' => '0'));
		//See if a search was set
		if (isset($search)) {
			//This saves off the filters, and the search query for pagination
			$this -> Session -> write('Collectibles.search', $search);
			//Is the search an empty string?
			if ($search == '') {
				debug($conditions);
				debug($joins);
				debug($filters);
				$this -> paginate = array("joins" => $joins, "conditions" => array($conditions, $filters), "contain" => array('SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'Upload', 'CollectiblesTag' => array('Tag')), 'limit' => $listSize);
			} else {
				//Using like for now because switch to InnoDB
				array_push($conditions, array('Collectible.name LIKE' => '%' . $search . '%'));
				//array_push($conditions, array("MATCH(Collectible.name) AGAINST('{$search}' IN BOOLEAN MODE)"));
				$this -> paginate = array("joins" => $joins, "conditions" => array($conditions, $filters), "contain" => array('SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'Upload', 'CollectiblesTag' => array('Tag')), 'limit' => $listSize);
			}
		} else {
			//This a search based on filters, not a search string
			$this -> paginate = array("joins" => $joins, "contain" => array('SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'Upload', 'CollectiblesTag' => array('Tag')), 'conditions' => array($conditions, $filters), 'limit' => $listSize);
		}

		$data = $this -> paginate('Collectible');
		debug($data);
		$this -> set('collectibles', $data);
	}

}
?>