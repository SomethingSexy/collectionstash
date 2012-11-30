<?php
/**
 * This shell adds the "wishlist" stash to current user's
 *
 */
class AddNewStashShell extends AppShell {
	public $uses = array('User', 'Stash');

	public function main() {
		// get all users
		$users = $this -> User -> find("all", array('contain' => false));

		foreach ($users as $key => $user) {
			$userData['Stash'] = array();
			$userData['Stash']['name'] = 'Wishlist';
			//Need to put this here to create the entity
			// TODO: Update Stash to use the EntityTypeBehavior to automate this shit
			$userData['EntityType']['type'] = 'stash';
			$userData['Stash']['user_id'] = $user['User']['id'];

			$this -> Stash -> create();
			$this -> Stash -> saveAll($userData);
		}

	}

}
?>