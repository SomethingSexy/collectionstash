<?php
App::uses('TransactionFactory', 'Lib/Transaction');
class Listing extends AppModel
{
    public $name = 'Listing';
    public $belongsTo = array('Collectible', 'User');
    public $hasOne = array('CollectiblesUser');
    public $hasMany = array('Transaction' => array('dependent' => true));
    public $actsAs = array('Containable');
    //TODO: need to check duplicates
    public $validate = array(
    //
    'ext_item_id' => array('maxLength' => array('rule' => array('maxLength', 200), 'required' => true, 'allowEmpty' => false, 'message' => 'Item is required and cannot be more than 200 characters.'), 'dups' => array('rule' => array('checkDuplicateItems'), 'message' => 'A listing with that item has already been added.')),
    //
    'listing_type_id' => array('rule' => 'numeric', 'allowEmpty' => false, 'required' => true, 'message' => 'Must be a valid listing type.'),
    // this is only needed when deleting or selling
    'sold_cost' => array('rule' => array('money', 'left'), 'allowEmpty' => true, 'message' => 'Please supply a valid monetary amount.'),
    // this is only used for updates
    'listing_price' => array('rule' => array('money', 'left'), 'allowEmpty' => true, 'message' => 'Please supply a valid monetary amount.'),
    //traded for, only needed when deleting or selling
    'traded_for' => array('maxLength' => array('rule' => array('maxLength', 1000), 'allowEmpty' => true, 'message' => 'Traded for is required and must be less than 1000 characters.')),);
    
    private $collectibleCacheKey = 'listing_collectible_';
    
    function afterSave($created, $options = array()) {
        // so far we only doing singles, I don't think we do multiple
        if (isset($this->data['Listing']['collectible_id'])) {
            $this->clearCache($this->data['Listing']['collectible_id']);
        }
    }
    
