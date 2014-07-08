<?php
App::uses('Sanitize', 'Utility');
class StashsController extends AppController
{
    public $name = 'Stashs';
    public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js', 'Time');
    public $components = array('StashSearch');
    /*
     * This action will be used to allow the user to view/edit their stash.  Individual collectible edits will happen in
     * the ColletiblesUsers controller.  This will be the main launching point.  Although one could argue that this
     * should go in the CollectiblesUsers controller.
     *
     * Right now, I am not keying this by Stash, if I ever get back into multiple stashes this will have to be updated.
    */
    public function edit() {
        //Since we are making sure they are logged in, there should always be a user
        $this->checkLogIn();
        $user = $this->getUser();
        
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data = Sanitize::clean($this->request->data);
            
            if ($this->Stash->CollectiblesUser->saveMany($this->request->data['CollectiblesUser'], array('fieldList' => array('sort_number'), 'callbacks' => false))) {
                $this->Session->setFlash(__('Your sort was successfully saved.', true), null, null, 'success');
            } else {
                $this->Session->setFlash(__('There was a problem saving your sort.', true), null, null, 'error');
            }
        }
        //Ok we have a user, although this seems kind of inefficent but it works for now
        $this->set('myStash', true);
        $this->set('stashUsername', $user['User']['username']);
        
        $collectibles = $this->Stash->CollectiblesUser->find("all", array('joins' => array(array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'order' => array('sort_number' => 'desc'), 'conditions' => array('CollectiblesUser.active' => true, 'CollectiblesUser.user_id' => $user['User']['id']), 'contain' => array('Condition', 'Merchant', 'Collectible' => array('User', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype'))));
        
        $this->set(compact('collectibles'));
    }
    
    public function profile() {
        $this->autoRender = false;
        // need to be logged in
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        // create
        if ($this->request->isPost()) {
        } else if ($this->request->isPut()) {
            // update
            $profile = $this->request->input('json_decode', true);
            $profile = Sanitize::clean($profile);
            $user = $this->getUser();
            
            $stash = $this->Stash->find("first", array('conditions' => array('Stash.user_id' => $user['User']['id']), 'contain' => false));
            
            $this->Stash->id = $stash['Stash']['id'];
            if (!isset($profile['privacy'])) {
                $profile['privacy'] = 0;
            }
            if (!$this->Stash->saveField('privacy', $profile['privacy'])) {
                $this->response->statusCode(400);
            } else {
                // this needs to be here otherwise backbone doesn't process the response correctly
                $this->response->body('{}');
            }
        } else if ($this->request->isDelete()) {
        }
    }
}
?>