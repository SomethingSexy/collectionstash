<?php
class UserFavorite extends AppModel
{
    public $name = 'UserFavorite';
    public $belongsTo = array('Favorite', 'User');
    public $actsAs = array('Containable');
}
?>