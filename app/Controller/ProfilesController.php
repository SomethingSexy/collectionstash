<?php
App::uses('Sanitize', 'Utility');

//TODO this could get renamed account
class ProfilesController extends AppController
{
    
    public $helpers = array('Html', 'Js', 'FileUpload.FileUpload', 'Minify');
    
    public function index() {
        $this->checkLogIn();
        $this->layout = 'require';
        $this->loadModel('Stash');
        
        $stashProfileSettings = $this->Stash->getProfileSettings($this->getUser());
        debug($stashProfileSettings);
        $this->set(compact('stashProfileSettings'));
        
        $profile = $this->Profile->find('first', array('conditions' => array('Profile.user_id' => $this->getUserId()), 'contain' => false));
        debug($profile);
        
        $user = $this->Profile->User->find('first', array('conditions' => array('User.id' => $this->getUserId()), 'contain' => false));
        
        // build model data
        $profile = array('id' => $user['User']['id'], 'first_name' => $user['User']['first_name'], 'last_name' => $user['User']['last_name'], 'email' => $user['User']['email'], 'email_notification' => $profile['Profile']['email_notification'], 'email_newsletter' => $profile['Profile']['email_newsletter']);
        
        $this->set(compact('profile'));
    }
    
    public function profile($id = null) {
        $this->autoRender = false;
        
        // need to be logged in
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        
        // create
        if ($this->request->isPost()) {
            
            // do nothing for now
            
        } else if ($this->request->isPut()) {
            
            // update
            $profile = $this->request->input('json_decode', true);
            
            // no need to clean for now on the update
            $profile = Sanitize::clean($profile);
            
            $response = $this->Profile->updateProfile($profile, $this->getUser());
            
            // $this->set('returnData', $response);
            
            
        } else if ($this->request->isDelete()) {
        }
    }
}
?>