<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesWishListsController extends AppController
{
    
    public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js');
    public $components = array('Image');
    /**
     * This will handle add, update, delete asynchronously
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
            return;
        }
        
        if ($this->request->isPut()) {
            // Update, not much to do here right now
            
            
        } else if ($this->request->isPost()) {
            $collectible['CollectiblesWishList']['collectible_id'] = Sanitize::clean($id);
            
            $response = $this->CollectiblesWishList->add($collectible, $this->getUser());
            
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
                $this->set('returnData', $response);
            }
        } else if ($this->request->isDelete()) {
            // for now this will handle deletes where the user is prompted
            // about the delete
            // we need to pull the query parameters
            $collectible['CollectiblesWishList'] = array();
            $collectible['CollectiblesWishList']['id'] = $id;
            
            $response = $this->CollectiblesWishList->remove($collectible, $this->getUser());
            if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                $this->response->statusCode(401);
            } else if (!$response['response']['isSuccess'] && $response['response']['code'] === 500) {
                $this->response->statusCode(500);
            }
        } else if ($this->request->isGet()) {
        }
    }
    
    public function collectibles($username = null) {
        $this->set(compact('username'));
        if ($this->request->isGet()) {
            $user = $this->CollectiblesWishList->User->find("first", array('conditions' => array('User.username' => $username), 'contain' => false));
            $this->paginate = array('paramType' => 'querystring', 'limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => array('CollectiblesWishList.user_id' => $user['User']['id']), 'contain' => array('Collectible' => array('User' => array('fields' => array('username')), 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype')));
            $collectibles = $this->paginate('CollectiblesWishList');
            
            $extractUserCollectibles = Set::extract('/CollectiblesWishList/.', $collectibles);
            
            foreach ($extractUserCollectibles as $key => $value) {
                $extractUserCollectibles[$key]['Collectible'] = $collectibles[$key]['Collectible'];
                if (isset($extractUserCollectibles[$key]['Collectible']['CollectiblesUpload'])) {
                    foreach ($extractUserCollectibles[$key]['Collectible']['CollectiblesUpload'] as $uploadKey => $upload) {
                        if ($upload['primary']) {
                            $resizedImg = $this->Image->image($upload['Upload']['name'], array('uploadDir' => 'files', 'width' => 400, 'height' => 400, 'imagePathOnly' => true));
                            $extractUserCollectibles[$key]['Collectible']['CollectiblesUpload'][$uploadKey]['Upload']['thumbnail_url'] = $resizedImg['path'];
                        }
                    }
                }
            }
            
            $this->set('collectibles', $extractUserCollectibles);
            // $this->response->body(json_encode($extractUserCollectibles));
            
            
        }
    }
}
?>