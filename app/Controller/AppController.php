<?php
App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeEventListener', 'Event');
class AppController extends Controller
{
    
    // Since we are specifying the auto login and auth here, we need to pull in session as well
    public $components = array('Search', 'Session', 'AutoLogin', 'Auth' => array('authenticate' => array('Form')));
    
    public function beforeFilter() {
        
        // Configure our auto login stuff
        $this->AutoLogin->settings = array(
        
        // Model settings
        'model' => 'User', 'username' => 'username', 'password' => 'password',
        
        // Controller settings
        'plugin' => '', 'controller' => 'Users', 'loginAction' => 'login', 'logoutAction' => 'logout',
        
        // Cookie settings
        'cookieName' => 'rememberMe', 'expires' => ' + 1month',
        
        // Process logic
        'active' => true, 'redirect' => true, 'requirePrompt' => true);
        
        // Since I am not using auth to it'sfullestrightnow
        // we need to allow all, the individual methods will
        // figure out if they need a user to be logged in
        $this->Auth->allow();
        
        if ($this->request->isAjax()) {
            Configure::write('debug', 0);
            $this->layout = 'ajax';
        } 
        else {
            $this->layout = 'default';
        }
        if (AuthComponent::user('id')) {
            $this->set('isLoggedIn', true);
            $this->set('username', $this->getUsername());
            if ($this->isUserAdmin()) {
                $this->set('isUserAdmin', true);
            } 
            else {
                $this->set('isUserAdmin', false);
            }
        } 
        else {
            $this->set('isLoggedIn', false);
            $this->set('isUserAdmin', false);
        }
        
        // $this->set('subscriptions', $this->getSubscriptions());
        $this->set('notificationsCount', $this->getNotificationsCount());
        
        //Since this gets set for every request, setting this here for the default
        $this->set('title_for_layout', 'Collection Stash - A collector and artist platform for building and sharing your collection.');
        $this->set('description_for_layout', 'A collectible reference database and online collection cataloging platform.');
        $this->set('keywords_for_layout', 'statue collection, action figure collection, toy collection, collectible database, action figure, toy, stash, storage');
        
        //This stores off any request parameters per request, can be used to recreate urls later
        $requestParams = '?';
        if (isset($this->request->query)) {
            foreach ($this->request->query as $key => $value) {
                if ($key !== 'ext' && $key !== 'url') {
                    $requestParams = $requestParams . $key . '=' . $value;
                }
            }
        }
        $this->set(compact('requestParams'));
    }
    
    public function getUser() {
        $authUser = AuthComponent::user();
        $user['User'] = $authUser;
        return $user;
    }
    
    public function getUsername() {
        $user = $this->getUser();
        return $user['User']['username'];
    }
    
    public function isLoggedIn() {
        $user = $this->getUser();
        if (isset($user['User']) && !empty($user['User'])) {
            return true;
        } 
        else {
            return false;
        }
    }
    
    public function isUserAdmin() {
        $user = $this->getUser();
        
        if ($user['User']['admin'] == 1) {
            return true;
        } 
        else {
            return false;
        }
    }
    
    public function getUserId() {
        $user = $this->getUser();
        return $user['User']['id'];
    }
    
    public function getNotificationsCount() {
        $count = $this->Session->read('notificationsCount');
        
        if ($count === null) {
            return 0;
        } 
        else {
            return $count;
        }
    }
    
    public function handleNotLoggedIn() {
        $this->Session->setFlash('Your session has timed out.');
        $this->redirect(array('admin' => false, 'controller' => 'users', 'action' => 'login'), null, true);
    }
    
    /**
     * This method will check if the user is logged in, if they are not it will
     * auto redirect them.
     */
    public function checkLogIn() {
        if (!$this->isLoggedIn()) {
            $this->handleNotLoggedIn();
        }
    }
    
    public function checkAdmin() {
        if (!$this->isUserAdmin()) {
            $this->handleNotLoggedIn();
        }
    }
    
    function my_array_unique($array, $keep_key_assoc = false) {
        $duplicate_keys = array();
        $tmp = array();
        
        foreach ($array as $key => $val) {
            
            // convert objects to arrays, in_array() does not support objects
            if (is_object($val)) $val = (array)$val;
            
            if (!in_array($val, $tmp)) $tmp[] = $val;
            else $duplicate_keys[] = $key;
        }
        
        foreach ($duplicate_keys as $key) unset($array[$key]);
        
        return $keep_key_assoc ? $array : array_values($array);
    }
    
    public function notifyUser($userEmail = null, $message, $subject = null) {
        $subscriptions = array();
        $subscription = array();
        $subscription['Subscription']['user_id'] = $userEmail;
        $subscription['Subscription']['message'] = $message;
        $subscription['Subscription']['subject'] = $subject;
        array_push($subscriptions, $subscription);
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Controller.Subscription.notify', $this, array('subscriptions' => $subscriptions)));
    }
    
    public function convertErrorsJSON($errors = null, $model = null) {
        $retVal = array();
        
        if (!is_null($errors)) {
            foreach ($errors as $key => $value) {
                $error = array();
                if (!is_null($model)) {
                    $error['model'] = $model;
                }
                $error['name'] = $key;
                $error['message'] = $value;
                $error['inline'] = true;
                array_push($retVal, $error);
            }
        }
        
        return $retVal;
    }
    
    protected function getClientIP() {
        
        // this is for production
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        } 
        else {
            return $this->request->clientIp();
        }
    }
}
?>