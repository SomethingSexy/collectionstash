<?php
App::uses('Sanitize', 'Utility');
class StashsController extends AppController
{
    public $name = 'Stashs';
    public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js', 'Time');
    
    public $filters = array(
    
    //
    'm' => array('model' => 'Manufacture', 'multiple' => true, 'id' => 'id', 'user_selectable' => true, 'label' => 'Manufacturer', 'key' => 'title'),
    
    //
    'ct' => array('model' => 'Collectibletype', 'multiple' => true, 'id' => 'id', 'user_selectable' => true, 'label' => 'Platform', 'key' => 'name'),
    
    //
    'l' => array('model' => 'License', 'multiple' => true, 'id' => 'id', 'user_selectable' => true, 'label' => 'Brand', 'key' => 'name'),
    
    //
    's' => array('model' => 'Scale', 'multiple' => true, 'id' => 'id', 'user_selectable' => true, 'label' => 'Scale', 'key' => 'scale'),
    
    //
    'v' => array('model' => 'Collectible', 'multiple' => false, 'id' => 'variant', 'user_selectable' => true, 'label' => 'Variant', 'values' => array(1 => 'Yes', 0 => 'No')),
    
    //
    'o' => array('custom' => true, 'multiple' => false, 'id' => 'order', 'user_selectable' => true, 'label' => 'Order by', 'values' => array('n' => 'Newest', 'o' => 'Oldest', 'a' => 'Ascending', 'd' => 'Descending')));
    
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
    
