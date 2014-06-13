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
                debug($lastestComments);
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
        } else if ($this->request->isGet()) {
        }
    }
    //This is the old way of doing it
    public function update() {
        $data = array();
        if (!$this->isLoggedIn()) {
            $data['success'] = array('isSuccess' => false);
            $data['error']['message'] = __('Clearly I broke something because you are not logged in yet but somehow you are able to submit an update.');
            $this->set('comments', $data);
            return;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data = Sanitize::clean($this->request->data);
            $this->request->data['Comment']['user_id'] = $this->getUserId();
            
            $response = $this->Comment->updateComment($this->request->data);
            if ($response) {
                $this->set('comments', $response);
            } else {
                //Something really fucked up
                $data['success'] = array('isSuccess' => false);
                $data['error'] = array('message', __('Invalid request.'));
                $this->set('comments', $data);
            }
        } else {
            $data['success'] = array('isSuccess' => false);
            $data['error'] = array('message', __('Invalid request.'));
            $this->set('comments', $data);
            return;
        }
    }
    //This is the old way of doing it
    public function remove() {
        $data = array();
        if (!$this->isLoggedIn()) {
            $data['success'] = array('isSuccess' => false);
            $data['error']['message'] = __('Clearly I broke something because you are not logged in yet but somehow you are able to submit an update.');
            $this->set('comments', $data);
            return;
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data = Sanitize::clean($this->request->data);
            $userId = $this->getUserId();
            $response = $this->Comment->removeComment($this->request->data, $userId);
            if ($response) {
                $this->set('comments', $response);
            } else {
                //Something really fucked up
                $data['success'] = array('isSuccess' => false);
                $data['error'] = array('message', __('Invalid request.'));
                $this->set('comments', $data);
            }
        } else {
            $data['success'] = array('isSuccess' => false);
            $data['error'] = array('message', __('Invalid request.'));
            $this->set('comments', $data);
            return;
        }
    }
    /**
     * This is the old way of doing it
     *
     * Adds a comment
     * Post
     *  - comment
     *  - last comment in the view
     *  - type
     *  - type_id
     */
    public function add() {
        $data = array();
        //must be logged in to post comment
        if (!$this->isLoggedIn()) {
            $data['success'] = array('isSuccess' => false);
            $data['error']['message'] = __('You must be logged in to post a comment.');
            $this->set('comments', $data);
            return;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data = Sanitize::clean($this->request->data);
            $this->request->data['Comment']['user_id'] = $this->getUserId();
            //TODO: After save we need to also return all comments inbetween this update the last one they viewed.
            if ($this->Comment->saveAll($this->request->data)) {
                $data['comments'] = array();
                $data['success'] = array('isSuccess' => true);
                $commentId = $this->Comment->id;
                $comment = $this->Comment->findById($commentId);
                $entity = $this->Comment->EntityType->getEntityCore($comment['Comment']['entity_type_id']);
                
                $lastestComments = array();
                
                if (isset($this->request->data['Comment']['last_comment_created']) && !empty($this->request->data['Comment']['last_comment_created'])) {
                    $lastestComments = $this->Comment->getComments($this->request->data['Comment']['entity_type_id'], $this->request->data['Comment']['user_id'], array('Comment.created >' => $this->request->data['Comment']['last_comment_created'], 'and' => array('Comment.created <=' => $comment['Comment']['created'])));
                } else {
                    $lastestComments = $this->Comment->getComments($this->request->data['Comment']['entity_type_id'], $this->request->data['Comment']['user_id']);
                }
                
                if (!empty($lastestComments)) {
                    $data['comments'] = $lastestComments['comments'];
                }
                $this->getEventManager()->dispatch(new CakeEvent('Controller.Comment.add', $this, array('commentId' => $commentId, 'userId' => $this->request->data['Comment']['user_id'], 'entityTypeId' => $this->request->data['Comment']['entity_type_id'])));
                
                $this->getEventManager()->dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$ADD_COMMENT, 'user' => $this->getUser(), 'comment' => $comment, 'entity' => $entity)));
                
                $this->set('comments', $data);
            } else {
                $data['success'] = array('isSuccess' => false);
                $errors = $this->Comment->invalidFields();
                /*
                 * If there is an error, check to see if any of the fields were invalid. if they
                 * were the only user inputted one is the comment field, otherwise use a generic error mesage
                */
                if (!empty($errors)) {
                    $data['error']['message'] = $errors['comment'][0];
                } else {
                    $data['error']['message'] = __('Sorry, your request was invalid.');
                }
                $this->set('comments', $data);
            }
        } else {
            $data['success'] = array('isSuccess' => false);
            $data['error'] = array('message', __('Invalid request.'));
            $this->set('comments', $data);
            return;
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