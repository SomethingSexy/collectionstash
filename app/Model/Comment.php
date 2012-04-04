<?php
class Comment extends AppModel {
    public $name = 'Comment';
    //TODO: We need a counter cache for user
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
            $val['Comment']['formatted_created'] = $mysqldate;
        }
        return $results;
    }

    /*
     * Might have to do custom pagination on this at some point
     *
     * The userId will be the user who is logged in, we will use this to determine
     * what access rights the user has for the individual comment or all of the comments
     *
     * If the userId is null then there are no special actions added
     *
     * The owner id will be the userId of the person who might "own" these comments.  That will
     * allow me to not necessarly have to hardcode anything in here.
     */
    public function getComments($type = null, $typeID = null, $userId = null, $ownerId = null, $conditions = array()) {
        $commentMetaData = array();
        //These are main level permissions that would override all individual comment permissions
        //$commentMetaData['permissions']['edit'] = false;
        //$commentMetaData['permissions']['remove'] = false;
        //Get all comments
        $conditions = array_merge(array('Comment.type' => $type, 'Comment.type_id' => $typeID), $conditions);

        $comments = $this -> find("all", array('contain' => 'User', 'conditions' => $conditions));

        $commentMetaData = $this -> addPermissions($comments, $userId, $ownerId);

        return $commentMetaData;
    }

    /**
     * This method will add the permissions to each comment
     */
    private function addPermissions($comments, $userId = null, $ownerId = null) {
        $commentMetaData = array();
        //These are main level permissions that would override all individual comment permissions
        //$commentMetaData['permissions']['edit'] = false;
        //$commentMetaData['permissions']['remove'] = false;
        //If the user Id is null then continue because no one is logged in who is viewing
        //so no permissions are given
        if ($userId !== null && is_numeric($userId)) {
            //Grab the user information for the person who is logged in and viewing these comments
            $loggedInUser = $this -> User -> find("first", array('conditions' => array('User.id' => $userId)));
            //Make sure it is a valid user first
            if (!empty($loggedInUser)) {
                //If they are an admin then they have all rights regardless
                if ($loggedInUser['User']['admin']) {
                    //An admin can remove and edit
                    $commentPermissions['permissions']['edit'] = true;
                    $commentPermissions['permissions']['remove'] = true;

                    foreach ($comments as $key => &$comment) {
                        $comment['permissions'] = $commentPermissions['permissions'];
                    }

                } else {
                    //see if they have an owner
                    if ($ownerId != null && is_numeric($ownerId) && $userId === $ownerId) {
                        $ownerUser = $this -> User -> find("first", array('conditions' => array('User.id' => $ownerId)));
                        //If the logged in user and the owner are the same, give them mod rights
                        if (!empty($ownerUser)) {
                            //If there is an owner and it is the same as the logged in user then give them "mod" rights over all comments
                            //An owner can just remove other's comments but not edit
                            $commentPermissions['permissions']['remove'] = true;
                            $commentPermissions['permissions']['edit'] = false;

                            foreach ($comments as $key => &$comment) {
                                $comment['permissions'] = $commentPermissions['permissions'];
                            }

                        }
                    } else {
                        //there is no owner and they are not an admin so now we need to do individual comment permissions to
                        //see if any of the comments that were posted were posted by the user who is logged in

                        foreach ($comments as $key => &$comment) {
                            $commentPermissions = array();
                            if ($comment['User']['id'] === $userId) {
                                $commentPermissions['permissions']['edit'] = true;
                                $commentPermissions['permissions']['remove'] = true;
                            } else {
                                $commentPermissions['permissions']['edit'] = false;
                                $commentPermissions['permissions']['remove'] = false;
                            }

                            $comment['permissions'] = $commentPermissions['permissions'];
                        }
                    }
                }
            }
        }

        $commentMetaData['comments'] = $comments;
        //We might have to have some hardcoded stuff in here because of special cases, like stash

        return $commentMetaData;
    }

}
?>
