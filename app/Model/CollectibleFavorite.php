<?php
class CollectibleFavorite extends AppModel
{
    public $name = 'CollectibleFavorite';
    public $belongsTo = array('Favorite', 'Collectible');
    public $actsAs = array('Containable');

    
}
?>