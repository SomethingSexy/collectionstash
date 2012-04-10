<?php
class CommentType extends AppModel {
    var $name = 'CommentType';
    var $hasMany = array('Comment');
    var $actsAs = array('Containable');
}
?>
