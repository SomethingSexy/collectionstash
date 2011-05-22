<?php
class AppController extends Controller {

	//var $components = array('RequestHandler');

	public function beforeFilter() {
		if($this -> isLoggedIn()) {
			$this -> set('isLoggedIn', true);
			$this -> set('username', $this -> getUsername());
			if($this -> isUserAdmin()) {
				$this -> set('isUserAdmin', true);
			} else {
				$this -> set('isUserAdmin', false);
			}
		} else {
			$this -> set('isLoggedIn', false);
			$this -> set('isUserAdmin', false);
		}
	}

	public function getUser() {
		return $this -> Session -> read('user');
	}

	public function getUsername() {
		$user = $this -> getUser();
		return $user['User']['username'];
	}

	public function isLoggedIn() {
		if($this -> getUser()) {
			return true;
		} else {
			return false;
		}
	}

	public function isUserAdmin() {
		$user = $this -> getUser();

		if($user['User']['admin'] == 1) {
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

		if(!isset($manufactures)) {
			$this -> loadModel('Manufacture');
			$manufactures = $this -> Manufacture -> getManufactureSearchData();
			$this -> Session -> write('Manufacture_Search.filter', $manufactures);
		}

		debug($manufactures);

		$collectibleTypes = $this -> Session -> read('CollectibleType_Search.filter');

		if(!isset($collectibleTypes)) {
			$this -> loadModel('Collectibletype');
			$collectibleTypes = $this -> Collectibletype -> getCollectibleTypeSearchData();
			$this -> Session -> write('CollectibleType_Search.filter', $collectibleTypes);
		}
	}

	public function handleNotLoggedIn() {
		$this -> Session -> setFlash('Your session has timed out.');
		$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
	}

	/**
	 * This method will check if the user is logged in, if they are not it will
	 * auto redirect them.
	 */
	public function checkLogIn() {
		if(!$this -> isLoggedIn()) {
			$this -> handleNotLoggedIn();
		}
	}

	public function searchCollectible($conditions =null) {
		//TODO clean up this code
		$this -> loadModel('Collectible');
		debug($this -> data);
		$this -> setSearchData();

		if(!empty($this -> params['named']['initial'])) {
			if($this -> params['named']['initial'] == 'yes') {
				$this -> Session -> delete('Collectibles.search');
				$this -> Session -> delete('Collectibles.filters');
				$this -> Session -> delete('Collectibles.userSearchFields');
			}
		}

		if(!empty($this -> data)) {
			$search = $this -> data['Search']['search'];
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
			array_push($filters, array('AND' => array()));
			array_push($filters[0]['AND'], array('OR' => array()));
			$filtersSet = false;
			$manufactureFilterSet = false;
			foreach($this->data['Search']['Manufacture']['Filter'] as $key => $value) {
				if($value != 0) {
					array_push($filters[0]['AND'][0]['OR'], array('Collectible.manufacture_id' => $key));
					$filtersSet = true;
					$manufactureFilterSet = true;
				}
			}

			array_push($filters, array('AND' => array()));
			array_push($filters[1]['AND'], array('OR' => array()));
			$collectibletypeFilterSet = false;
			foreach($this->data['Search']['CollectibleType']['Filter'] as $key => $value) {
				if($value != 0) {
					array_push($filters[1]['AND'][0]['OR'], array('Collectible.collectibletype_id' => $key));
					$filtersSet = true;
					$collectibletypeFilterSet = true;
				}
			}
			//These two if checks make sure that we are not setting arrays if there
			//is nothing being searched on.  This stops offset database issue and
			//database issues when there is no left join
			if(!$collectibletypeFilterSet) {
				array_pop($filters);

			}
			if(!$manufactureFilterSet) {
				$filters = array_reverse($filters);
				array_pop($filters);
				$filters = array_reverse($filters);
			}

			if(!$filtersSet) {
				$filters = array();
			}
			debug($filters);
			$this -> Session -> write('Collectibles.userSearchFields', $this -> data['Search']);
			debug($this -> data['Search']);

		} elseif($this -> Session -> check('Collectibles.search')) {
			$search = $this -> Session -> read('Collectibles.search');
			$filters = $this -> Session -> read('Collectibles.filters');
		}

		//Some conditions were added
		if(!is_array($conditions)) {
			$conditions = array();
		}

		if(isset($search)) {
			$this -> Session -> write('Collectibles.search', $search);
			$this -> Session -> write('Collectibles.filters', $filters);
			if($search == '') {
				array_push($conditions, array('Approval.state' => '0'));
				$this -> paginate = array("conditions" => array($conditions, $filters), "contain" => array('Manufacture', 'Collectibletype', 'Upload', 'Approval'), 'limit' => 25);
			} else {
				array_push($conditions, array('Approval.state' => '0'));
				//Using like for now because switch to InnoDB
				array_push($conditions, array('Collectible.name LIKE' => '%' . $search . '%'));
				//array_push($conditions, array("MATCH(Collectible.name) AGAINST('{$search}' IN BOOLEAN MODE)"));
				$this -> paginate = array("conditions" => array($conditions, $filters), "contain" => array('Manufacture', 'Collectibletype', 'Upload', 'Approval'), 'limit' => 25);
			}
		} else {
			array_push($conditions, array('Approval.state' => '0'));
			$this -> paginate = array("contain" => array('Manufacture', 'Collectibletype', 'Upload', 'Approval'), 'conditions' => array($conditions), 'limit' => 25);
		}

		$data = $this -> paginate('Collectible');
		debug($data);
		$this -> set('collectibles', $data);
	}

}
?>