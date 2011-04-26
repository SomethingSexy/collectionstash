<?php
App::import('Sanitize');
class UsersController extends AppController {
    var $name = 'Users';

    var $helpers = array('Html', 'Form', 'FileUpload.FileUpload');

    var $components = array('Email');
    
    function login() {
        $this -> Session -> destroy();
        //$this -> layout = 'login';
        //debug(Configure::read('Settings.title'));
        $success = true;
        if($this -> data) {
            $this -> data = Sanitize::clean($this -> data, array('encode' => false));
            $this -> User -> recursive = 0;
            $results = $this -> User -> getUser($this -> data['User']['username']);
            if($results) {
                if($results['User']['status'] == 0) {
                    if($results['User']['password'] == md5($this -> data['User']['password'])) {
                        $this -> log($results);
                        $this -> Session -> write('user', $results);
                        $this -> redirect( array('action' => 'home'), null, true);
                    } else {
                        $this -> Session -> setFlash(__('Invalid Login.', true), null, null, 'error');
                        $success = false;
                    }
                } else {
                    $this -> Session -> setFlash(__('Your account has not been activated yet.', true), null, null, 'error');
                    $success = false;
                }
            } else {
                $this -> Session -> setFlash(__('Invalid Login.', true), null, null, 'error');
                $success = false;
            }
        }

        if(!$success) {
            $this -> data['User']['password'] = '';
            $this -> data['User']['new_password'] = '';
            $this -> data['User']['confirm_password'] = '';
        }

    }

	function logout() {
		$this -> Session -> delete('user');
		$this -> Session -> destroy();

		$this -> redirect( array('action' => 'login'), null, true);
	}

    function index() {
        if($this -> isLoggedIn()) {
            $this -> set('knownusers', $this -> User -> find('all', array('id', 'username', 'first_name', 'last_name'), 'id DESC'));
        } else {
            $this -> redirect( array('action' => 'login'), null, true);
        }

    }

	function home($username =null) {
		$user;
		$myCollection = false;
		if($username) {
			$user = $this -> User -> getUser($username);
			//Check first to see if logged in and then if the user enter is your username

		} else {
			$myCollection = true;
			$username = $this -> getUsername();
			$user = $this -> getUser();
			if(!$user) {
				//TODO error
			}

		}

		if($user) {
			//$results = $this->User->findByUsername($username);
			//do I need to store here, can I pull from session in view?
			//$this->set('user', $user);

			//Grab all of the collectibles for this user.  Not being used currently.
			/* $this->paginate = array(
 			'conditions'=>array('CollectiblesUser.user_id'=>$user['User']['id']),
 			// 'limit' => 1,
 			'contain'=>array(
 			'Collectible' => array ( 'Manufacture','Collectibletype','Upload'))
 			);
 			$data = $this->paginate('CollectiblesUser');
 			debug($data);     */
			//Grab the number of collectibles for this user
			$stashCount = $this -> User -> getNumberOfStashesByUser($username);
			$stashDetails = $this -> User -> Stash -> getStashDetails($user['User']['id']);
			$this -> set('stashCount', $stashCount);
			$this -> set('stashDetails', $stashDetails);
			//Grab the number of pending submissions.
			if($myCollection) {
				$this -> loadModel('Collectible');
				$submissionCount = $this -> Collectible -> getPendingCollectiblesByUserId($user['User']['id']);
				$this -> set('submissionCount', $submissionCount);
			}
			$this -> set('myCollection', $myCollection);
			//$this->set('collectibles',$data);
		} else {
			$this -> redirect( array('action' => 'login'), null, true);
		}
	}

	function register() {
		//Make sure the user name is not a list of specific ones...like any controller names :)
		if(!empty($this -> data)) {
			$this -> data['User']['password'] = md5($this -> data['User']['new_password']);
			$this -> data['User']['admin'] = 0;
			$this -> data['User']['status'] = 1;
			debug($this -> data);
			if($this -> User -> save($this -> data)) {
				$newUserId = $this -> User -> id;
				$stashData = array();
				$stashData['Stash']['name'] = 'Default';
				$stashData['Stash']['user_id'] = $newUserId;
				//Since this is a new user, the total_count for the stash for validation will start at 0
				$stashData['Stash']['total_count'] = 0;
				$this -> User -> Stash -> create();

				if($this -> User -> Stash -> save($stashData)) {

					$this -> User -> id = $newUserId;
					//since this is a new user we should be able to safetly just make it 1
					$this -> User -> saveField('stash_user_count', '1');
					$test = $this -> __sendActivationEmail($this -> User -> id);
					debug($test);
					$this -> Session -> setFlash('Your registration information was accepted');
					//$this->Session->write('user', $this->data);
					//this of code sets up the newly registered user under the aco user
					//$parent = $this -> Acl -> Aro -> findByAlias('Users');
					//$aro = new Aro();
					//$aro -> create();
					//$aro -> save( array('alias' => $this -> data['User']['username'], 'model' => 'User', 'foreign_key' => $this -> User -> id, 'parent_id' => 2));
					//$this -> Acl -> Aro -> save();
					//$this->redirect(array('action' => 'index'), null, true);
				}
			} else {
				$this -> data['User']['password'] = '';
				$this -> data['User']['new_password'] = '';
				$this -> data['User']['confirm_password'] = '';
				$this -> Session -> setFlash('There was a problem saving this information.');
			}
		}
	}

    /**
     * Activates a user account from an incoming link
     *
     *  @param Int $user_id User.id to activate
     *  @param String $in_hash Incoming Activation Hash from the email
     */
    function activate($user_id =null, $in_hash =null) {
        $this -> User -> id = $user_id;
        if($this -> User -> exists()) {
            if($this -> User -> field('status') != 0) {
                if($in_hash == $this -> User -> getActivationHash()) {
                    // Update the active flag in the database
                    $this -> User -> saveField('status', 0);

                    // Let the user know they can now log in!
                    $this -> Session -> setFlash('Your account has been activated, please log in below');
                    $this -> redirect('login');
                }
            } else {
                $this -> Session -> setFlash('Your account has already been activated!');
                $this -> redirect('login');
            }
        }
        // Activation failed, render ‘/views/user/activate.ctp’ which should tell the user.
    }

/**
     * Send out an activation email to the user.id specified by $user_id
     *  @param Int $user_id User to send activation email to
     *  @return Boolean indicates success
    */
    function __sendActivationEmail($user_id) 
    {
      $user = $this->User->find(array('User.id' => $user_id), array('User.id', 'User.email', 'User.username'), null, false);
      debug($user);
      if($user === false) {
			debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
			return false;
		}
  
      // Set data for the "view" of the Email
      $this->set('activate_url', 'http://' . env('SERVER_NAME') . '/users/activate/' . $user['User']['id'] . '/' . $this->User->getActivationHash());
      $this->set('username', $this->data['User']['username']);
      
      $this->Email->smtpOptions = array(
        'port'=>'25',
        'timeout'=>'30',
        'host' => 'smtpout.secureserver.net',
        'username'=>'admin@collectionstash.com',
        'password'=>'oblivion1968'
       // 'client' => 'smtp_helo_hostname'
      );   
      $this->Email->delivery = 'smtp';  
      $this->Email->to = $user['User']['email'];
      $this->Email->subject = env('SERVER_NAME') . '– Please confirm your email address';
      $this->Email->from = 'admin@collectionstash.com';
      $this->Email->template = 'user_confirm';
      $this->Email->sendAs = 'text';   // you probably want to use both :)   
      $return = $this->Email->send();
      $this->set('smtp_errors', $this->Email->smtpError);
      return $return;
    }
}
?>