    public function updateProfileSettings() {
        $this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
        if ($this->isLoggedIn()) {
            if (!empty($this->request->data)) {
                $user = $this->getUser();
                
                $stash = $this->Stash->find("first", array('conditions' => array('Stash.user_id' => $user['User']['id']), 'contain' => false));
                
                $this->Stash->id = $stash['Stash']['id'];
                if (!isset($this->request->data['Stash']['privacy'])) {
                    $this->request->data['Stash']['privacy'] = 0;
                }
                if ($this->Stash->saveField('privacy', $this->request->data['Stash']['privacy'])) {
                    $this->set('aProfileSettings', array('success' => array('isSuccess' => true, 'message' => __('You have successfully updated your settings.', true))));
                } else {
                    $this->set('aProfileSettings', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'errors' => array($this->Stash->validationErrors)));
                }
            } else {
                $this->set('aProfileSettings', array('success' => array('isSuccess' => false), 'isTimeOut' => false, 'message' => array('There was an issue trying to save your settings.')));
            }
        } else {
            $this->set('aProfileSettings', array('success' => array('isSuccess' => false), 'isTimeOut' => true));
        }
    }
    
    private function search($user) {
        $saveSearchFilters = $this->getFiltersFromQuery();
        $tableFilters = $this->processQueryFilters($saveSearchFilters);
        debug($saveSearchFilters);
        debug($tableFilters);
        
        $joins = array();
        array_push($joins, array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"')));
        array_push($joins, array('table' => 'collectibles', 'alias' => 'Collectible2', 'type' => 'inner', 'conditions' => array('Collectible2.id = CollectiblesUser.collectible_id')));
        array_push($joins, array('table' => 'manufactures', 'alias' => 'Manufacture', 'type' => 'inner', 'conditions' => array('Collectible2.manufacture_id = Manufacture.id')));
        array_push($joins, array('table' => 'licenses', 'alias' => 'License', 'type' => 'inner', 'conditions' => array('Collectible2.license_id = License.id')));
        
        $conditions = array('CollectiblesUser.active' => true, 'CollectiblesUser.user_id' => $user['User']['id']);
        array_push($conditions, $tableFilters);
        
        // Be very careful when changing this contains, it is tied to the type
        $this->paginate = array('findType' => 'orderAveragePrice', 'joins' => $joins, 'limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => $conditions, 'contain' => array('Condition', 'Merchant', 'Collectible' => array('User', 'CollectiblePriceFact', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype', 'ArtistsCollectible' => array('Artist'))));
        return $this->paginate('CollectiblesUser');
    }
    protected function getFilters($userId) {
        
        // grab the default filers
        $filters = $this->filters;
        
        $values = $this->Stash->getFilters($userId);
        
        $filters['m']['values'] = $values['m']['values'];
        $filters['ct']['values'] = $values['ct']['values'];
        $filters['l']['values'] = $values['l']['values'];
        $filters['s']['values'] = $values['s']['values'];
        
        return $filters;
    }
    
    public function view($userId = null, $type = 'tile') {
        $this->layout = 'fluid';
        $this->set('stashType', 'default');
        if (!is_null($userId)) {
            $userId = Sanitize::clean($userId, array('encode' => false));
            $user = $this->Stash->User->find("first", array('conditions' => array('User.username' => $userId), 'contain' => array('Stash')));
            
            //Ok we have a user, although this seems kind of inefficent but it works for now
            if (!empty($user)) {
                if (!empty($user['Stash'])) {
                    $loggedInUser = $this->getUser();
                    $viewingMyStash = false;
                    if ($loggedInUser['User']['id'] === $user['User']['id']) {
                        $viewingMyStash = true;
                    }
                    $this->set('myStash', $viewingMyStash);
                    $this->set('stashUsername', $userId);
                    
                    //If the privacy is 0 or you are viewing your own stash then always show
                    //or if it is set to 1 and this person is logged in also show.
                    if ($user['Stash'][0]['privacy'] === '0' || $viewingMyStash || ($user['Stash'][0]['privacy'] === '1' && $this->isLoggedIn())) {
                        $collectibles = $this->search($user);
                        
                        $this->set(compact('collectibles'));
                        $this->set('stash', $user['Stash'][0]);
                        
                        $reasons = $this->Stash->CollectiblesUser->CollectibleUserRemoveReason->find('all', array('contain' => false));
                        $this->set(compact('reasons'));
                        
                        $this->set('filters', $this->getFilters($user['User']['id']));
                        
                        // This will us the standard view
                        if ($type === 'list') {
                            $this->render('view_list');
                        } else {
                            $this->render('view_v2');
                        }
                    } else {
                        $this->render('view_private');
                        return;
                    }
                } else {
                    
                    //This is a fucking error
                    $this->redirect('/', null, true);
                }
            } else {
                $this->render('view_no_exist');
                return;
            }
        } else {
            
            //$this -> redirect('/', null, true);
            
            
        }
    }
    
    /**
     * WTF is this doing?
     */
    public function comments($userId = null) {
        $this->layout = 'fluid';
        if (!is_null($userId)) {
            $userId = Sanitize::clean($userId, array('encode' => false));
            
            //Also retrieve the UserUploads at this point, so we do not have to do it later and comments
            $user = $this->Stash->User->find("first", array('conditions' => array('User.username' => $userId), 'contain' => array('Stash')));
            
            //Ok we have a user, although this seems kind of inefficent but it works for now
            if (!empty($user)) {
                if (!empty($user['Stash'])) {
                    $loggedInUser = $this->getUser();
                    $viewingMyStash = false;
                    if ($loggedInUser['User']['id'] === $user['User']['id']) {
                        $viewingMyStash = true;
                    }
                    $this->set('myStash', $viewingMyStash);
                    $this->set('stashUsername', $userId);
                    
                    //If the privacy is 0 or you are viewing your own stash then always show
                    //or if it is set to 1 and this person is logged in also show.
                    if ($user['Stash'][0]['privacy'] === '0' || $viewingMyStash || ($user['Stash'][0]['privacy'] === '1' && $this->isLoggedIn())) {
                        $this->set('stash', $user['Stash'][0]);
                    } else {
                        $this->render('view_private');
                        return;
                    }
                } else {
                    
                    //This is a fucking error
                    $this->redirect('/', null, true);
                }
            } else {
                $this->render('view_no_exist');
                return;
            }
        } else {
            $this->redirect('/', null, true);
        }
    }
    
    public function history($userId = null) {
        $this->layout = 'fluid';
        if (!is_null($userId)) {
            $userId = Sanitize::clean($userId, array('encode' => false));
            
            //Also retrieve the UserUploads at this point, so we do not have to do it later and comments
            $user = $this->Stash->User->find("first", array('conditions' => array('User.username' => $userId), 'contain' => array('Stash')));
            
            //Ok we have a user, although this seems kind of inefficent but it works for now
            if (!empty($user)) {
                if (!empty($user['Stash'])) {
                    $loggedInUser = $this->getUser();
                    $viewingMyStash = false;
                    if ($loggedInUser['User']['id'] === $user['User']['id']) {
                        $viewingMyStash = true;
                    }
                    $this->set('myStash', $viewingMyStash);
                    $this->set('stashUsername', $userId);
                    
                    //If the privacy is 0 or you are viewing your own stash then always show
                    //or if it is set to 1 and this person is logged in also show.
                    if ($user['Stash'][0]['privacy'] === '0' || $viewingMyStash || ($user['Stash'][0]['privacy'] === '1' && $this->isLoggedIn())) {
                        $graphData = $this->Stash->getStashGraphHistory($user);
                        $this->set(compact('graphData'));
                        $this->set('stash', $user['Stash'][0]);
                        $this->paginate = array('findType' => 'orderAveragePrice', 'joins' => array(array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'limit' => 25, 'conditions' => array('CollectiblesUser.user_id' => $user['User']['id']), 'contain' => array('Listing' => array('Transaction'), 'Condition', 'Merchant', 'Collectible' => array('User', 'CollectiblePriceFact', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype', 'ArtistsCollectible' => array('Artist'))));
                        $collectibles = $this->paginate('CollectiblesUser');
                        $this->set(compact('collectibles'));
                        $reasons = $this->Stash->CollectiblesUser->CollectibleUserRemoveReason->find('all', array('contain' => false));
                        $this->set(compact('reasons'));
                    } else {
                        $this->render('view_private');
                        return;
                    }
                } else {
                    
                    //This is a fucking error
                    $this->redirect('/', null, true);
                }
            } else {
                $this->render('view_no_exist');
                return;
            }
        } else {
            $this->redirect('/', null, true);
        }
    }
}
?>