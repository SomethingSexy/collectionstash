<?php
class FavoritesController extends AppController {
    
    public $helpers = array('Html', 'Js', 'Minify', 'FileUpload.FileUpload');
    public $components = array('Image');
    // public function index() {
    //     $this->autoRender = false;
    //     debug($this->Favorite->find('first', array('conditions' => array('Favorite.id' => 1))));
    //     debug($this->Favorite->find('first', array('conditions' => array('Favorite.user_id' => 1))));
    //     debug($this->Favorite->CollectibleFavorite->find('all', array('conditions' => array('CollectibleFavorite.collectible_id' => '1'), 'joins' => array(array('table' => 'favorites', 'alias' => 'Favorite1', 'type' => 'inner', 'conditions' => array('CollectibleFavorite.favorite_id = Favorite1.id', 'Favorite1.user_id = 2'))))));
    
    //     $data['Favorite'] = array('user_id' => 2);
    //     $data['CollectibleFavorite'] = array('collectible_id' => 10);
    //     if ($this->Favorite->saveAssociated($data, array('validate' => false, 'deep' => true))) {
    //         $retVal = true;
    //     }
    
    //     debug($this->Favorite->getCollectibleFavorite('4988', 1));
    // }
    
    
    /**
     * API to return a list of favorites per user.  This will be paginated.
     */
    public function index($username = null) {
        $this->set(compact('username'));
        if ($this->request->isGet()) {
            $user = $this->Favorite->User->find("first", array('conditions' => array('User.username' => $username), 'contain' => false));
            $this->paginate = array('paramType' => 'querystring', 'limit' => 25, 'contain' => array('CollectibleFavorite' => array('Collectible' => array('CollectiblesUpload' => array('Upload')))), 'conditions' => array('Favorite.user_id' => $user['User']['id']));
            $favorites = $this->paginate('Favorite');
            
            foreach ($favorites as $key => $value) {
                if (isset($value['CollectibleFavorite'])) {
                    if (!empty($value['CollectibleFavorite']['Collectible']['CollectiblesUpload'])) {
                        foreach ($value['CollectibleFavorite']['Collectible']['CollectiblesUpload'] as $uploadKey => $upload) {
                            $img = $this->Image->image($upload['Upload']['name'], array('uploadDir' => Configure::read('Settings.Collectible.upload-directory'), 'imagePathOnly' => true));
                            $resizedImg = $this->Image->image($upload['Upload']['name'], array('uploadDir' => Configure::read('Settings.Collectible.upload-directory'), 'width' => 200, 'height' => 200, 'imagePathOnly' => true, 'resizeType' => 'adaptive'));
                            $favorites[$key]['CollectibleFavorite']['Collectible']['CollectiblesUpload'][$uploadKey]['Upload']['imagePath'] = $img['path'];
                            $favorites[$key]['CollectibleFavorite']['Collectible']['CollectiblesUpload'][$uploadKey]['Upload']['resizedImagePath'] = $resizedImg['path'];
                        }
                    }
                }
            }
            
            $extractFavorites = Set::extract('/Favorite/.', $favorites);
            foreach ($extractFavorites as $key => $value) {
                if (isset($favorites[$key]['CollectibleFavorite'])) {
                    $extractFavorites[$key]['CollectibleFavorite'] = $favorites[$key]['CollectibleFavorite'];
                } 
                else if (isset($favorites[$key]['UserFavorite'])) {
                    $extractFavorites[$key]['UserFavorite'] = $favorites[$key]['UserFavorite'];
                }
            }
            
            $this->set('favorites', $extractFavorites);
        } 
        else {
            $this->response->body(__('Invalid request.'));
            $this->response->statusCode(500);
        }
    }
    
    public function favorite($id = null) {
        $data = array();
        $this->autoRender = false;
        //must be logged in to post comment
        if (!$this->isLoggedIn()) {
            $this->response->body(__('You do not have permissions to complete this request.'));
            $this->response->statusCode(401);
            return;
        }
        $userId = $this->getUserId();
        // legacy-ish stuff post/put is also removing
        if ($this->request->is('post') || $this->request->is('put')) {
            $type = $this->request->data['Favorite']['type'];
            $id = $this->request->data['Favorite']['type_id'];
            $subscribed = $this->request->data['Favorite']['subscribed'];
            $subscribed = ($subscribed === 'true');
            
            if ($this->Favorite->addSubscription($id, $type, $userId, $subscribed)) {
                // reset the favorites in the session
                $favorites = $this->Favorite->getFavorites($userId);
                $this->Session->write('favorites', $favorites);
                $this->response->statusCode(200);
                $this->response->body('{}');
                return;
            } 
            else {
                $this->response->statusCode(400);
                $this->response->body('{}');
                return;
            }
        } 
        else if ($this->request->is('delete')) {
            if ($this->Favorite->removeFavorite($id, $userId)) {
                // reset the favorites in the session
                $favorites = $this->Favorite->getFavorites($userId);
                $this->Session->write('favorites', $favorites);
                $this->response->statusCode(200);
                $this->response->body('{}');
                return;
            } 
            else {
                $this->response->statusCode(400);
                $this->response->body('{}');
                return;
            }
        } 
        else {
            $this->response->body(__('Invalid request.'));
            $this->response->statusCode(500);
            return;
        }
    }
}
?>