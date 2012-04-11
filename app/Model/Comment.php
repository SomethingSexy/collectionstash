<?php
class Comment extends AppModel {
    public $name = 'Comment';
    //TODO: We need a counter cache for user
    public $belongsTo = array('CommentType', 'User' => array('fields' => array('id', 'username')));
    public $hasMany = array('LatestComment');
    public $actsAs = array('Containable');

    public $validate = array(
    //description field
    'comment' => array('minLength' => array('rule' => array('minLength', 10), 'message' => 'Comment must be at least 10 characters.'), 'maxLength' => array('rule' => array('maxLength', 1000), 'message' => 'Comment cannot be more than 1000 characters.')), );

    function beforeValidate() {
        //right now we only have comments on stashes
        if ($this -> data['Comment']['type'] !== 'stash') {
            return false;
        }
        $typeId = $this -> data['Comment']['type_id'];
        $model = null;
        //Do I want to valid that the id I am posting too is valid?
        if ($this -> data['Comment']['type'] === 'stash') {

            $model = $this -> User -> Stash -> find("first", array('conditions' => array('Stash.id' => $typeId)));
        }

        if ($model === null || empty($model)) {
            return false;
        }

        return true;
    }

    public function afterFind($results, $primary = false) {
        debug($results);
        debug($primary);
        foreach ($results as $key => &$val) {
            $datetime = strtotime($val['Comment']['created']);
            $mysqldate = date("m/d/y g:i A", $datetime);
            $val['Comment']['formatted_created'] = $mysqldate;
        }
        return $results;
    }

    public function beforeSave() {
        //Update the id is set, so we don't have to worry about this
        $retVal = true;
        if (empty($this -> id)) {
            $type = $this -> data['Comment']['type'];
            $type_id = $this -> data['Comment']['type_id'];

            $commentType = $this -> CommentType -> find('first', array('conditions' => array('CommentType.type' => $type, 'CommentType.type_id' => $type_id)));
            if (empty($commentType)) {
                $commentTypeForSave = array();
                $commentTypeForSave['CommentType']['type'] = $type;
                $commentTypeForSave['CommentType']['type_id'] = $type_id;
                if ($this -> CommentType -> save($commentTypeForSave)) {
                    $commentType['CommentType']['id'] = $this -> CommentType -> id;
                } else {
                    $retVal = false;
                }
            }
            $this -> data['Comment']['comment_type_id'] = $commentType['CommentType']['id'];
        }

        if ($retVal) {
            unset($this -> data['Comment']['type']);
            unset($this -> data['Comment']['type_id']);
        }

        return $retVal;
    }

    public function afterSave($created) {
        if ($created) {
            if (!$this -> LatestComment -> saveLatest($this -> data)) {
                CakeLog::write('error', 'There was a problem saving the last comment for ' . $this -> data['Comment']['comment_type_id']);
            }
        }
    }

    /**
     * This is the start of a standard of how to return stuff for updates and committs so that we can do them
     * via the model and also return proper data.
     *
     * For now I am not saving history of the comment
     */
    public function updateComment($comment) {
        $retVal = array();
        $retVal['response'] = array();
        $retVal['response']['isSuccess'] = false;
        $retVal['response']['message'] = '';
        $retVal['response']['code'] = 0;
        //Maybe this should be an error code
        $retVal['response']['errors'] = array();

        // This is the userid the person updating the comment
        $userId = $comment['Comment']['user_id'];
        $commentId = $comment['Comment']['id'];

        //Check to make sure this logged in user has permission to update this comment.
        //They need to be the one who added the comment original
        $currentComment = $this -> find("first", array('conditions' => array('Comment.id' => $commentId), 'contain' => 'User'));
        if ($currentComment['User']['id'] === $userId) {
            $this -> id = $commentId;
            if ($this -> save($comment)) {
                $retVal['response']['isSuccess'] = true;
            } else {
                $retVal['response']['isSuccess'] = false;
                $errors = $this -> validationErrors;
                $retVal['response']['errors'] = $errors;
            }
        } else {
            $error['code'] = 0;
            $error['message'] = 'Invalid access';
            array_push($retVal['response']['errors'], $error);
        }

        return $retVal;
    }

    public function removeComment($comment, $ownerId = null) {
        //To remove the comment, they need to either be an admin, the one who added the comment or the owner of the domain of the comment
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
        //Get all comments
        //Grab the comment type first, I have a feeling this will be the fastest way to do this, instead of a join
        $commentType = $this -> CommentType -> find("first", array('conditions' => array('CommentType.type' => $type, 'CommentType.type_id' => $typeID), 'contain' => false));
        $conditions = array_merge(array('Comment.comment_type_id' => $commentType['CommentType']['id']), $conditions);

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
                foreach ($comments as $key => &$comment) {
                    $commentPermissions['permissions']['edit'] = false;
                    $commentPermissions['permissions']['remove'] = false;
                    //If they are admin, they get to remove anything
                    if ($loggedInUser['User']['admin']) {
                        $commentPermissions['permissions']['remove'] = true;
                    }
                    //If they are the owner, they also get to remove
                    if ($ownerId != null && is_numeric($ownerId) && $userId === $ownerId) {
                        $ownerUser = $this -> User -> find("first", array('conditions' => array('User.id' => $ownerId)));
                        //If the logged in user and the owner are the same, give them mod rights
                        if (!empty($ownerUser)) {
                            //If there is an owner and it is the same as the logged in user then give them "mod" rights over all comments
                            //An owner can just remove other's comments but not edit
                            $commentPermissions['permissions']['remove'] = true;
                        }
                    }
                    //If they are the one who wrote the comment, they get to edit and remove
                    if ($comment['User']['id'] === $userId) {
                        $commentPermissions['permissions']['edit'] = true;
                        $commentPermissions['permissions']['remove'] = true;
                    }

                    $comment['permissions'] = $commentPermissions['permissions'];
                }
            }
        }

        $commentMetaData['comments'] = $comments;
        //We might have to have some hardcoded stuff in here because of special cases, like stash

        return $commentMetaData;
    }

}
?>
