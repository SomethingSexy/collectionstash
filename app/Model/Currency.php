<?php
class Currency extends AppModel {
    var $name = 'Currency';
    var $hasMany = array('Collectible');
    var $actsAs = array('Containable');
}
?>
