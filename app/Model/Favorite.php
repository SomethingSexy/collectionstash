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
     * 
     * just goin to add/remove for now
     */
    public function addSubscription($id, $type, $user_id, $subscribed = null) {
       $retVal = false;
        
        if ($type === 'collectible') {
            // if we are subscribing, check to see if we are already subscribed
            if ($subscribed) {
                if (count($this->CollectibleFavorite->find('first', array('conditions' => array('CollectibleFavorite.collectible_id' => $id), 'joins' => array(array('table' => 'favorites', 'alias' => 'Favorite1', 'type' => 'inner', 'conditions' => array('CollectibleFavorite.favorite_id = Favorite1.id', 'Favorite1.user_id = '. $user_id)))))) > 0) {
                    // do nothing
                    
                    
                } 
                else {
                    $data['Favorite'] = array('user_id' => $user_id);
                    $data['CollectibleFavorite'] = array('collectible_id' => $id);
                    if($this->saveAssociated($data, array('validate' => false, 'deep' => true))){
                        $retVal = true;
                    }
                }
            }
        } 
        else if ($type === 'stash') {

        }

        return $retVal;
    }
}
?>