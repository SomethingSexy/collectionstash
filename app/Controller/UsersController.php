<?php
App::uses('Validation', 'Utility');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
class UsersController extends AppController {
    public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify');
    public $components = array('StashSearch');
    
    public function beforeFilter() {
        parent::beforeFilter();
    }
    // Soooo much data :)
    public function profile($username = null, $view = 'stash') {
        $this->layout = 'require';
        $loggedInUser = $this->getUser();
        // grab the user settings and the profile settings for the given user
        
        $user = $this->User->find("first", array('conditions' => array('User.username' => $username), 'contain' => array('Profile')));
        if (empty($user)) {
            $this->render('viewNoExist');
            return;
        }
        // grab the stash for this user, since comments are tied to a stash
        $stash = $this->User->Stash->find('first', array('conditions' => array('Stash.user_id' => $user['User']['id']), 'contain' => array('StashFact')));
        
        if ($stash['Stash']['privacy'] === '0' || ($loggedInUser['User']['id'] === $user['User']['id']) || ($stash['Stash']['privacy'] === '1' && $this->isLoggedIn())) {
        } 
        else {
            $this->set(compact('username'));
            $this->render('viewPrivate');
            return;
        }
        
        $profile = array();
        $profile['username'] = $user['User']['username'];
        $profile['id'] = $user['User']['id'];
        $profile['member_since'] = date("F j, Y", strtotime($user['User']['created']));
        $profile['first_name'] = $user['User']['first_name'];
        $profile['last_name'] = $user['User']['last_name'];
        $profile['display_name'] = $user['Profile']['display_name'];
        $profile['location'] = $user['Profile']['location'];
        $profile['entity_type_id'] = $stash['Stash']['entity_type_id'];
        // grab stash information..note collectibles_user_count is all collectibles in the stash including history
        $stashFacts = $this->User->Stash->find('first', array('conditions' => array('Stash.user_id' => $user['User']['id']), 'contain' => array('StashFact')));
        $currentOwnedCount = $this->User->CollectiblesUser->find('count', array('conditions' => array('CollectiblesUser.user_id' => $user['User']['id'], 'active' => true), 'contain' => false));
        $currentSaleCount = $this->User->CollectiblesUser->find('count', array('conditions' => array('CollectiblesUser.user_id' => $user['User']['id'], 'active' => true, 'sale' => true), 'contain' => false));
        $pointsYear = $this->User->UserPointFact->getUserTotalPointsCurrentYear($user['User']['id']);
        $pointsMonth = $this->User->UserPointFact->getUserTotalPointsCurrentMonth($user['User']['id']);
        $previousPointsMonth = $this->User->UserPointFact->getUserTotalPointsPreivousMonth($user['User']['id']);
        $facts = array();
        $facts['owned'] = $currentOwnedCount;
        $facts['sale'] = $currentSaleCount;
        $facts['collectibles_user_count'] = $stashFacts['Stash']['collectibles_user_count'];
        $facts['collectibles_wish_list_count'] = $user['User']['collectibles_wish_list_count'];
        $facts['user_upload_count'] = $user['User']['user_upload_count'];
        $facts['comment_count'] = $user['User']['comment_count'];
        $facts['collectible_count'] = $user['User']['collectible_count'];
        $facts['edit_count'] = $user['User']['edit_count'];
        $facts['points'] = $user['User']['points'];
        $facts['points_month'] = $pointsMonth;
        $facts['points_year'] = $pointsYear;
        $facts['points_previous_month'] = $previousPointsMonth;
        
        $this->set(compact('facts'));
        
        $this->set('title_for_layout', $user['User']['username'] . '\'s Stash - Collectible Stash');
        $this->set('description_for_layout', 'Stash profile for user ' . $user['User']['username']);
        // permissions
        $permissions = array();
        if ($loggedInUser['User']['id'] === $user['User']['id']) {
            $permissions['edit_collectible_user'] = true;
            $permissions['show_stash_facts'] = true;
            if (Configure::read('Settings.User.uploads.allowed')) {
                $permissions['upload_photos'] = true;
                $permissions['edit_photos'] = true;
            }
            // set it here so that it isn't rendered on the page either
            $stash['StashFact']['collectibles_user_count'] = $stashFacts['Stash']['collectibles_user_count'];
            $this->set('stashFacts', $stash['StashFact']);
            $permissions['edit_work'] = true;
            $permissions['showFavorite'] = false;
        } 
        else {
            $permissions['edit_collectible_user'] = false;
            $permissions['show_stash_facts'] = false;
            $permissions['edit_work'] = false;
            // stupid spot but fixing last minute bug, only show if logged in
            if ($this->isLoggedIn()) {
                $permissions['showFavorite'] = true;
            }
        }
        
        if ($this->isLoggedIn()) {
            $permissions['add_comment'] = true;
            $profile['favorited'] = $this->User->Favorite->isFavorited($user['User']['id'], $loggedInUser, 'user');
        } 
        else {
            $permissions['add_comment'] = false;
            $profile['favorited'] = false;
        }
        
        $reasons = $this->Stash->CollectiblesUser->CollectibleUserRemoveReason->find('all', array('contain' => false));
        $this->set(compact('reasons'));
        
        $this->set(compact('permissions'));
        $this->set(compact('profile'));
        
        $this->set('filters', $this->StashSearch->getFilters($user['User']['id']));
        // retrieve all comments
        $comments = $this->User->Comment->getComments($stash['Stash']['entity_type_id'], $loggedInUser['User']['id']);
        
        $extractComments = Set::extract('/Comment/.', $comments['comments']);
        
        foreach ($extractComments as $key => $value) {
            $extractComments[$key]['User'] = $comments['comments'][$key]['User'];
            $extractComments[$key]['permissions'] = $comments['comments'][$key]['permissions'];
        }
        
        $this->set('comments', $extractComments);
        // grab the latest activity for this user
        $activity = $this->User->Activity->find('all', array('limit' => 10, 'conditions' => array('Activity.user_id' => $user['User']['id']), 'order' => array('Activity.created' => 'desc')));
        $this->set('activity', Set::extract('/Activity/.', $activity));
        $works = $this->User->Collectible->find('all', array('conditions' => array('Collectible.user_id' => $user['User']['id'], 'OR' => array('Collectible.status_id' => 1, 'Collectible.custom_status_id' => array('1', '2', '3'))), 'contain' => array('Collectibletype', 'Manufacture', 'Status', 'User' => array('fields' => array('id', 'username')))));
        
        $extractWorks = Set::extract('/Collectible/.', $works);
        
        foreach ($extractWorks as $key => $value) {
            $extractWorks[$key]['User'] = $works[$key]['User'];
            $extractWorks[$key]['Collectibletype'] = $works[$key]['Collectibletype'];
            $extractWorks[$key]['Manufacture'] = $works[$key]['Manufacture'];
            $extractWorks[$key]['Status'] = $works[$key]['Status'];
        }
        
        $this->set('works', $extractWorks);
    }
    /**
     * This was the old route that went to the user dasboard, if this route somehow still exists, send
     * them to their page, if they are logged in
     */
    public function home() {
        if ($this->isLoggedIn()) {
            $this->redirect('/profile/' . $this->getUsername());
        } 
        else {
            $this->redirect('/users/login');
        }
    }
    
