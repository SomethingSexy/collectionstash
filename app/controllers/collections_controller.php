<?php
//probably should rename this to Stash or something
//pr Stashes....then the index or default shows Stashes detail...the /stashes/stash/shows the stash info for that one
//need to handle looking at other peoples stashes that are not your own
// /username should route to stashes/detail
// /stashes/stash/username with an id will show that users stash
class CollectionsController extends AppController
{
  var $name = 'Collections';

  var $helpers = array('Html', 'Form','Ajax', 'FileUpload.FileUpload');

  var $components = array('RequestHandler');
  
  var $uses = array('User');

  public function beforeFilter()
  {
    parent::beforeFilter();
    //This is in reference to this set of code to allow JSON type in cakephp
    //http://www.pagebakers.nl/2007/06/05/using-json-in-cakephp-12/
    $this->RequestHandler->setContent('json', 'text/x-json');
  }


  
  public function viewCollectible($id=null)
  {
    if (!$id) 
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
  
  //This views a users stash details...not logged in
 /* function view($username=null)
  {
  //IF not found then do something
    $result = $this->User->findByUsername($username);   
    $stashCount = $this->User->getNumberOfStashesByUser($username);
    $stashDetails = $this->User->Stash->getStashDetails($result['User']['id']);  
    $this->set('stashCount', $stashCount );
    $this->set('stashDetails', $stashDetails );
    $this->set(compact('username'));
  }   */
  
  // function home()
  // {
  //   $username = $this->getUsername();
  //   $user = $this->getUser();
  //   $stashCount = $this->User->getNumberOfStashesByUser($username);
  //   $stashDetails = $this->User->Stash->getStashDetails($user['User']['id']);  
  //   $this->set('stashCount', $stashCount );
  //   $this->set('stashDetails', $stashDetails );
  // 
  // }
  // 
  function gallery()
  {
 //Add something in here that if they aren't logged in and they pass in an id, we return that users collection
    $username = $this->getUsername();
    if ($username)
    {
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
				if($this -> User -> Stash -> CollectiblesStash -> delete($id, false)) {
					$this -> Session -> setFlash(__('Collectible was successfully removed.', true), null, null, 'success');
					$this -> redirect( array('action' => 'index'), null, true);
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
		debug($this->data);			
		if(!$id) {
			if(!empty($this -> data)) {
				//$this->loadModel('CollectiblesUser');
				$fieldList = array('edition_size', 'cost', 'condition_id', 'merchant_id');
				if($this->User->CollectiblesUser->save($this -> data, true, $fieldList))
				{
					$this -> Session -> setFlash(__('Collectible was successfully updated.', true), null, null, 'success');
					$this -> redirect( array('action' => 'index'), null, true);	
				}
				else
				{
					$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				}
				
			
			} else {

			}

		} else {
			$username = $this -> getUsername();
			$joinRecords = $this -> User -> getCollectibleByUser($username, $id);
			$this -> loadModel('Condition');
			$this -> set('conditions', $this -> Condition -> find('list', array('order' => 'name')));
			$this -> loadModel('Merchant');
			$this -> set('merchants', $this -> Merchant -> find('list', array('order' => 'name')));
			$this -> set('collectible', $joinRecords);
			
			//$this->User->habtmUpdate('Collectible', 14, 92, array('edition_size' => 60));
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


 // if (!empty($this->data)) 
 //      {
 //        if($this->data['search'])
 //        {
 //            if(empty($this->data['search']))
 //            {
 //              $this->data['search'] = "test";
 //            }
 //            debug($this->data);
 //            $this->paginate = array(
 //              "conditions"=> array("MATCH(Collectible.name) AGAINST('{$this->data['search']}' IN BOOLEAN MODE)", 'Approval.state' => '0'),
 //              "contain"=>array ('Manufacture','Collectibletype','Upload', 'Approval'),
 //              'limit'=>1
 //            );
 //            $data = $this->paginate('Collectible');
 //            debug($data);
 //            $this->set('stashId',$this->params['named']['stashId']);
 //            $this->set('collectibles', $data);     
 //        }
 //        else
 //        {
 //          $result =  $this->User->findByUsername($username);
 //          $this->data['CollectiblesStash']['data_added'] = date("Y-m-d H:i:s", time());
 //          //$addData['CollectiblesStash']['stash_id'] = $this->params['named']['stashId'];        
 //          if($this->User->Stash->CollectiblesStash->saveAll($this->data))
 //          {
 //            $this->Session->setFlash(__('Collectible was successfully added.', true));
 //            $this->redirect(array('controller' => 'collections', 'action' => 'index'),null, true);
 //          }
 //          else
 //          {
 //              //TODO
 //          }                 
 //        }    
 //      }
 //      else
 //      { 
 //        $this->paginate = array("contain"=>array('Manufacture','Collectibletype','Upload', 'Approval'),
 //          'conditions' => array('Approval.state' => '0'),'limit'=>1
 //        
 //        );
 //        $data = $this->paginate('Collectible');
 //        debug($data);
 //        $this->set('stashId',$this->params['named']['stashId']);
 //        $this->set('collectibles', $data);     
 //      }



?>






