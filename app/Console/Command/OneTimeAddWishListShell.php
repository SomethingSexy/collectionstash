<?php
/**
 * Used to fix  #224
 *
 */
class OneTimeAddWishListShell extends AppShell {
    public $uses = array('User', 'WishList');
    
    public function main() {
        // Grab all Wishlist entries in the stashes table
        $users = $this->User->find("all", array('contain' => false));
        
        foreach ($users as $key => $user) {
            // grab all collectibles for that stash wishlist
            $hasWishList = $this->WishList->find('count', array('contain' => false, 'conditions' => array('WishList.user_id' => $user['User']['id']))) > 0;
            
            if (!$hasWishList) {
                $wishList = array();
                $wishList['WishList'] = array();
                $wishList['WishList']['user_id'] = $user['User']['id'];
                $wishList['WishList']['collectibles_wish_list_count'] = 0;
                // save
                if ($this->WishList->saveAssociated($wishList)) {
                }
            }
        }
    }
}
?>