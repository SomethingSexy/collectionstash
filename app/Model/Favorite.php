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
}
?>