    function notifications() {
        $this->checkLogIn();
        
        $notifications = $this->User->Notification->getNotifications($this->getUser(), array('limit' => 25, 'order' => array('Notification.created' => 'desc')));
        $totalNotifications = $this->User->Notification->getCountNotifications($this->getUser());
        $this->set(compact('notifications'));
        $this->set(compact('totalNotifications'));
        $this->layout = 'default';
        $this->set('dashboard', 'notifications');
    }
    /**
     * This is the main index into this controller, it will display a list of users.
     */
    function index() {
        $this->paginate = array('conditions' => array('User.admin !=' => 1), 'contain' => false, 'order' => array('User.username' => 'ASC'), 'limit' => 50);
        $users = $this->paginate('User');
        $this->set(compact('users'));
    }
    
    function login() {
        $message = null;
        $messageType = null;
        
        if ($this->Session->check('Message.error')) {
            $message = $this->Session->read('Message.error');
            $message = $message['message'];
            $messageType = 'error';
        } 
        else if ($this->Session->check('Message.success')) {
            $message = $this->Session->read('Message.success');
            $message = $message['message'];
            $messageType = 'success';
        }
        
        $this->Session->destroy();
        $this->Session->setFlash($message, null, null, $messageType);
        $success = true;
        if ($this->request->is('post')) {
            $this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
            $this->User->recursive = 0;
            $results = null;
            if (isset($this->request->data['User']['username']) && Validation::email($this->request->data['User']['username'])) {
                $this->request->data['User']['email'] = $this->request->data['User']['username'];
                $this->Auth->authenticate['Form'] = array('fields' => array('username' => 'email'));
                $results = $this->User->getUserByEmail($this->request->data);
            } 
            else {
                $results = $this->User->getUser($this->request->data['User']['username']);
            }
            
            if ($results) {
                if ($results['User']['status'] == 0) {
                    if (!$results['User']['force_password_reset']) {
                        //This seems redundant might make more since to auto login them in because I already have the data
                        if ($this->Auth->login()) {
                            $user = $this->Auth->user();
                            $this->User->id = $user['id'];
                            $this->User->saveField('last_login', date("Y-m-d H:i:s", time()));
                            
                            $autoLogin = isset($this->request->data['User']['auto_login']) ? $this->request->data['User']['auto_login'] : false;
                            if ($autoLogin) {
                                $this->AutoLogin->write($user['username'], $this->request->data['User']['password']);
                            } 
                            else {
                                $this->AutoLogin->delete();
                            }
                            
                            CakeLog::write('info', $results);
                            $this->Session->write('user', $user);
                            
                            CakeLog::write('info', 'User ' . $user['id'] . ' successfully logged in at ' . date("Y-m-d H:i:s", time()));
                            // grab the total number of unread notifications
                            $totalNotifications = $this->User->Notification->getCountUnreadNotifications($user['id']);
                            $this->Session->write('notificationsCount', $totalNotifications);
                            
                            $this->redirect('/profile/' . $user['username']);
                        } 
                        else {
                            $this->Session->setFlash(__('Username or password is incorrect', true), null, null, 'error');
                            $this->request->data['User']['password'] = '';
                            $this->request->data['User']['new_password'] = '';
                            $this->request->data['User']['confirm_password'] = '';
                            CakeLog::write('error', 'User ' . $this->request->data['User']['username'] . ' failed logging in at ' . date("Y-m-d H:i:s", time()));
                        }
                    } 
                    else {
                        // $this -> Auth -> logout();
                        return $this->redirect(array('controller' => 'forgotten_requests', 'action' => 'forceResetPassword'));
                    }
                } 
                else {
                    // $this -> Auth -> logout();
                    $this->Session->setFlash(__('Your account has not been activated yet.', true), null, null, 'error');
                    $this->request->data['User']['password'] = '';
                    $this->request->data['User']['new_password'] = '';
                    $this->request->data['User']['confirm_password'] = '';
                }
            } 
            else {
                $this->Session->setFlash(__('Invalid Login.', true), null, null, 'error');
                $this->request->data['User']['password'] = '';
                $this->request->data['User']['new_password'] = '';
                $this->request->data['User']['confirm_password'] = '';
            }
        }
    }
    
