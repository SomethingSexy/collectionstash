<?php
class FavoritesController extends AppController {
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function index() {
        $this->autoRender = false;
        debug($this->Favorite->find('first', array('conditions' => array('Favorite.id' => 1))));
        debug($this->Favorite->find('first', array('conditions' => array('Favorite.user_id' => 1))));
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
            $type = $this->request->data['Subscription']['type'];
            $id = $this->request->data['Subscription']['id'];
            $subscribed = $this->request->data['Subscription']['subscribed'];
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
                $this->response->statusCode(200);
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