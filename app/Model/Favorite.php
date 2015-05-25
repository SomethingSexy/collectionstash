<?php
class Favorite extends AppModel {
    public $name = 'Favorite';
    public $hasOne = array('UserFavorite', 'CollectibleFavorite');
    public $actsAs = array('Containable');
    function afterFind($results, $primary = false) {
        if ($results && $primary) {
            foreach ($results as $key => $val) {
                if (isset($val['UserFavorite']) && is_null($val['UserFavorite']['id']))  {
                    unset($results[$key]['UserFavorite']);
                }
                if (isset($val['CollectibleFavorite']) && is_null($val['CollectibleFavorite']['id']))  {
                    unset($results[$key]['CollectibleFavorite']);
                }
            }
        }

        return $results;
    }
}
?>