    function afterFind($results, $primary = false) {
        
        if ($results) {
            // If it is primary handle all of these things
            if ($primary) {
                foreach ($results as $key => $val) {
                    if (isset($val['Listing'])) {
                        if (isset($val['Listing']['start_date'])) {
                            $datetime = strtotime($val['Listing']['start_date']);
                            $datetime = date("m/d/y g:i A", $datetime);
                            $results[$key]['Listing']['start_date'] = $datetime;
                        }
                        if (isset($val['Listing']['end_date'])) {
                            $datetime = strtotime($val['Listing']['end_date']);
                            $datetime = date("m/d/y g:i A", $datetime);
                            $results[$key]['Listing']['end_date'] = $datetime;
                        }
                        // this should go in the database somewhere but for now add here
                        // a mapping of collectible_user_remove_reason_id
                        if (isset($val['Listing']['listing_type_id'])) {
                            if ($val['Listing']['listing_type_id'] === '1' || $val['Listing']['listing_type_id'] === '2') {
                                $results[$key]['Listing']['collectible_user_remove_reason_id'] = 1;
                            } else if ($val['Listing']['listing_type_id'] === '2') {
                                $results[$key]['Listing']['collectible_user_remove_reason_id'] = 3;
                            }
                        }
                    }
                }
            } else {
                if (isset($results[$this->primaryKey])) {
                    
                    if (isset($results['start_date'])) {
                        $datetime = strtotime($results['start_date']);
                        $datetime = date("m/d/y g:i A", $datetime);
                        $results['start_date'] = $datetime;
                    }
                    if (isset($results['end_date'])) {
                        $datetime = strtotime($results['end_date']);
                        $datetime = date("m/d/y g:i A", $datetime);
                        $results['end_date'] = $datetime;
                    }
                    // this should go in the database somewhere but for now add here
                    // a mapping of collectible_user_remove_reason_id
                    if ($results['listing_type_id'] === '1' || $results['listing_type_id'] === '2') {
                        $results['collectible_user_remove_reason_id'] = 1;
                    } else if ($results['listing_type_id'] === '2') {
                        $results['collectible_user_remove_reason_id'] = 3;
                    }
                } else {
                    
                    foreach ($results as $key => $val) {
                        
                        if (isset($val['Listing'])) {
                            if (isset($val['Listing']['start_date'])) {
                                $datetime = strtotime($val['Listing']['start_date']);
                                $datetime = date("m/d/y g:i A", $datetime);
                                $results[$key]['Listing']['start_date'] = $datetime;
                            }
                            if (isset($val['Listing']['end_date'])) {
                                $datetime = strtotime($val['Listing']['end_date']);
                                $datetime = date("m/d/y g:i A", $datetime);
                                $results[$key]['Listing']['end_date'] = $datetime;
                            }
                            // this should go in the database somewhere but for now add here
                            // a mapping of collectible_user_remove_reason_id
                            if (isset($val['Listing']['listing_type_id'])) {
                                if ($val['Listing']['listing_type_id'] === '1' || $val['Listing']['listing_type_id'] === '2') {
                                    $results[$key]['Listing']['collectible_user_remove_reason_id'] = 1;
                                } else if ($val['Listing']['listing_type_id'] === '3') {
                                    $results[$key]['Listing']['collectible_user_remove_reason_id'] = 2;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $results;
    }
    
    function checkDuplicateItems($check) {
        // we need these to proceed
        if (empty($check['ext_item_id']) || empty($this->data['Listing']['listing_type_id']) || empty($this->data['Listing']['collectible_id'])) {
            return false;
        }
        
        $count = $this->find('count', array('contain' => false, 'conditions' => array('Listing.collectible_id' => $this->data['Listing']['collectible_id'], 'Listing.ext_item_id' => $check['ext_item_id'], 'Listing.listing_type_id' => $this->data['Listing']['listing_type_id'])));
        // return true if none found
        return $count === 0;
    }
    
    public function findByCollectibleId($id) {
        
        $listings = Cache::read($this->collectibleCacheKey . $id, 'collectible');
        // if it isn't in the cache, add it to the cache
        if (!$listings) {
            $listings = $this->find('all', array('conditions' => array('Listing.collectible_id' => $id), 'contain' => array('User' => array('fields' => array('User.username', 'User.admin')), 'Transaction')));
            Cache::write($this->collectibleCacheKey . $id, $listings, 'collectible');
        }
        
        return $listings;
    }
    /**
     *
     */
    public function remove($id, $user) {
        $retVal = $this->buildDefaultResponse();
        // we need the collectible_id so we know which cache to clear
        $listing = $this->find('first', array('conditions' => array('Listing.id' => $id), 'contain' => false));
        
        if ($this->delete($id, true)) {
            $this->clearCache($listing['Listing']['collectible_id']);
            $retVal['response']['isSuccess'] = true;
        } else {
            $retVal['response']['isSuccess'] = false;
            array_push($retVal['response']['errors'], array('message' => __('Invalid request.')));
        }
        
        return $retVal;
    }
    /**
     * listing_Type_id = 1 eBay
     * listing_type_id = 2 personal sell
     * listing_type_id = 3 personal trade
     *
     * // this is all of the possible fields this method will accept
     * ext_item_id
     * sold_cost
     * traded_for
     * end_date
     * active_sale
     * collectible_id
     *
     */
    public function createListing($data, $user) {
        $retVal = $this->buildDefaultResponse();
        
        if (isset($data['collectible_user_remove_reason_id'])) {
            
            if ($data['collectible_user_remove_reason_id'] == 1) {
                $data['listing_type_id'] = 2;
            } else if ($data['collectible_user_remove_reason_id'] == 2) {
                $data['listing_type_id'] = 3;
            }
        }
        // we must have this first
        if (!isset($data['listing_type_id']) || empty($data['listing_type_id'])) {
            $retVal['response']['isSuccess'] = false;
            $retVal['response']['data']['listing_type_id'] = __('Must be a valid listing type.');
            return $retVal;
        }
        
        $factory = new TransactionFactory();
        
        $transactionable = $factory->getTransaction($data['listing_type_id']);
        
        $retVal = $transactionable->createListing($this, $data, $user);
        
        if ($retVal['response']['isSuccess']) {
            $transactionId = $retVal['response']['data']['id'];
            $transaction = $this->find('first', array('contain' => array('User', 'Transaction', 'Collectible'), 'conditions' => array('Listing.id' => $transactionId)));
            // As of now, we just need to the id but we
            // can expand this later to return more if necessary
            $retVal['response']['data'] = $transaction['Listing'];
            $retVal['response']['data']['User'] = $transaction['User'];
            $retVal['response']['data']['Transaction'] = $transaction['Transaction'];
            $retVal['response']['isSuccess'] = true;
            // since we can only add attributes through collectibles right
            // now, do not do any event stuff here
            $this->getEventManager()->dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$USER_ADD_LISTING, 'user' => $user, 'object' => $transaction, 'type' => 'Listing')));
        }
        
        return $retVal;
    }
    /**
     *
     */
    public function updateListing($data, $user) {
        $retVal = $this->buildDefaultResponse();
        
        $factory = new TransactionFactory();
        
        $transactionable = $factory->getTransaction($data['Listing']['listing_type_id']);
        
        $retVal = $transactionable->updateListing($this, $data, $user);
        
        return $retVal;
    }
    /**
     * Running the api through Listing so it is all contained here
     */
    public function updateTransaction($data, $listing, $user) {
        $retVal = $this->buildDefaultResponse();
        // grab our transaction type
        $factory = new TransactionFactory();
        
        $transactionable = $factory->getTransaction($listing['Listing']['listing_type_id']);
        // if there is no transaction right now we need to create one, otherwise update
        // an existing one
        if (empty($listing['Listing']['Transaction'])) {
            $data = $transactionable->createTransaction($data, $listing, $user);
        } else {
            $data = $transactionable->updateTransaction($data, $listing, $user);
        }
        
        if ($this->Transaction->save($data, array('validate' => false))) {
            $this->clearCache($listing['Listing']['collectible_id']);
            $retVal['response']['isSuccess'] = true;
        }
        
        return $retVal;
    }
    /**
     * $id = collectible_id
     */
    public function clearCache($id) {
        Cache::delete($this->collectibleCacheKey . $id, 'collectible');
    }
}
?>
