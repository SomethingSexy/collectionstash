<?php
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class Favorite extends AppModel {
    public $name = 'Favorite';
    public $hasOne = array('UserFavorite' => array('dependent' => true), 'CollectibleFavorite' => array('dependent' => true));
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
    /**
     * Gets favorites and organizes them by type, either User or Collectible
     */
    public function getFavorites($user) {
        $organize = array('User' => array(), 'Collectible' => array());
        $favorites = $this->find('all', array('conditions' => array('Favorite.user_id' => $user['User']['id'])));
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
    
    public function getCollectibleFavorite($id, $user) {
        return $this->CollectibleFavorite->find('first', array('conditions' => array('CollectibleFavorite.collectible_id' => $id), 'joins' => array(array('table' => 'favorites', 'alias' => 'Favorite1', 'type' => 'inner', 'conditions' => array('CollectibleFavorite.favorite_id = Favorite1.id', 'Favorite1.user_id = ' . $user['User']['id'])))));
    }
    
    public function getUserFavorite($id, $user) {
        return $this->UserFavorite->find('first', array('conditions' => array('UserFavorite.user_id' => $id), 'joins' => array(array('table' => 'favorites', 'alias' => 'Favorite1', 'type' => 'inner', 'conditions' => array('UserFavorite.favorite_id = Favorite1.id', 'Favorite1.user_id = ' . $user['User']['id'])))));
    }
    
    public function isFavorited($id, $user, $type) {
        if ($type === 'collectible') {
            return !!$this->getCollectibleFavorite($id, $user);
        } 
        else if ($type === 'user') {
            return !!$this->getUserFavorite($id, $user);
        } 
        else {
            return false;
        }
    }
    /**
     * This will add a subscription to the given model, model id and the user who is adding ths subscription
     *
     * just goin to add/remove for now
     */
    public function addSubscription($id, $type, $user, $subscribed = null) {
        $retVal = false;
        
        $subscribed = ($subscribed === true || $subscribed === 'true');
        
        if ($type === 'collectible') {
            // if we are subscribing, check to see if we are already subscribed
            if ($subscribed) {
                // if one already exists, just return true
                if (count($this->getCollectibleFavorite($id, $user)) > 0) {
                    $retVal = true;
                } 
                else {
                    $data['Favorite'] = array('user_id' => $user['User']['id']);
                    $data['CollectibleFavorite'] = array('collectible_id' => $id);
                    
                    if ($this->saveAssociated($data, array('validate' => false, 'deep' => true))) {
                        $collectible = $this->CollectibleFavorite->Collectible->find('first', array('contain' => array('CollectiblesUpload' => array('Upload'), 'Manufacture', 'User', 'ArtistsCollectible' => array('Artist')), 'conditions' => array('Collectible.id' => $id)));
                        $this->getEventManager()->dispatch(new CakeEvent('Model.Activity.add', $this, array('activityType' => ActivityTypes::$ADD_FAVORITE, 'user' => $user, 'collectible' => $collectible)));
                        $retVal = true;
                    }
                }
            } 
            else {
                // at this point we want to remove our subscription
                $favorite = $this->getCollectibleFavorite($id, $user);
                
                if (!empty($favorite)) {
                    if ($this->removeFavorite($favorite['Favorite']['id'])) {
                        $retVal = true;
                    }
                } 
                else {
                    $retVal = true;
                }
            }
        } 
        else if ($type === 'user') {
            if ($subscribed) {
                // if one already exists, just return true
                if (count($this->getUserFavorite($id, $user)) > 0) {
                    $retVal = true;
                } 
                else {
                    $data['Favorite'] = array('user_id' => $user['User']['id']);
                    $data['UserFavorite'] = array('user_id' => $id);
                    
                    if ($this->saveAssociated($data, array('validate' => false, 'deep' => true))) {
                        $retVal = true;
                        $userFavorite = $this->User->find('first', array('contain' => false, 'conditions' => array('User.id' => $id)));
                        $this->getEventManager()->dispatch(new CakeEvent('Model.Activity.add', $this, array('activityType' => ActivityTypes::$ADD_FAVORITE, 'user' => $user, 'userFavorite' => $userFavorite)));
                        $retVal = true;
                    }
                }
            } 
            else {
                // at this point we want to remove our subscription
                $favorite = $this->getUserFavorite($id, $user);
                
                if (!empty($favorite)) {
                    if ($this->removeFavorite($favorite['Favorite']['id'])) {
                        $retVal = true;
                    }
                } 
                else {
                    $retVal = true;
                }
            }
        }
        
        return $retVal;
    }
    /**
     *
     */
    public function removeFavorite($id, $user) {
        // find the favorite...make sure they can delete the favorite
        $favorite = $this->find('first', array('conditions' => array('Favorite.id' => $id)));
        
        if (!$favorite) {
            return false;
        }
        
        if ($favorite['Favorite']['user_id'] !== $user['User']['id']) {
            return false;
        }
        
        if ($this->delete($id)) {
            if (isset($favorite['CollectibleFavorite'])) {
                $collectible = $this->CollectibleFavorite->Collectible->find('first', array('contain' => array('CollectiblesUpload' => array('Upload'), 'Manufacture', 'User', 'ArtistsCollectible' => array('Artist')), 'conditions' => array('Collectible.id' => $favorite['CollectibleFavorite']['collectible_id'])));
                $this->getEventManager()->dispatch(new CakeEvent('Model.Activity.add', $this, array('activityType' => ActivityTypes::$REMOVE_FAVORITE, 'user' => $user, 'collectible' => $collectible)));
            } 
            else if (isset($favorite['UserFavorite'])) {
                // do we want to inform everyone that a user unfavorited another user?
                
            }
            
            return true;
        } 
        else {
            return false;
        }
    }
}
?>