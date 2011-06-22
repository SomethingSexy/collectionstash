<?php

App::import('Sanitize');
class StashsController extends AppController {
	var $name = 'Stashs';

	var $helpers = array('Html', 'Form', 'Ajax', 'FileUpload.FileUpload');

	var $components = array('RequestHandler');


	function stats ($id = null) {
		//This is private stuff for sure make sure they are logged in
		$this -> checkLogIn();		
		
		if($id) {
			$id = Sanitize::clean($id, array('encode' => false));
			$stash = $this -> Stash -> findById($id);	
			//Check to make sure that the user id tied to this stash is the one logged in
			if($stash['Stash']['user_id'] == $this -> getUserId()) {
				$stashStats = $this -> Stash -> getStashStats($id); //CollectiblesUser -> find('all', array('conditions' => array('CollectiblesUser.stash_id'=>$id), 'contain'=> false));	
				$this -> set(compact('stashStats'));
			} else {
				$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);	
			}
			
		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
		
	}


	//Use the id is the id of the stash we are viewing
	function index($id = null) {
		//Add something in here that if they aren't logged in and they pass in an id, we return that users collection
		$username = $this -> getUsername();
		if($username) {
			if($id) {
				$this -> setStashIdSession($id);
			} else {
				$id = $this -> getStashIdSession();
			}

			if($id) {
				$result = $this -> Stash -> User -> findByUsername($username);
				$this -> loadModel('Stash');

				//TODO need to chec to see if $id is valid
				$this -> setStashIdSession($id);
				/*
				 * Right now, I am searching and paginating on a stash because
				 * that is what I am viewing.  Really this should be done in the stashs
				 * controller and not here since I am trying to get rid of this controller.
				 *
				 * This will let me eventually have other types attached to one stash.
				 */
				$this -> paginate = array('conditions' => array('id' => $id), 'limit' => 20, 'contain' => array('CollectiblesUser' => array('Collectible' => array('Manufacture','License', 'Collectibletype', 'Upload'))));

				//        $this->paginate = array('conditions'=>array('CollectiblesUser.stash_id'=>$id),
				//                'limit' => 20,
				//                'contain'=>array(
				//                'Collectible' => array ( 'Manufacture','Collectibletype','Upload'))
				//        );

				// $data = $this->User->CollectiblesUser->find("all", array(
				//         'conditions'=>array('CollectiblesUser.stash_id'=>$id),
				//         //'limit' => 1,
				//         'contain'=>array(
				//         'Collectible' => array ( 'Manufacture','Collectibletype','Upload'))
				//
				//     ));
				$collectibleCount = $this -> Stash -> getNumberOfCollectiblesInStash($id);
				$this -> set('collectibleCount', $collectibleCount);
				$data = $this -> paginate('Stash');
				debug($data[0]['CollectiblesUser']);
				$this -> set('collectibles', $data[0]['CollectiblesUser']);
			} else {
				$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
			}
		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	public function beforeFilter() {
		parent::beforeFilter();
		//This is in reference to this set of code to allow JSON type in cakephp
		//http://www.pagebakers.nl/2007/06/05/using-json-in-cakephp-12/
		$this -> RequestHandler -> setContent('json', 'text/x-json');
	}

	public function edit() {
	$this -> data = Sanitize::clean($this -> data, array('encode' => false));
	$username = $this -> getUsername();
	if($username) {//check to see they passed in data for the add
			if(!empty($this -> data)) {
				if($this -> RequestHandler -> isAjax()) {
					Configure::write('debug', 0);
				}
				
				//Grab the stash we are trying to edit
				$stashForEdit = $this -> Stash -> findById($this -> data['Stash']['id']);
				debug($stashForEdit);
				$userId = $this -> getUserId();
				$this -> loadModel('User');
				$this -> User -> id = $this -> getUserId();
				$this -> data['Stash']['user_id'] = $userId;
				$user = $this -> User -> find();
				$this -> data['Stash']['total_count'] = $user['User']['stash_count'];
				debug($this->data);
				//Check to make sure that the user id tied to this stash is the one logged in
				if($stashForEdit['Stash']['user_id'] == $this -> getUserId()) {
					$this -> Stash -> id = $this -> data['Stash']['id'];
					if($this -> Stash -> save($this -> data, true)) {
						$this -> set('aStash', array('success' => array('isSuccess' => true, 'message' =>     __('You have successfully edited your Stash.', true))));
					} else {
						$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'errors' => array($this -> User -> Stash -> validationErrors)));
					}
				} else {
					//$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
				}

			} else {
				//$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
			}
		} else {
			//$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
		}
	}

