<?php
class LatestComment extends AppModel {
    public $name = 'LatestComment';
    public $belongsTo = array('CommentType', 'Comment', 'User' => array('fields' => array('id', 'username')));
    public $actsAs = array('Containable');

    /**
     * This saves the comment as the latest comment for the given comment type.  The comment
     * type is unique in this table, we only want to see the latest comments for
     * each comment type.
     *
     * This will check to see if one exists, if it doesn't then it will create, otherwise update
     */
    public function saveLatest($comment) {
        $retVal = true;
        //Hmm, going to try this to force a transaction around the find and update/save
        $dataSource = $this -> getDataSource();
        $dataSource -> begin();
        $latestComment = array();
        $latestComment['LatestComment']['comment_id'] = $comment['Comment']['id'];
        $latestComment['LatestComment']['comment_type_id'] = $comment['Comment']['comment_type_id'];
        $latestComment['LatestComment']['user_id'] = $comment['Comment']['user_id'];

        $existingLatestComment = $this -> find("first", array('contain' => false, 'conditions' => array('LatestComment.comment_type_id' => $comment['Comment']['comment_type_id'])));
        if (!empty($existingLatestComment)) {
            $latestComment['LatestComment']['id'] = $existingLatestComment['LatestComment']['id'];
            $this -> id = $existingLatestComment['LatestComment']['id'];
        }

        $comment = $latestComment;
        if (!$this -> save($comment)) {
            $retVal = false;
        }
        //Commit regardless
        $dataSource -> commit();
        return $retVal;
    }

}
?>
