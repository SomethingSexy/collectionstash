<?php
class Comment extends AppModel {
    public $name = 'Comment';
    public $belongsTo = array('User' => array('fields' => array('id', 'username')), 'Stash' => array('conditions' => array('Comment.type' => 'stash'), 'foreignKey' => 'type_id'));
    public $actsAs = array('Containable');

    public $validate = array(
    //description field
    'comment' => array('minLength' => array('rule' => array('minLength', 10), 'message' => 'Comment must be at least 10 characters.'), 'maxLength' => array('rule' => array('maxLength', 1000), 'message' => 'Comment cannot be more than 1000 characters.')), );

    function beforeValidate() {
        //right now we only have comments on stashes
        debug($this -> data);
        if ($this -> data['Comment']['type'] !== 'stash') {
            return false;
        }
        $typeId = $this -> data['Comment']['type_id'];
        $model = null;
        //Do I want to valid that the id I am posting too is valid?
        if ($this -> data['Comment']['type'] === 'stash') {
            $model = $this -> Stash -> find("first", array('conditions' => array('Stash.id' => $typeId)));
        }

        if ($model === null || empty($model)) {
            return false;
        }

        return true;
    }

    public function afterFind($results, $primary = false) {
        foreach ($results as $key => &$val) {
            $datetime = strtotime($val['Comment']['created']);
            $mysqldate = date("m/d/y g:i A", $datetime);
            $val['Comment']['created'] = $mysqldate;
        }

        return $results;
    }

}
?>
