<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesUsersController extends AppController
{
    
    public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js');
    public $components = array('StashSearch');
    // public function balls() {
    //     $this->autoRender = false;
    //     $joins = array();
    //     array_push($joins, array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"')));
    //     array_push($joins, array('table' => 'collectibles', 'alias' => 'Collectible', 'type' => 'inner', 'conditions' => array('Collectible.id = CollectiblesUser.collectible_id')));
    //     array_push($joins, array('table' => 'manufactures', 'alias' => 'Manufacture', 'type' => 'inner', 'conditions' => array('Collectible.manufacture_id = Manufacture.id')));
    
    //     $this->paginate = array('joins' => $joins, 'limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => array('Manufacture.id' => 2, 'CollectiblesUser.active' => true, 'CollectiblesUser.user_id' => 2), 'contain' => array('Condition', 'Merchant'));
    //     $collectibles = $this->paginate('CollectiblesUser');
    //     debug(count($collectibles));
    // }
    
    
    /**
     * This is for viewing an collectible in a user's stash
     */
    public function view($id = null) {
        if (!is_null($id) && is_numeric($id)) {
            $id = Sanitize::clean($id, array('encode' => false));
            //TODO should be more model behavior but whateves
            //First lets grab the collectible user
            $collectiblesUser = $this->CollectiblesUser->getUserCollectible($id);
            debug($collectiblesUser);
            if (isset($collectiblesUser) && !empty($collectiblesUser)) {
                //First see if the person viewing this collectible is logged in
                $this->set('stashUsername', $collectiblesUser['User']['username']);
                $viewMyCollectible = false;
                if ($this->isLoggedIn()) {
                    //If they are logged in, check to see if the user ids match up
                    if ($this->getUserId() === $collectiblesUser['User']['id']) {
                        $viewMyCollectible = true;
                        $reasons = $this->CollectiblesUser->CollectibleUserRemoveReason->find('all', array('contain' => false));
                        $this->set(compact('reasons'));
                    }
                }
                $this->set(compact('viewMyCollectible'));
                if ($collectiblesUser['Stash']['privacy'] === '0' || $viewMyCollectible) {
                    //You are looking at your collectible, well then BAM, show that shit
                    $this->set('collectible', $collectiblesUser);
                } else {
                    $this->render('viewPrivate');
                    return;
                }
            } else {
                $this->Session->setFlash(__('Invalid collectible', true));
                $this->redirect($this->referer());
            }
        } else {
            $this->Session->setFlash(__('Invalid collectible', true));
            $this->redirect($this->referer());
        }
    }
    
    public function collectibles($username = null) {
        if ($this->request->isGet()) {
            $user = $this->CollectiblesUser->User->find("first", array('conditions' => array('User.username' => $username), 'contain' => false));
            $collectibles = $this->StashSearch->search($user);
            debug($collectibles);
            
            $this->set(compact('collectibles'));
        }
    }
    /**
     * This will handle add, update, delete asynchronously of a single collectible
     */
    public function collectible($id = null) {
        
        if (!$this->isLoggedIn()) {
            $data['response'] = array();
            $data['response']['isSuccess'] = false;
            $error = array('message' => __('You must be logged in to add a collectible.'));
            $error['inline'] = false;
            $data['response']['errors'] = array();
            array_push($data['response']['errors'], $error);
            $this->set('returnData', $data);
            $this->response->statusCode(401);
            return;
        }
        
        if ($this->request->isPut()) {
            // this will handle updating for sale and future changes when moving update to modal
            $collectible['CollectiblesUser'] = $this->request->input('json_decode', true);
            $collectible['CollectiblesUser'] = Sanitize::clean($collectible['CollectiblesUser']);
            $response = $this->CollectiblesUser->update($collectible, $this->getUser());
            if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                $this->response->statusCode(401);
            } else if (!$response['response']['isSuccess'] && $response['response']['code'] === 500) {
                $this->response->statusCode(500);
            }
            $this->set('returnData', $response);
        } else if ($this->request->isPost()) {
            $collectible['CollectiblesUser'] = $this->request->input('json_decode', true);
            $collectible['CollectiblesUser'] = Sanitize::clean($collectible['CollectiblesUser']);
            
            $response = $this->CollectiblesUser->add($collectible, $this->getUser());
            
            $this->set('returnData', $response);
        } else if ($this->request->isDelete()) {
            // for now this will handle deletes where the user is prompted
            // about the delete
            // we need to pull the query parameters
            //TODO: the controller probably should know about this data
            $collectible['CollectiblesUser'] = array();
            $collectible['CollectiblesUser']['id'] = $id;
            $collectible['CollectiblesUser']['collectible_user_remove_reason_id'] = Sanitize::clean($this->request->query('collectible_user_remove_reason_id'));
            $collectible['CollectiblesUser']['sold_cost'] = Sanitize::clean($this->request->query('sold_cost'));
            $collectible['CollectiblesUser']['traded_for'] = Sanitize::clean($this->request->query('traded_for'));
            $collectible['CollectiblesUser']['remove_date'] = Sanitize::clean($this->request->query('remove_date'));
            
            $response = $this->CollectiblesUser->remove($collectible, $this->getUser());
            if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                $this->response->statusCode(401);
            } else if (!$response['response']['isSuccess'] && $response['response']['code'] === 500) {
                $this->response->statusCode(500);
            }
            
            $this->set('returnData', $response);
        } else if ($this->request->isGet()) {
        }
    }
    /**
     * This method edits a user's collectible, via standard post
     */
    function edit($id = null) {
        $this->checkLogIn();
        
        $collectiblesUser = $this->CollectiblesUser->find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('User', 'Listing' => array('Transaction'), 'Merchant', 'Collectible' => array('Currency'))));
        
        if (isset($collectiblesUser) && !empty($collectiblesUser)) {
            $loggedInUserId = $this->getUserId();
            if ($loggedInUserId === $collectiblesUser['CollectiblesUser']['user_id']) {
                
                if (!empty($this->request->data)) {
                    $this->request->data = Sanitize::clean($this->request->data);
                    $response = $this->CollectiblesUser->update($this->request->data, $this->getUser());
                    
                    if ($response['response']['isSuccess']) {
                        $this->Session->setFlash(__('Your collectible was successfully updated.', true), null, null, 'success');
                        $this->redirect(array('controller' => 'collectibles_users', 'action' => 'view', $id), null, true);
                    } else {
                        $this->Session->setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
                    }
                } else {
                    $this->request->data = $collectiblesUser;
                }
                $this->set('collectible', $collectiblesUser);
                $this->set('conditions', $this->CollectiblesUser->Condition->find('list', array('order' => 'name')));
            } else {
                $this->Session->setFlash(__('Invalid access', true), null, null, 'error');
                $this->redirect('/', null, true);
                return;
            }
        } else {
            $this->Session->setFlash(__('Invalid collectible', true), null, null, 'error');
            $this->redirect($this->referer(), null, true);
            return;
        }
    }
    // I might maintain 2 add functions because they will do different things
    // Quick add will be for when you are adding something without entering any
    // information or you are adding to your wishlist.
    public function quickAdd($id) {
        $data = array();
        $data['response'] = array();
        $data['response']['isSuccess'] = false;
        $data['response']['errors'] = array();
        //must be logged in to post comment
        if (!$this->isLoggedIn()) {
            $error = array('message' => __('You must be logged in to add an item.'));
            $error['inline'] = false;
            
            array_push($data['response']['errors'], $error);
            $this->set('returnData', $data);
            return;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $id = Sanitize::clean($id);
            $type = Sanitize::clean($type);
            $collectiblesUser = array();
            $collectiblesUser['CollectiblesUser']['collectible_id'] = $id;
            $response = $this->CollectiblesUser->add($collectiblesUser, $this->getUser());
            
            if ($response) {
                $this->set('returnData', $response);
            } else {
                //Something really fucked up
                $data['response']['isSuccess'] = false;
                array_push($data['response']['errors'], array('message' => __('Invalid request.')));
                $this->set('returnData', $data);
            }
        } else {
            $data['response']['isSuccess'] = false;
            array_push($data['response']['errors'], array('message' => __('Invalid request.')));
            $this->set('returnData', $data);
            return;
        }
    }
    
    public function sale($id = null) {
        $this->layout = 'fluid';
        if (is_null($id)) {
            $this->redirect('/', null, true);
            return;
        }
        $id = Sanitize::clean($id, array('encode' => false));
        $user = $this->CollectiblesUser->User->find("first", array('conditions' => array('User.username' => $id), 'contain' => array('Stash')));
        //Ok we have a user, although this seems kind of inefficent but it works for now
        if (empty($user)) {
            $this->render('view_no_exist');
            return;
        }
        if (empty($user['Stash'])) {
            //This is a fucking error
            $this->redirect('/', null, true);
            return;
        }
        
        $loggedInUser = $this->getUser();
        $viewingMyStash = false;
        if ($loggedInUser['User']['id'] === $user['User']['id']) {
            $viewingMyStash = true;
        }
        $this->set('myStash', $viewingMyStash);
        $this->set('stashUsername', $id);
        //If the privacy is 0 or you are viewing your own stash then always show
        //or if it is set to 1 and this person is logged in also show.
        if ($user['Stash'][0]['privacy'] === '0' || $viewingMyStash || ($user['Stash'][0]['privacy'] === '1' && $this->isLoggedIn())) {
            // Be very careful when changing this contains, it is tied to the type
            $this->paginate = array('findType' => 'orderAveragePrice', 'joins' => array(array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id'))), 'limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => array('CollectiblesUser.active' => true, 'CollectiblesUser.sale' => true, 'CollectiblesUser.user_id' => $user['User']['id']), 'contain' => array('Listing' => array('Transaction'), 'Condition', 'Merchant', 'Collectible' => array('User', 'CollectiblePriceFact', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype', 'ArtistsCollectible' => array('Artist'))));
            $collectibles = $this->paginate('CollectiblesUser');
            $this->set(compact('collectibles'));
            // grab these for display purposes
            $reasons = $this->CollectiblesUser->CollectibleUserRemoveReason->find('all', array('contain' => false));
            $this->set(compact('reasons'));
        } else {
            $this->render('view_private');
            return;
        }
    }
    // TODO: This should get moved to the CollectibleController
    public function registry($id = null) {
        if (!is_null($id) && is_numeric($id)) {
            $collectible = $this->CollectiblesUser->Collectible->find("first", array('conditions' => array('Collectible.id' => $id), 'contain' => false));
            if (!empty($collectible)) {
                $usersWho = $this->CollectiblesUser->getListOfUsersWho($id, $collectible['Collectible']['numbered']);
                
                $wishlist = $this->CollectiblesUser->User->WishList->getListOfUserWishlist($id);
                
                $this->set('showEditionSize', $collectible['Collectible']['numbered']);
                $this->set('registry', $usersWho);
                
                $this->set('wishlist', $wishlist);
            } else {
                $this->redirect("/", null, true);
            }
        } else {
            $this->redirect("/", null, true);
        }
    }
    
    public function beforeRender() {
    }
}
?>