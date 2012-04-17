<?php
class CommentType extends AppModel {
    public $name = 'CommentType';
    public $hasMany = array('Comment');
    public $belongsTo = array('Stash' => array('conditions' => array('CommentType.type' => 'stash'), 'foreignKey' => 'type_id'));
    public $actsAs = array('Containable');

    public function afterFind($results, $primary = false) {
        foreach ($results as $key => &$val) {
            if ($val['CommentType']['type'] === 'stash') {
                $stash = $this -> Stash -> find("first", array('conditions' => array('Stash.id' => $val['CommentType']['type_id']), 'contain' => array('User'=>array('fields'=>'username'))));
                $val['CommentType']['Stash'] = $stash['Stash'];
                $val['CommentType']['User'] = $stash['User'];
            }
        }
        return $results;
    }

}
