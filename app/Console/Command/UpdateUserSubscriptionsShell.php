<?php
/**
 * This is a one time process to auto subscribe existing user's to thier
 * stashes.  All new users going forward will be automatically subscribed
 */
class UpdateUserSubscriptionsShell extends AppShell {
	public $uses = array('Stash', 'Subscription');

	public function main() {
		//Grab all Stashes
		$stashes = $this -> Stash -> find("all", array('contain' => array('User', 'EntityType')));

		foreach ($stashes as $key => $stash) {
			$subscription = $this -> Subscription -> find('first', array('conditions' => array('Subscription.entity_type_id' => $stash['EntityType']['id'], 'Subscription.user_id' => $stash['Stash']['user_id'])));
			$this -> out(print_r($subscription, true));
			if (empty($subscription)) {
				$this -> Subscription -> create();
				$subscriptionAdd = array();
				$subscriptionAdd['Subscription']['entity_type_id'] = $stash['EntityType']['id'];
				$subscriptionAdd['Subscription']['user_id'] = $stash['Stash']['user_id'];
				$this -> Subscription -> save($subscriptionAdd);
			}
		}
	}

}
?>