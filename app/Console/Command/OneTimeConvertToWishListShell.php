<?php
/**
 * This will be a one time shell that will convert wishlist collectibles to the new tables
 *
 */
class OneTimeConvertToWishListShell extends AppShell {
	public $uses = array('Stash', 'CollectiblesUser', 'WishList', 'CollectiblesWishList');

	public function main() {

		// Grab all Wishlist entries in the stashes table
		$stashes = $this -> Stash -> find("all", array('contain' => false, 'conditions' => array('Stash.Name' => 'Wishlist')));

		foreach ($stashes as $key => $stash) {
			// grab all collectibles for that stash wishlist
			$collectiblesUsers = $this -> CollectiblesUser -> find('all', array('contain' => false, 'conditions' => array('CollectiblesUser.stash_id' => $stash['Stash']['id'])));

			// create new wishlist entry
			$wishList = array();
			$wishList['WishList'] = array();
			$wishList['WishList']['user_id'] = $stash['Stash']['user_id'];
			//$wishList['WishList']['privacy'] = $stash['Stash']['privacy'];
			$wishList['CollectiblesWishList'] = array();

			// copy over collectibles
			if (!empty($collectiblesUsers)) {
				foreach ($collectiblesUsers as $key => $collectiblesUser) {
					array_push($wishList['CollectiblesWishList'], array('collectible_id' => $collectiblesUser['CollectiblesUser']['collectible_id'], 'user_id' => $collectiblesUser['CollectiblesUser']['user_id']));
				}
			}

			// save
			if ($this -> WishList -> saveAssociated($wishList)) {
				// delete row in stash
				// delete collectibles_user
				$this -> Stash -> delete($stash['Stash']['id']);
			}

		}

	}

}
?>