    function _autoLogin() {
        CakeLog::write('info', '_autoLogin' . date("Y-m-d H:i:s", time()));
        $user = $this->getUser();
        
        $totalNotifications = $this->User->Notification->getCountUnreadNotifications($user['User']['id']);
        $this->Session->write('notificationsCount', $totalNotifications);
    }
    
    function logout() {
        $this->Session->delete('user');
        $this->Session->destroy();
        $this->AutoLogin->delete();
        
        $this->redirect('/', null, true);
    }
    /**
     * Need to update this so that if the config is invites-only, then we have to check the email address to make
     * sure that it is one that is in the list.
     *
     * Also for helper, take the passed in email and put it in the $this->data
     */
    function register($email = null) {
        //Make sure the user name is not a list of specific ones...like any controller names :)
        if (Configure::read('Settings.registration.open')) {
            if (!empty($this->request->data)) {
                $this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
                $proceed = true;
                $invitedUser = null;
                //If invite only is turned on, first make sure that this user is invited
                if (Configure::read('Settings.registration.invite-only')) {
                    $invitedUser = $this->User->Invite->find("first", array('conditions' => array('Invite.email' => $this->request->data['User']['email'])));
                    if (empty($invitedUser)) {
                        $proceed = false;
                        $this->Session->setFlash(__('Sorry for the inconvenience, Collection Stash is currently invite only.', true), null, null, 'error');
                    }
                }
                if ($proceed) {
                    $this->request->data['User']['password'] = AuthComponent::password($this->data['User']['new_password']);
                    if ($this->User->createUser($this->request->data)) {
                        $newUserId = $this->User->id;
                        if (Configure::read('Settings.registration.invite-only')) {
                            $this->User->Invite->id = $invitedUser['Invite']['id'];
                            $this->User->Invite->saveField('registered', '1', false);
                        }
                        $emailResult = $this->__sendActivationEmail($this->User->id);
                        if ($emailResult) {
                            $this->Session->setFlash('Your registration information was accepted');
                            $this->render('registrationComplete');
                        } 
                        else {
                            //At this point sending the email failed, so we should roll it all back
                            $this->User->delete($newUserId);
                            $this->request->data['User']['password'] = '';
                            $this->request->data['User']['new_password'] = '';
                            $this->request->data['User']['confirm_password'] = '';
                            $this->Session->setFlash(__('There was a problem registering this information.', true), null, null, 'error');
                        }
                    } 
                    else {
                        $this->request->data['User']['password'] = '';
                        $this->request->data['User']['new_password'] = '';
                        $this->request->data['User']['confirm_password'] = '';
                        $this->Session->setFlash(__('There was a problem registering this information.', true), null, null, 'error');
                    }
                }
            } 
            else {
                if ($email) {
                    $this->request->data['User']['email'] = $email;
                }
            }
        } 
        else {
            $this->redirect(array('action' => 'login'), null, true);
        }
    }
    /**
     * Activates a user account from an incoming link
     *
     *  @param Int $user_id User.id to activate
     *  @param String $in_hash Incoming Activation Hash from the email
     */
    function activate($user_id = null, $in_hash = null) {
        $this->User->id = $user_id;
        if ($this->User->exists()) {
            if ($this->User->field('status') != 0) {
                if ($in_hash == $this->User->getActivationHash()) {
                    // Update the active flag in the database
                    $this->User->saveField('status', 0);
                    // Let the user know they can now log in!
                    $this->Session->setFlash(__('Your account has been activated, please log in below', true), null, null, 'success');
                    $this->redirect('login');
                } 
                else {
                    $this->set('userId', $user_id);
                    $this->render('activationExpired');
                }
            } 
            else {
                $this->Session->setFlash(__('Your account has already been activated!', true), null, null, 'error');
                $this->redirect('login');
            }
        } 
        else {
            $this->Session->setFlash(__('That user does not exist, please register.', true), null, null, 'error');
            $this->redirect('login');
        }
        // Activation failed, render ‘/views/user/activate.ctp’ which should tell the user.
        
        
    }
    