	public function stashList() {
		if($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
		}
		$username = $this -> getUsername();
		if($username) {
			$user = $this -> getUser();
			$this -> loadModel('User');
			$stashCount = $this -> User -> getNumberOfStashesByUser($username);
			$stashDetails = $this -> User -> Stash -> getStashDetails($user['User']['id']);
			$this -> set('aStash', $stashDetails);
		} else {
			$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
		}

	}

	public function add() {
		$this -> data = Sanitize::clean($this -> data, array('encode' => false));
		$username = $this -> getUsername();
		if($username) {
			//check to see they passed in data for the add
			if(!empty($this -> data)) {

				if($this -> RequestHandler -> isAjax()) {
					Configure::write('debug', 0);
				}

				$userId = $this -> getUserId();
				$this -> loadModel('User');
				$this -> User -> id = $this -> getUserId();
				$this -> data['Stash']['user_id'] = $userId;
				$user = $this -> User -> find();
				$this -> data['Stash']['total_count'] = $user['User']['stash_count'];
				$this -> User -> Stash -> create();
				if($this -> User -> Stash -> save($this -> data)) {
					$this -> set('aStash', array('success' => array('isSuccess' => true, 'message' =>     __('You have successfully created a new Stash.', true))));
				} else {
					$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'errors' => array($this -> User -> Stash -> validationErrors)));
				}
			} else {
				$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
			}
		} else {
			$this -> set('aStash', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
		}
	}

	public function remove() {
		$this -> autoRender = false;
		$username = $this -> getUsername();
		if($username) {
			//check to see they passed in data for the add
			if(!empty($this -> data)) {
				//Grab the stash we are trying to remove
				$stashForEdit = $this -> Stash -> findById($this -> data['Stash']['id']);

				//Check to make sure that the user id tied to this stash is the one logged in
				if($stashForEdit['Stash']['user_id'] == $this -> getUserId()) {

					$this -> Stash -> id = $this -> data['Stash']['id'];
					if($this -> Stash -> delete()) {
						$this -> Session -> setFlash(__('You have successfully deleted stash ' . $stashForEdit['Stash']['name'] . '.', true), null, null, 'success');
						$this -> redirect( array('controller' => 'users', 'action' => 'home'), null, true);
					}
				}
			}
		}

		$this -> Session -> setFlash(__('There was a problem trying to delete your stash.', true), null, null, 'success');
		$this -> redirect( array('controller' => 'users', 'action' => 'home'), null, true);
	}
	
	public function view($id = null) {
		debug($id);	
		//debug($this->Stash->findById($id));
		
		/*debug($this->Stash->find('all', array(
			'conditions' => array('Stash.id' => $id),
				'contain'=> array('CollectiblesUser' =>array('Collectible'=> array( 'conditions' => array('id' =>'217'))),
					
				'PostersUser'),

			)));*/
			$this -> loadModel('CollectiblesUser');
		debug($this->CollectiblesUser->find('all', array(
			'conditions' => array('CollectiblesUser.user_id' => '14', 'Collectible.name'=>'test'))));	
			
		//Find all collectible users whose collectible id is 217 that are in tash 62	
		debug($this->Stash->find('all', array(
			'conditions' => array('Stash.id' => '62'),
			'contain' => array('CollectiblesUser'=> array('conditions'=>array('collectible_id'=>'217')))
			
			)));	
		//Find all collectible users and poster users for stash id 62
		debug($this->Stash->find('all', array(
			'conditions' => array('Stash.id' => '62'),
			'contain' => array('CollectiblesUser','PostersUser')
			
			)));	
			
			
			debug($this->Stash->query('SELECT id, stash_id FROM collectibles_users 
                         UNION SELECT id, stash_id FROM posters_users'));
			

		/*$this -> loadModel('CollectiblesUser');
		$this->CollectiblesUser->bindModel(array('hasMany' => array('PostersUser'))); 
		debug($this->CollectiblesUser->find('all', array(
			'conditions' => array('CollectiblesUser.stash_id' => '62'),
			'fields'=>array('id','stash_id'),
			//'contain'=> array('CollectiblesUser','PostersUser'),
			'joins' => array(
			 //array(  'table' => 'collectibles_users',
			 // 'alias' => 'CollectiblesUser',
			 // 'type' => 'LEFT',
			 // 'conditions' => array(
			 //  'CollectiblesUser.stash_id = 62',
			 // )
			 //),
			 array(  'table' => 'posters_users',
			  'alias' => 'PostersUser',
			  'type' => 'LEFT',
			  'fields'=>array('id','stash_id'),
			  'conditions' => array(
			   'PostersUser.stash_id = 62',
			  )
			 )
			)
			)));*/
		
		
		
	}

	private function setStashIdSession($id) {
		if($id) {
			$this->Session->write('stashId',$id);
		}
	}

	private function getStashIdSession() {
		return $this->Session->read('stashId');
	}

}
?>