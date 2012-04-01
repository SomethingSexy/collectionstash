<?php
App::uses('Sanitize', 'Utility');
class CommentsController extends AppController {

    public $helpers = array('Html', 'Js', 'Minify');

    /*
     * Adds a comment
     * Post
     *  - comment
     *  - last comment in the view
     *  - type
     *  - type_id
     */
    function add() {
        $data = array();
        //must be logged in to post comment
        if (!$this -> isLoggedIn()) {
            $data['success'] = array('isSuccess' => false);
            $data['error']['message'] = __('You must be logged in to post a comment.');
            $this -> set('comments', $data);
            return;
        }
        if ($this -> request -> is('post') || $this -> request -> is('put')) {
            debug($this -> request -> data);
            $this -> request -> data = Sanitize::clean($this -> request -> data);
            $this -> request -> data['Comment']['user_id'] = $this -> getUserId();
            //TODO: After save we need to also return all comments inbetween this update the last one they viewed.
            if ($this -> Comment -> saveAll($this -> request -> data)) {
                $data['success'] = array('isSuccess' => true);
                $commentId = $this -> Comment -> id;
                $comment = $this -> Comment -> findById($commentId);
                $data['comment'] = $comment;
                $this -> set('comments', $data);
                
            } else {
                $data['success'] = array('isSuccess' => false);
                $errors = $this -> Comment -> invalidFields();
                debug($errors);
                /*
                 * If there is an error, check to see if any of the fields were invalid. if they
                 * were the only user inputted one is the comment field, otherwise use a generic error mesage
                 */
                if (!empty($errors)) {
                    $data['error']['message'] = $errors['comment'][0];
                } else {
                    $data['error']['message'] = __('Sorry, your request was invalid.');
                }
                $this -> set('comments', $data);
            }

        } else {
            $data['success'] = array('isSuccess' => false);
            $data['error'] = array('message', __('Invalid request.'));
            $this -> set('comments', $data);
            return;
        }

    }

    function view($type = null, $typeID = null) {
        /*
         * At this point, I think we need to get the security settings for each comment.
         * 
         * Rules:
         *  - If you are viewing comments owned by someone the person who owns it is a "mod", (stash)
         *  - If you are an admin then you can all actions for all comments
         *  - If you are viewing general comments, you get actions for your own comments
         */
        $userId = null;
        $ownerId = null;
        
        if ($this -> isLoggedIn()) {
            $userId = $this -> getUserId();    
        }
        
        /*
         * At this point, I could either hardcore checking for stash here or
         * 
         * I could make a comment component and then we would have to tell the
         * JS which URL to go after and then there wouldn't have to be any
         * real hardcoding
         */
        
        //For now
        if($type === 'stash'){
            $stash = $this -> Comment -> User -> Stash -> find("first", array('conditions'=> array('Stash.id'=> $typeID)));
            if(!empty($stash)){
                $ownerId = $stash['Stash']['user_id'];
            }
        }
        
        $comments = $this -> Comment -> getComments($type, $typeID, $userId, $ownerId);
        $this -> set('comments', array('commentsData' => $comments));
    }

}
?>