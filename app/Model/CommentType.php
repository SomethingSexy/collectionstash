<?php
class CommentType extends AppModel {
    public $name = 'CommentType';
    public $hasMany = array('Comment');
    public $belongsTo = array('Stash' => array('conditions' => array('CommentType.type' => 'stash'), 'foreignKey' => 'type_id'));
    public $actsAs = array('Containable');
}
?>
