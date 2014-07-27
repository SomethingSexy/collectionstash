<?php
App::uses('Sanitize', 'Utility');
App::uses('ActivityTypes', 'Lib/Activity');
class CommentsController extends AppController
{
    
    public $helpers = array('Html', 'Js', 'Minify', 'Time');
    /**
     * This is the main entry way into "Discussion" page.
     */
    public function index() {
        $this->paginate = array('limit' => 10, 'order' => array('LatestComment.modified' => 'desc'));
        $comments = $this->paginate('LatestComment');
        $this->set(compact('comments'));
    }
    // add/update/remove for new backbone architecture
    public function comment($id = null) {
        $this->autoRender = false;
        if (!$this->isLoggedIn()) {
            $data['response'] = array();
            $data['response']['isSuccess'] = false;
            $this->response->statusCode(401);
            return;
        }
        
        if ($this->request->isPut()) {
            $comment = $this->request->input('json_decode', true);
            $comment['Comment'] = Sanitize::clean($comment);
            $comment['Comment']['user_id'] = $this->getUserId();
            $response = $this->Comment->updateComment($comment);
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
                $this->response->body(json_encode($response['response']['data']));
            } else {
                $this->response->body('{}');
            }
        } else if ($this->request->isPost()) {
            $postedComment = $this->request->input('json_decode', true);
            $postedComment['Comment'] = Sanitize::clean($postedComment);
            $postedComment['Comment']['user_id'] = $this->getUserId();
            
            if ($this->Comment->saveAll($postedComment)) {
                $data = array();
                $commentId = $this->Comment->id;
                $comment = $this->Comment->findById($commentId);
                $entity = $this->Comment->EntityType->getEntityCore($comment['Comment']['entity_type_id']);
                
                $lastestComments = array();
                
                if (isset($postedComment['Comment']['last_comment_created']) && !empty($postedComment['Comment']['last_comment_created'])) {
                    $lastestComments = $this->Comment->getComments($comment['Comment']['entity_type_id'], $comment['Comment']['user_id'], array('Comment.created >' => $postedComment['Comment']['last_comment_created'], 'and' => array('Comment.created <=' => $comment['Comment']['created'])));
                } else {
                    $lastestComments = $this->Comment->getComments($comment['Comment']['entity_type_id'], $comment['Comment']['user_id']);
                }
                
                if (!empty($lastestComments)) {
                    $extractComments = Set::extract('/Comment/.', $lastestComments['comments']);
                    
                    foreach ($extractComments as $key => $value) {
                        $extractComments[$key]['User'] = $lastestComments['comments'][$key]['User'];
                        $extractComments[$key]['permissions'] = $lastestComments['comments'][$key]['permissions'];
                    }
                    
                    $data = $extractComments;
                }
                
                $this->getEventManager()->dispatch(new CakeEvent('Controller.Comment.add', $this, array('commentId' => $commentId, 'userId' => $comment['Comment']['user_id'], 'entityTypeId' => $comment['Comment']['entity_type_id'])));
                
                $this->getEventManager()->dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$ADD_COMMENT, 'user' => $this->getUser(), 'comment' => $comment, 'entity' => $entity)));
                
                $this->response->body(json_encode($data));
                $this->response->body(json_encode($data));
            } else {
                $data['success'] = array('isSuccess' => false);
                $this->response->statusCode(400);
                $this->response->body(json_encode($this->Comment->validationErrors));
            }
        } else if ($this->request->isDelete()) {
            $userId = $this->getUserId();
            $comment['Comment'] = array('id' => $id);
            $response = $this->Comment->removeComment($comment, $userId);
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(500);
                $this->response->body(json_encode($response));
            }
        } else if ($this->request->isGet()) {
        }
    }
    
    public function view($entityTypeId = null) {
        /*
         * At this point, I think we need to get the security settings for each comment.
         *
         * Rules:
         *  - If you are viewing comments owned by someone the person who owns it is a "mod", (stash)
         *  - If you are an admin then you can all actions for all comments
         *  - If you are viewing general comments, you get actions for your own comments
        */
        
        $userId = null;
        
        if ($this->isLoggedIn()) {
            $userId = $this->getUserId();
        }
        
        $comments = $this->Comment->getComments($entityTypeId, $userId);
        
        $this->set('comments', array('commentsData' => $comments));
    }
}
?>