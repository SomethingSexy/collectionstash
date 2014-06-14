<?php
class Comment extends AppModel
{
    public $name = 'Comment';
    //TODO: We need a counter cache for user
    public $belongsTo = array('EntityType' => array('counterCache' => true), 'User' => array('counterCache' => true, 'fields' => array('id', 'username')));
    public $hasMany = array('LatestComment' => array('dependent' => true));
    public $actsAs = array('Containable');
    
    public $validate = array('comment' => array('rule' => array('between', 10, 1000), 'allowEmpty' => false, 'message' => 'Comment must be at least 10 characters and less than 1000.'));
    
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($results[$key]['Comment'])) {
                if (isset($results[$key]['Comment']['created'])) {
                    $datetime = strtotime($results[$key]['Comment']['created']);
                    $mysqldate = date("m/d/y g:i A", $datetime);
                    $results[$key]['Comment']['formatted_created'] = $mysqldate;
                }
                
                if (isset($results[$key]['Comment']['comment'])) {
                    //Create a shorthand for this comment
                    $comment = $results[$key]['Comment']['comment'];
                    $commentLength = strlen($comment);
                    
                    if ($commentLength > 200) {
                        $comment = substr($comment, 0, 200);
                    }
                    $results[$key]['Comment']['shorthand_comment'] = $comment;
                }
            }
        }
        
        return $results;
    }
    
    public function afterSave($created) {
        if ($created) {
            if (!$this->LatestComment->saveLatest($this->data)) {
                CakeLog::write('error', 'There was a problem saving the last comment for ' . $this->data['Comment']['entity_type_id']);
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
        $currentComment = $this->find("first", array('conditions' => array('Comment.id' => $commentId), 'contain' => 'User'));
        if ($currentComment['User']['id'] === $userId) {
            $this->id = $commentId;
            if ($this->save($comment)) {
                $retVal['response']['isSuccess'] = true;
            } else {
                $retVal['response']['isSuccess'] = false;
                $errors = $this->validationErrors;
                $retVal['response']['errors'] = $errors;
            }
        } else {
            $error['code'] = 0;
            $error['message'] = 'Invalid access';
            array_push($retVal['response']['errors'], $error);
        }
        
        return $retVal;
    }
    /**
     * If the logged in user is an admin, the comment owner or the owned of the CommentType(passed in $userId === $ownerId),
     * then they can remove the comment
     *
     *
     * @param - $comment
     * @param - $userId This is the user id of the person logged in
     * @param - $ownerId This is the owner of the CommentType
     */
    public function removeComment($comment, $loggedInUserId = null) {
        $retVal = array();
        $retVal['response'] = array();
        $retVal['response']['isSuccess'] = false;
        $retVal['response']['message'] = '';
        $retVal['response']['code'] = 0;
        //Maybe this should be an error code
        $retVal['response']['errors'] = array();
        
        $allowed = $this->checkPermission('remove', $comment, $loggedInUserId);
        
        if (!$allowed) {
            $retVal['response']['message']  = 'Invalid access';
            return $retVal;
        }
        
        if ($this->delete($comment['Comment']['id'])) {
            $retVal['response']['isSuccess'] = true;
        } else {
            $retVal['response']['message']  = 'There was a problem deleting the comment';
        }
        
        return $retVal;
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
    public function getComments($entityTypeId = null, $userId = null, $conditions = array()) {
        $commentMetaData = array();
        //Get all comments
        //Grab the comment type first, I have a feeling this will be the fastest way to do this, instead of a join
        $entityType = $this->EntityType->find("first", array('conditions' => array('EntityType.id' => $entityTypeId), 'contain' => false));
        //Get the entity owner
        $ownerId = $this->EntityType->getEntityOwner($entityType);
        
        $conditions = array_merge(array('Comment.entity_type_id' => $entityType['EntityType']['id']), $conditions);
        
        $comments = $this->find("all", array('contain' => 'User', 'conditions' => $conditions));
        
        $commentMetaData = $this->addPermissions($comments, $userId, $ownerId);
        
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
            $loggedInUser = $this->User->find("first", array('contain' => false, 'conditions' => array('User.id' => $userId)));
            //Make sure it is a valid user first
            if (!empty($loggedInUser)) {
                //If they are an admin then they have all rights regardless
                foreach ($comments as $key => & $comment) {
                    $commentPermissions['permissions']['edit'] = false;
                    $commentPermissions['permissions']['remove'] = false;
                    //If they are admin, they get to remove anything
                    if ($loggedInUser['User']['admin']) {
                        $commentPermissions['permissions']['remove'] = true;
                    }
                    //If they are the owner, they also get to remove
                    if ($ownerId != null && is_numeric($ownerId) && $loggedInUser['User']['id'] === $ownerId) {
                        $ownerUser = $this->User->find("first", array('contain' => false, 'conditions' => array('User.id' => $ownerId)));
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
    /**
     * This method will check to make sure the given action is possible
     */
    private function checkPermission($action, $comment, $loggedInUserId = null) {
        $retVal = false;
        // This is the userid the person performing an action on the comment
        
        $commentId = $comment['Comment']['id'];
        
        if (!is_numeric($commentId)) {
            return false;
        }
        
        $actionComment = $this->find("first", array('conditions' => array('Comment.id' => $commentId), 'contain' => array('User', 'EntityType')));
        
        $ownerId = $stash = $this->EntityType->getEntityOwner($actionComment);
        
        $userId = $actionComment['Comment']['user_id'];
        //To remove the comment, they need to either be an admin, the one who added the comment or the owner of the domain of the comment
        if ($action === 'remove') {
            //First check to see if they are the one who is logged in, is the one who wrote the comment
            if ($userId === $loggedInUserId) {
                $retVal = true;
            } else if ($ownerId != null && is_numeric($ownerId) && $loggedInUserId === $ownerId) {
                //not going to validate ownerId here because it is coming from internal
                $retVal = true;
            } else {
                //Grab the logged in user and see if they are an admin
                $loggedInUser = $this->User->find("first", array('conditions' => array('User.id' => $loggedInUserId)));
                if (!empty($loggedInUser) && $loggedInUser['User']['admin']) {
                    $retVal = true;
                }
            }
        }
        
        return $retVal;
    }
}
?>
