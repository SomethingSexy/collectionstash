<?php
class CollectibleUser extends AppModel
{
    var $name = 'CollectibleUser';
    var $belongsTo = array('User', 'Collectible');
}

?>