    function resendActivation($user_id = null) {
        if ($user_id) {
            $this->User->id = $user_id;
            if ($this->User->exists()) {
                if ($this->User->field('status') != 0) {
                    $emailResult = $this->__sendActivationEmail($this->User->id);
                    if ($emailResult) {
                        //do nothing
                        
                        
                    } 
                    else {
                        //Do what?
                        
                        
                    }
                } 
                else {
                    $this->Session->setFlash(__('Your account has already been activated!', true), null, null, 'error');
                    $this->redirect('login');
                }
            } 
            else {
                $this->redirect('login');
            }
        } 
        else {
            $this->redirect('login');
        }
    }
    /**
     * This method is called when a user recieved a link to reset their password
     */
    function resetPassword($type = 'forgot', $id = null) {
        if (!is_null($id)) {
            $this->loadModel('ForgottenRequest');
            $forgottenRequest = $this->ForgottenRequest->find("first", array('conditions' => array('ForgottenRequest.id' => $id)));
            if (!empty($forgottenRequest)) {
                $createdDate = $forgottenRequest['ForgottenRequest']['created'];
                /**
                 * Check to make sure that the created date is less than 24 hours, this is to
                 * make sure we do not have stale requests out there.
                 */
                if (time() <= strtotime($createdDate) + 86400) {
                    /*
                     * Checking here now to see if something was submitted, I think to stay as secure
                     * as possible we will need to go through this process everytime
                    */
                    if (!empty($this->request->data)) {
                        $this->User->set($this->request->data);
                        /*
                         * Validate JUST the new_password and the confirm_password
                        */
                        if ($this->User->validates(array('fieldList' => array('new_password', 'confirm_password')))) {
                            $this->request->data['User']['id'] = $forgottenRequest['ForgottenRequest']['user_id'];
                            if ($this->User->changePassword($this->request->data)) {
                                $userId = $this->User->id;
                                if ($type === 'reset') {
                                    $this->User->id = $userId;
                                    $this->User->saveField('force_password_reset', '0');
                                }
                                
                                $this->ForgottenRequest->delete($forgottenRequest['ForgottenRequest']['id']);
                                $this->Session->setFlash(__('Your password has been successfully changed, please log in below', true), null, null, 'success');
                                $this->redirect('login');
                            }
                        } 
                        else {
                            $this->request->data['User']['password'] = '';
                            $this->request->data['User']['new_password'] = '';
                            $this->request->data['User']['confirm_password'] = '';
                            $this->Session->setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
                        }
                    }
                } 
                else {
                    //If it is expired, lets delete cause it is not needed out there
                    $this->ForgottenRequest->delete($forgottenRequest['ForgottenRequest']['id']);
                    $this->Session->setFlash(__('The key to reset your password as expired, please resubmit the request.', true), null, null, 'error');
                    $this->redirect(array('controller' => 'forgotten_requests', 'action' => 'forgotPassword'));
                }
            } 
            else {
                $this->Session->setFlash(__('Your request to reset your password was not found, if you need to reset your password select the link below.', true), null, null, 'error');
                $this->redirect('login');
            }
        } 
        else {
            $this->Session->setFlash(__('Invalid request to reset your password.', true), null, null, 'error');
            $this->redirect('login');
        }
    }
    /**
     * Send out an activation email to the user.id specified by $user_id
     *  @param Int $user_id User to send activation email to
     *  @return Boolean indicates success
     */
    function __sendActivationEmail($user_id) {
        $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id), 'contain' => false));
        debug($user);
        if ($user === false) {
            debug(__METHOD__ . " failed to retrieve User data for user.id: {$user_id}");
            return false;
        }
        
        $email = new CakeEmail('smtp');
        $email->emailFormat('text');
        $email->template('user_confirm', 'simple');
        $email->to(trim($user['User']['email']));
        $email->subject('Welcome to Collection Stash! Please confirm your email address');
        $email->viewVars(array('activate_url' => 'http://' . env('SERVER_NAME') . '/users/activate/' . $user['User']['id'] . '/' . $this->User->getActivationHash(), 'username' => $this->request->data['User']['username']));
        $email->send();
        
        return true;
    }
}
?>
