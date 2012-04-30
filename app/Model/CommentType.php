<?php
/**
 * This should be turned into a generic model type object.  Not sure I 100% agree with this type of design but it will
 * prevent me from having a whole crap ton of extra tables.  
 * 
 * This will be used to store references to different models to link up comments and subscriptions, or anything else that needs to dynamically links to models
 * 
 * I will be renaming this model and table so I don't have to make existing changes to the database
 */
class EntityType extends AppModel {
    public $name = 'EntityType';
    public $hasMany = array('Comment');
    public $belongsTo = array('Stash' => array('conditions' => array('EntityType.type' => 'stash'), 'foreignKey' => 'type_id'));
    public $actsAs = array('Containable');

    public function afterFind($results, $primary = false) {
        foreach ($results as $key => &$val) {
            if ($val['EntityType']['type'] === 'stash') {
                $stash = $this -> Stash -> find("first", array('conditions' => array('Stash.id' => $val['EntityType']['type_id']), 'contain' => array('User'=>array('fields'=>'username'))));
                $val['EntityType']['Stash'] = $stash['Stash'];
                $val['EntityType']['User'] = $stash['User'];
            }
        }
        return $results;
    }

}
