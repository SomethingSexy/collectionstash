<?php
class Scale extends AppModel
{
    public $name = 'Scale';
    public $hasMany = array('Collectible');
    public $actsAs = array('Containable');
    public $displayField = 'scale';
}
?>
