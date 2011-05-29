<?php
//TODO Convert most of this over to Stash or User controller...this is not needed anymore
//pr Stashes....then the index or default shows Stashes detail...the /stashes/stash/shows the stash info for that one
//need to handle looking at other peoples stashes that are not your own
// /username should route to stashes/detail
// /stashes/stash/username with an id will show that users stash
class CollectionsController extends AppController
{
  var $name = 'Collections';

  var $helpers = array('Html', 'Form','Ajax', 'FileUpload.FileUpload');

  var $components = array('RequestHandler');
  
  //This is set to null becaue we do not have a backing model
  var $uses = null;  

  public function beforeFilter()
  {
    parent::beforeFilter();
    //This is in reference to this set of code to allow JSON type in cakephp
    //http://www.pagebakers.nl/2007/06/05/using-json-in-cakephp-12/
    $this->RequestHandler->setContent('json', 'text/x-json');
  }


  
  public function viewCollectible($id=null)
  {
    if ($id == null) 
    {
      $this->Session->setFlash(__('Invalid collectible', true), null, null, 'error');
      $this->redirect(array('action' => 'index'));
    }
    $this->loadModel('CollectiblesUser');
    $collectible = $this->CollectiblesUser->getCollectibleDetail($id);
    debug($collectible);
    $this->set('collectible', $collectible);
    $this->loadModel('Collectible');
    $count = $this->Collectible->getNumberofCollectiblesInStash($collectible['Collectible']['id']);
    $this->set('collectibleCount', $count);
    
  }



  //TODO update this so it handles both logged in and not logged in
  function stash($username=null)
  {
      $id = 1;
	  $this -> loadModel('User');
     $result = $this->User->findByUsername($username);           
     //TODO need to chec to see if $id is valid
      $this->setStashIdSession($id);
      $data = $this->User->Stash->CollectiblesStash->find("all", array(
              'conditions'=>array('CollectiblesStash.stash_id'=>$id),
              //'limit' => 1,
              'contain'=>array(
              'Collectible' => array ( 'Manufacture','Collectibletype','Upload'))
              
          ));  
      $collectibleCount = $this->User->Stash->getNumberOfCollectiblesInStash($id);
      $this->set('collectibleCount', $collectibleCount );

      $this->set('collectibles',$data);  
  }
  
  function gallery()
  {

    $username = $this->getUsername();
    if ($username)
    {
    	$this -> loadModel('User');
       $joinRecords =  $this->User->getAllCollectiblesByUser($username);
           debug($joinRecords);
        $this->set('collection',$joinRecords);
    } 
    else 
    {
     $this->redirect(array('controller'=>'users','action' => 'login'), null, true);
    }  
  }
  
  //TODO make sure the one we are removing is for this user
	function remove($id =null) {
		if(!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect( array('action' => 'index'));
		} else {
			$username = $this -> getUsername();
			if($username) {
				$this -> loadModel('User');
				if($this -> User -> CollectiblesUser -> delete($id, false)) {
					$this -> Session -> setFlash(__('Collectible was successfully removed.', true), null, null, 'success');
					//WHERE DO I GO BACK TO?
					$this -> redirect( array('controller'=> 'stashs', 'action' => 'index'), null, true);
				}
			} else {
				$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
			}
		}
	}  

  function addSearch()
  {        
    //check to see if the user is logged in
    //shouldn't have access to this if they are not logged in
    $username = $this->getUsername();
    $this->setStashIdSession($this->params['named']['stashId']);

    if ($username)
    {
		$this->searchCollectible();
		$this -> loadModel('Condition');
		$this -> set('conditions', $this -> Condition -> find('list', array('order' => 'name')));
		$this -> loadModel('Merchant');
		$this -> set('merchants', $this -> Merchant -> find('list', array('order' => 'name')));
		$this -> set('stashId', $this -> params['named']['stashId']);
     	$this->render('add');

    }
    else
    {
      $this->redirect(array('controller'=>'users','action' => 'login'), null, true);
    }
  }

  public function add()
  {
    $username = $this->getUsername();
    if ($username)
    {
      if(!empty($this->data))
      {
        if ($this->RequestHandler->isAjax()) 
        {
          Configure::write('debug', 0);
          //$this->render('../json/add'); 
          
        }

       // $this->data['CollectiblesUsersStash']['data_added'] = date("Y-m-d H:i:s", time());
       // $this->data['CollectiblesUsersStash']['collectibles_user_id'] = '1'; 
        //$addData['CollectiblesStash']['stash_id'] = $this->params['named']['stashId'];        
        $this->data['CollectiblesUser']['user_id'] = $this->getUserId();
        
        $collectible_id = $this->data['CollectiblesUser']['collectible_id'];
		debug($this->data);
		$this->loadModel('Collectible');
		$this->Collectible->recursive = -1; 
		$collectible = $this->Collectible->findById($collectible_id);
		debug($collectible);
		//Save as a different name so the saveAll doesn't accidently save it
		$this->data['TempCollectible'] = $collectible['Collectible'];
        
        debug($this->data);
        $this -> loadModel('User');
        if($this->User->CollectiblesUser->saveAll($this->data))
        {
           //$this->redirect(array('action' => 'index'), "null", true);
          // $this->Session->setFlash(__('Collectible was successfully added.', true));
          // $this->redirect(array('controller' => 'collections', 'action' => 'index'),null, true); 
          
          //TODO pass a success message
          $this->set('aPosts', array('success'=> array('isSuccess'=>true,'message'=>__('Your collectible has successfully been added.', true))));
        }
        else
        {
           $this->set('aPosts', array('success'=>array('isSuccess'=>false),'isTimeOut'=> false, 'errors'=>array($this->User->CollectiblesUser->validationErrors))); 
        }
      }                   
    }  
    else
    {
      	$this->set('aPosts', array('success'=>array('isSuccess'=>false),'isTimeOut'=> true)); 
    }  
  }
  
  function editCollectible($id =null) {
		$this->checkLogIn();
		$this -> loadModel('User');
		debug($this->data);		
		debug($id);		
		if(!empty($this -> data)) {
			$fieldList = array('edition_size', 'cost', 'condition_id', 'merchant_id');
			if($this->User->CollectiblesUser->save($this -> data, true, $fieldList))
			{
				$this -> Session -> setFlash(__('Collectible was successfully updated.', true), null, null, 'success');
				$this -> redirect( array('controller'=> 'stashs', 'action' => 'index'), null, true);	
			}
			else
			{
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			}	
		} else {
			$username = $this -> getUsername();
			$joinRecords = $this -> User -> getCollectibleByUser($username, $id);
			$this -> loadModel('Condition');
			$this -> set('conditions', $this -> Condition -> find('list', array('order' => 'name')));
			$this -> loadModel('Merchant');
			$this -> set('merchants', $this -> Merchant -> find('list', array('order' => 'name')));
			$this -> set('collectible', $joinRecords);
		}
	}

	public function who($id =null) {
		if($this -> isLoggedIn()) {
			if($id) {
				$this -> loadModel('CollectiblesUser');
				$usersWho = $this -> CollectiblesUser -> getListOfUsersWho($id);
				debug($usersWho);
				$this -> set('usersWho', $usersWho);
			}

		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
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






