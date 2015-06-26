<?php
class FavoritesController extends AppController {
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function index() {
        $this->autoRender = false;
        debug($this->Favorite->find('first', array('conditions' => array('Favorite.id' => 1))));
        debug($this->Favorite->find('first', array('conditions' => array('Favorite.user_id' => 1))));
        debug($this->Favorite->CollectibleFavorite->find('all', array('conditions' => array('CollectibleFavorite.collectible_id' => '1'), 'joins' => array(array('table' => 'favorites', 'alias' => 'Favorite1', 'type' => 'inner', 'conditions' => array('CollectibleFavorite.favorite_id = Favorite1.id', 'Favorite1.user_id = 2'))))));
        
        $data['Favorite'] = array('user_id' => 2);
        $data['CollectibleFavorite'] = array('collectible_id' => 10);
        if ($this->Favorite->saveAssociated($data, array('validate' => false, 'deep' => true))) {
            $retVal = true;
        }

        debug($this->Favorite->getCollectibleFavorite('4988', 1));
    }
    
    public function favorite() {
        $data = array();
        $this->autoRender = false;
        //must be logged in to post comment
        if (!$this->isLoggedIn()) {
            $this->response->body(__('You do not have permissions to complete this request.'));
            $this->response->statusCode(401);
            return;
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $type = $this->request->data['Favorite']['type'];
            $id = $this->request->data['Favorite']['type_id'];
            $subscribed = $this->request->data['Favorite']['subscribed'];
            $userId = $this->getUserId();
            if ($this->Favorite->addSubscription($id, $type, $userId, $subscribed)) {
                // $subscriptions = $this->getSubscriptions();
                // if ($subscribed === 'true') {
                //     // When you log in, it is pulling in the id of the subscription as the value
                //     // Not sure it really matters
                //     $subscriptions[$entityTypeId] = $this->Subscription->id;
                // }
                // else {
                //     unset($subscriptions[$entityTypeId]);
                // }
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