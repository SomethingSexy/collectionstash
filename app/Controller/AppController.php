<?php
App::uses('AuthComponent', 'Controller/Component');
App::uses('CakeEventListener', 'Event');
class AppController extends Controller
{
    
    // Since we are specifying the auto login and auth here, we need to pull in session as well
    public $components = array('Session', 'AutoLogin', 'Auth' => array('authenticate' => array('Form')));
    
    public function beforeFilter() {
        
        // Configure our auto login stuff
        $this->AutoLogin->settings = array(
        
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
        $this->Auth->allow();
        
        if ($this->request->isAjax()) {
            Configure::write('debug', 0);
            $this->layout = 'ajax';
        } else {
            $this->layout = 'default';
        }
        if (AuthComponent::user('id')) {
            $this->set('isLoggedIn', true);
            $this->set('username', $this->getUsername());
            if ($this->isUserAdmin()) {
                $this->set('isUserAdmin', true);
            } else {
                $this->set('isUserAdmin', false);
            }
        } else {
            $this->set('isLoggedIn', false);
            $this->set('isUserAdmin', false);
        }
        
        $this->set('subscriptions', $this->getSubscriptions());
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
        } else {
            return false;
        }
    }
    
    public function isUserAdmin() {
        $user = $this->getUser();
        
        if ($user['User']['admin'] == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getUserId() {
        $user = $this->getUser();
        return $user['User']['id'];
    }
    
    public function getSubscriptions() {
        $subscriptions = $this->Session->read('subscriptions');
        
        if ($subscriptions === null) {
            return array();
        } else {
            return $subscriptions;
        }
    }
    
    public function getNotificationsCount() {
        $count = $this->Session->read('notificationsCount');
        
        if ($count === null) {
            return 0;
        } else {
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
    
    /**
     * This is the insane search method to search on a collectible.
     *
     * Enhancements: Determine what filters might be set so we only do the contains on necessary ones, not all
     *
     * This should really be a component
     */
    public function searchCollectible($conditions = null) {
        $this->loadModel('Collectible');
        
        $saveSearchFilters = array();
        
        // handle this one separately for now as well
        if (isset($this->request->query['q'])) {
            $this->request->data['Search'] = array();
            $this->request->data['Search']['search'] = '';
            $this->request->data['Search']['search'] = $this->request->query['q'];
        }
        
        // handle this one separately
        if (isset($this->request->query['o'])) {
        } else {
            $this->request->query['o'] = 'a';
        }
        
        if (isset($this->request->data['Search']['search']) && trim($this->request->data['Search']['search']) !== '') {
            $search = $this->request->data['Search']['search'];
            $search = ltrim($search);
            $search = rtrim($search);
            $saveSearchFilters['search'] = $search;
        }
        
        // Here I need to check the query string for all possible filters
        $currentFilters = array();
        $currentFilters['Search'] = array();
        foreach ($this->filters as $filterkey => $filter) {
            if (isset($this->request->query[$filterkey])) {
                
                $queryValue = $this->request->query[$filterkey];
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
        
        if (isset($saveSearchFilters['t'])) {
            reset($saveSearchFilters['t']);
            
            // make sure array pointer is at first element
            $firstKey = $saveSearchFilters['t'][0];
            $this->loadModel('Tag');
            $tag = $this->Tag->find("first", array('contain' => false, 'conditions' => array('Tag.id' => $firstKey)));
            $saveSearchFilters['tag'] = $tag['Tag'];
        }
        
        //If nothing is set, use alphabetical order as the default
        $order = array();
        $order['Collectible.name'] = 'ASC';
        $status = array();
        $status['Collectible.status_id'] = '4';
        $tableFilters = array();
        foreach ($currentFilters['Search'] as $filterKey => $filterGroup) {
            
            // if the one we are looking at is a custom
            if (!isset($this->filters[$filterKey]['custom']) || !$this->filters[$filterKey]['custom']) {
                $modelFilters = array();
                array_push($modelFilters, array('AND' => array()));
                array_push($modelFilters[0]['AND'], array('OR' => array()));
                $filtersSet = false;
                
                foreach ($filterGroup as $key => $value) {
                    array_push($modelFilters[0]['AND'][0]['OR'], array($this->filters[$filterKey]['model'] . '.' . $this->filters[$filterKey]['id'] => $value));
                    $filtersSet = true;
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
                    case "n":
                        $order['Collectible.modified'] = 'desc';
                        break;

                    case "o":
                        $order['Collectible.created'] = 'ASC';
                        break;

                    case "a":
                        $order['Collectible.name'] = 'ASC';
                        break;

                    case "d":
                        $order['Collectible.name'] = 'desc';
                        break;

                    default:
                        $order['Collectible.name'] = 'ASC';
                }
            } else if ($filterKey === 'status') {
                $status['Collectible.status_id'] = $filterGroup[0];
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
        
        // set status here, this one is a litte special because we need a default
        array_push($conditions, $status);
        debug($tableFilters);
        
        //See if a search was set
        if (isset($search)) {
            
            //Is the search an empty string?
            if ($search == '') {
                $this->paginate = array("joins" => $joins, 'order' => $order, "conditions" => array($conditions, $tableFilters), "contain" => array('Scale', 'ArtistsCollectible' => array('Artist'), 'AttributesCollectible' => array('Attribute' => array('AttributeCategory', 'Scale', 'Manufacture', 'AttributesUpload' => array('Upload'))), 'SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')), 'limit' => $listSize);
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
                $this->paginate = array("joins" => $joins, 'order' => $order, "conditions" => array($conditions, $tableFilters), "contain" => array('Scale', 'ArtistsCollectible' => array('Artist'), 'AttributesCollectible' => array('Attribute' => array('AttributeCategory', 'Scale', 'Manufacture', 'AttributesUpload' => array('Upload'))), 'SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')), 'limit' => $listSize);
            }
        } else {
            
            //This a search based on filters, not a search string
            $this->paginate = array("joins" => $joins, 'order' => $order, "contain" => array('Scale', 'ArtistsCollectible' => array('Artist'), 'AttributesCollectible' => array('Attribute' => array('AttributeCategory', 'Scale', 'Manufacture', 'AttributesUpload' => array('Upload'))), 'SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')), 'conditions' => array($conditions, $tableFilters), 'limit' => $listSize);
        }
        
        $data = $this->paginate('Collectible');
        
        $this->set('collectibles', $data);
        
        // $filters = $this->_getFilters($saveSearchFilters);
        $this->set('filters', $this->getFilters());
        $saveSearchFilters = $this->_processFilters($saveSearchFilters);
        $this->set(compact('saveSearchFilters'));
        
        return $data;
    }
    
    private function _processFilters($searchFilters) {
        $retVal = array();
        
        // if we have some settings
        if (isset($searchFilters['m'])) {
            
            $this->loadModel('Manufacture');
            foreach ($searchFilters['m'] as $key => $value) {
                $manufacturer = $this->Manufacture->find("first", array('conditions' => array('Manufacture.id' => $value), 'contain' => false));
                array_push($retVal, array('id' => $value, 'label' => $manufacturer['Manufacture']['title'], 'type' => 'm'));
            }
        }
        
        if (isset($searchFilters['ct'])) {
            
            $this->loadModel('Collectibletype');
            foreach ($searchFilters['ct'] as $key => $value) {
                $collectibleType = $this->Collectibletype->find("first", array('conditions' => array('Collectibletype.id' => $value), 'contain' => false));
                array_push($retVal, array('id' => $value, 'label' => $collectibleType['Collectibletype']['name'], 'type' => 'ct'));
            }
        }
        
        if (isset($searchFilters['l'])) {
            $this->loadModel('License');
            foreach ($searchFilters['l'] as $key => $value) {
                $license = $this->License->find("first", array('conditions' => array('License.id' => $value), 'contain' => false));
                array_push($retVal, array('id' => $value, 'label' => $license['License']['name'], 'type' => 'l'));
            }
        }
        
        if (isset($searchFilters['s'])) {
            $this->loadModel('Scale');
            foreach ($searchFilters['s'] as $key => $value) {
                $scale = $this->Scale->find("first", array('conditions' => array('Scale.id' => $value), 'contain' => false));
                array_push($retVal, array('id' => $value, 'label' => $scale['Scale']['scale'], 'type' => 's'));
            }
        }
        
        if (isset($searchFilters['status'])) {
            foreach ($searchFilters['status'] as $key => $value) {
                if ($value === '2') {
                    array_push($retVal, array('id' => '2', 'label' => __('Pending'), 'type' => 'status'));
                } else if ($value === '4') {
                    array_push($retVal, array('id' => '4', 'label' => __('Active'), 'type' => 'status'));
                }
            }
        }
        
        return $retVal;
    }
    
    protected function getFilters() {
        return $this->filters;
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
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Controller . Subscription . notify', $this, array('subscriptions' => $subscriptions)));
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
}
?>