<?php
App::uses('CakeEventListener', 'Event');
App::uses('EntityType', 'Model');
App::uses('Favorite', 'Model');
/**
 * I might be able to put this into one Event called notify :)
 *
 * If this takes off and starts to be slow, it might be better to write all events
 * to a file or in memory and then have a cronjob run every so often to process
 */
class EntityChangeEventListener implements CakeEventListener {
    
    public function implementedEvents() {
        return array('Controller.Stash.Collectible.add' => 'collectibleAddedToStash', 'Controller.WishList.Collectible.add' => 'collectibleAddedToWishList', 'Controller.Comment.add' => 'commentAdded', 'Controller.Collectible.approve' => 'collectibleApprove', 'Controller.Collectible.deny' => 'collectibleDeny');
    }
    /**
     * This will handle whenever a comment is added to an entity
     */
    public function commentAdded($event) {
        //This is the entity type id
        $entityTypeId = $event->data['entityTypeId'];
        //this is the id of the user who posted the comment
        $userId = $event->data['userId'];
        $commentId = $event->data['commentId'];
        // $event->subject->loadModel('Subscription');
        
        //This will also return the model that the entity is for
        $EntityType = new EntityType();
        $entityType = $EntityType->getEntityCore($entityTypeId);
        $type = $entityType['EntityType']['type'];
        // since we know we are adding a comment, get that information
        $comment = $event->subject->Comment->find('first', array('conditions' => array('Comment.id' => $commentId), 'contain' => array('User')));
        $templateData = json_encode($comment);
        
        if ($type === 'stash') {
            // if the user that posted the comment is not the same as the stash it was posted to...notify the owner of the stash
            if ($entityType['Stash']['User']['id'] !== $userId) {
                $message = __('The following new comment has been posted to your <a href="http://' . env('SERVER_NAME') . '/stash/' . $entityType['Stash']['User']['username'] . '">Stash</a>.');
                $subscription = $this->buildSubscription($entityType['Stash']['User']['id'], $message, __('A new coment has been posted.'), 'comment_add', $templateData);
                CakeEventManager::instance()->dispatch(new CakeEvent('Controller.Subscription.notify', $event->subject, array('subscriptions' => array($subscription))));
            }
            //now we need to check to see if this stash has any favorites (really user favorites)
            $favoriteModel = new Favorite();
            $favorites = $favoriteModel->UserFavorite->find('all', array('conditions' => array('UserFavorite.user_id' => $entityType['Stash']['User']['id'])));
            $subscriptions = array();
            foreach ($favorites as $key => $value) {
                // no need to check against yourself, since you cannot favorite yourself
                $message = __('The following new comment has been posted to <a href="http://' . env('SERVER_NAME') . '/stash/' . $entityType['Stash']['User']['username'] . '">Stash</a>.');
                $subscription = $this->buildSubscription($value['Favorite']['user_id'], $message, __('A new coment has been posted.'), 'comment_add', $templateData);
                array_push($subscriptions, $subscription);
            }
            
            if (!empty($subscriptions)) {
                CakeEventManager::instance()->dispatch(new CakeEvent('Controller.Subscription.notify', $event->subject, array('subscriptions' => $subscriptions)));
            }
        } 
        else if ($type === 'collectible') {
            $favoriteModel = new Favorite();
            $favorites = $favoriteModel->CollectibleFavorite->find('all', array('conditions' => array('CollectibleFavorite.collectible_id' => $entityType['Collectible']['id'])));
            $subscriptions = array();
            foreach ($favorites as $key => $value) {
                // since you can favorite the collectible, make sure you don't emailed for your own post
                if ($value['Favorite']['user_id'] !== $userId) {
                    $message = __('The following new comment has been posted to the collectible <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $entityType['Collectible']['id'] . '">' . $entityType['Collectible']['name'] . '</a>' . '.');
                    $subscription = $this->buildSubscription($value['Favorite']['user_id'], $message, __('A new coment has been posted.'), 'comment_add', $templateData);
                    array_push($subscriptions, $subscription);
                }
            }
            
            if (!empty($subscriptions)) {
                CakeEventManager::instance()->dispatch(new CakeEvent('Controller.Subscription.notify', $event->subject, array('subscriptions' => $subscriptions)));
            }
        }
    }
    /**
     * This executes whenever a collectible as been added to someone's stash
     */
    public function collectibleAddedToStash($event) {
        //This is the id of the collectibleuser that was added
        $id = $event->subject->id;
        
        $stashId = $event->data['stashId'];
        $collectibleUserId = $event->data['collectibleUserId'];
        // Grab the stash
        $stash = $event->subject->User->Stash->find("first", array('contain' => array('User'), 'conditions' => array('Stash.id' => $stashId)));
        
        $collectibleUser = $event->subject->find('first', array('conditions' => array('CollectiblesUser.id' => $collectibleUserId), 'contain' => array('Condition', 'Merchant', 'Collectible' => array('Collectibletype', 'Manufacture', 'ArtistsCollectible' => array('Artist'), 'CollectiblesUpload' => array('Upload')))));
        $templateData = json_encode($collectibleUser);

        $favoriteModel = new Favorite();
        $favorites = $favoriteModel->UserFavorite->find('all', array('conditions' => array('UserFavorite.user_id' => $stash['User']['id'])));
        $subscriptions = array();
        foreach ($favorites as $key => $value) {
            // no need to check against yourself, since you cannot favorite yourself
            $message = __('The following new comment has been posted to <a href="http://' . env('SERVER_NAME') . '/stash/' . $stash['User']['username'] . '">Stash</a>.');
            $subscription = $this->buildSubscription($value['Favorite']['user_id'], $message, __('A new coment has been posted.'), 'comment_add', $templateData);
            array_push($subscriptions, $subscription);
        }
        
        if (!empty($subscriptions)) {
            CakeEventManager::instance()->dispatch(new CakeEvent('Model.Subscription.notify', $event->subject, array('subscriptions' => $subscriptions)));
        }
    }
    /**
     * TODO: This doesn't really work.  We need to update the subscription/follower stuff then we can get this to work
     */
    public function collectibleAddedToWishList($event) {
        // $event->subject = the object the event was dispatched from
        // in this example $event->subject = BlogController
        
        //This is the id of the collectibleuser that was added
        $id = $event->subject->id;
        
        $wishListId = $event->data['wishListId'];
        $collectibleUserId = $event->data['collectibleWishListId'];
        // Grab the stash
        $wishList = $event->subject->WishList->find("first", array('contain' => array('User'), 'conditions' => array('WishList.id' => $wishListId)));
        
        $collectibleUser = $event->subject->find('first', array('conditions' => array('CollectiblesWishList.id' => $collectibleUserId), 'contain' => array('Collectible' => array('Collectibletype', 'Manufacture', 'ArtistsCollectible' => array('Artist'), 'CollectiblesUpload' => array('Upload')))));
        $templateData = json_encode($collectibleUser);
        // TODO: Update with favorite system
        //now grab all of the Subscriptions
        // $subscriptions = $event->subject->User->Subscription->find("all", array('contain' => array('User'), 'conditions' => array('Subscription.subscribed' => '1', 'Subscription.entity_type_id' => $wishList['Stash']['entity_type_id'])));
        
        // //Build the message
        // $message = $wishList['User']['username'];
        // $message.= __(' has added the following collectible to their <a href="http://' . env('SERVER_NAME') . '/wishlist/' . $wishList['User']['username'] . '">Wish List</a>.');
        
        // foreach ($subscriptions as $key => $subscription) {
        
        //     //If the subscription is the same as the owner of the stash, unset it
        //     if ($subscription['Subscription']['user_id'] === $wishList['WishList']['user_id']) {
        //         unset($subscriptions[$key]);
        //     }
        //     else {
        //         $subscriptions[$key]['Subscription']['message'] = $message;
        //         $subscriptions[$key]['Subscription']['subject'] = __($wishList['User']['username'] . ' updated their wish list.');
        //         $subscriptions[$key]['Subscription']['notification_type'] = 'wishlist_add';
        //         $subscriptions[$key]['Subscription']['notification_json_data'] = $templateData;
        //     }
        // }
        
        // if (!empty($subscriptions)) {
        //     CakeEventManager::instance()->dispatch(new CakeEvent('Model.Subscription.notify', $event->subject, array('subscriptions' => $subscriptions)));
        // }
        // else {
        //     CakeLog::write('info', 'No subscriptions');
        // }
        
        
    }
    /**
     * This won't be specific to the Entity stuff but I want to keep all of this logic
     * in the same place
     */
    public function collectibleApprove($event) {
        $userId = $event->data['userId'];
        $collectileId = $event->data['collectileId'];
        $notes = $event->data['notes'];
        
        $collectible = $event->subject->Collectible->find('first', array('conditions' => array('Collectible.id' => $collectileId), 'contains' => array('Collectibletype', 'Manufacture', 'ArtistsCollectible' => array('Artist'))));
        $templateData = json_encode($collectible);
        
        $message = 'We have approved <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectible['Collectible']['id'] . '">' . $collectible['Collectible']['displayTitle'] . '</a> you submitted to <a href="http://' . env('SERVER_NAME') . '">Collection Stash</a>.';
        
        $subject = __('Your submission of ' . $collectible['Collectible']['displayTitle'] . ' has been successfully approved!');
        
        $subscriptions = array();
        $subscription = array();
        $subscription['Subscription']['user_id'] = $userId;
        $subscription['Subscription']['message'] = $message;
        $subscription['Subscription']['subject'] = $subject;
        $subscription['Subscription']['notification_type'] = 'add_approval';
        $subscription['Subscription']['notification_json_data'] = $templateData;
        array_push($subscriptions, $subscription);
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Controller.Subscription.notify', $event->subject, array('subscriptions' => $subscriptions)));
    }
    
    public function collectibleDeny($event) {
        $userId = $event->data['userId'];
        $collectileId = $event->data['collectileId'];
        $collectible = $event->data['collectible'];
        $notes = $event->data['notes'];
        
        $message = 'We have denied ' . $collectible['Collectible']['displayTitle'] . '</a> you submitted to <a href="http://' . env('SERVER_NAME') . '">Collection Stash</a>.';
        
        if (isset($notes) && !empty($notes)) {
            $message.= 'The reason the submission was denied:' . $notes;
        }
        $subject = __('Oh no! Your submission of ' . $collectible['Collectible']['displayTitle'] . ' has been denied.');
        
        $subscriptions = array();
        $subscription = array();
        $subscription['Subscription']['user_id'] = $userId;
        $subscription['Subscription']['message'] = $message;
        $subscription['Subscription']['subject'] = $subject;
        $subscription['Subscription']['notification_type'] = 'add_deny';
        $subscription['Subscription']['notification_json_data'] = null;
        array_push($subscriptions, $subscription);
        
        CakeEventManager::instance()->dispatch(new CakeEvent('Controller.Subscription.notify', $event->subject, array('subscriptions' => $subscriptions)));
    }
    
    private function buildSubscription($userId, $message, $subject, $type, $templateData) {
        $subscription['Subscription'] = array();
        $subscription['Subscription']['user_id'] = $userId;
        $subscription['Subscription']['message'] = $message;
        $subscription['Subscription']['subject'] = $subject;
        $subscription['Subscription']['notification_type'] = $type;
        $subscription['Subscription']['notification_json_data'] = $templateData;
        
        return $subscription;
    }
}
?>