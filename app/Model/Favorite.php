<?php
class Favorite extends AppModel {
    public $name = 'Favorite';
    public $hasOne = array('UserFavorite', 'CollectibleFavorite');
    public $belongsTo = array('User');
    public $actsAs = array('Containable');
    function afterFind($results, $primary = false) {
        if ($results && $primary) {
            foreach ($results as $key => $val) {
                if (isset($val['UserFavorite']) && is_null($val['UserFavorite']['id'])) {
                    unset($results[$key]['UserFavorite']);
                }
                if (isset($val['CollectibleFavorite']) && is_null($val['CollectibleFavorite']['id'])) {
                    unset($results[$key]['CollectibleFavorite']);
                }
            }
        }
        
        return $results;
    }
    
    public function getFavorites($userId) {
        $organize = array('User' => array(), 'Collectible' => array());
        $favorites = $this->find('all', array('conditions' => array('Favorite.user_id' => $userId)));
        foreach ($favorites as $key => & $favorite) {
            if (isset($favorite['UserFavorite'])) {
                $favorite['id'] = $favorite['UserFavorite']['user_id'];
                array_push($organize['User'], $favorite);
            } 
            else if (isset($favorite['CollectibleFavorite'])) {
                $favorite['id'] = $favorite['CollectibleFavorite']['collectible_id'];
                array_push($organize['Collectible'], $favorite);
            }
        }
        
        return $organize;
    }
    /**
     * This will add a subscription to the given model, model id and the user who is adding ths subscription
     */
    public function addSubscription($id, $type, $user_id, $subscribed = null) {
        $subscription = array();
        
        if ($type === 'collectible') {
            // if we are subscribing, check to see if we are already subscribed
            if ($subscribed) {
                if (count($this->CollectibleFavorite->find('first', array('conditions' => array('CollectibleFavorite.collectible_id' => $id)))) > 0) {
                    // do nothing
                    
                    
                } 
                else {
                    $data = array('id' => 10, 'title' => 'My new title');
                    $this->CollectibleFavorite->create();
                    // This will update Recipe with id 10
                    $this->CollectibleFavorite->save($data);
                }
            }
        } 
        else if ($type === 'stash') {
        }
        // Doing this here, it really shouln't be a big deal since this will be done by user for their own stuff
        $alreadyExist = $this->find("first", array('conditions' => array('Subscription.entity_type_id' => $entityTypeId, 'Subscription.user_id' => $user_id)));
        
        if (!empty($alreadyExist)) {
            $subscription['Subscription']['id'] = $alreadyExist['Subscription']['id'];
        }
        
        if ($subscribed === null || $subscribed === 'true') {
            $subscription['Subscription']['subscribed'] = 1;
        } 
        else {
            $subscription['Subscription']['subscribed'] = 0;
        }
        $subscription['Subscription']['entity_type_id'] = $entityTypeId;
        $subscription['Subscription']['user_id'] = $user_id;
        
        if ($this->save($subscription)) {
            return true;
        } 
        else {
            return false;
        }
    }
}
?>