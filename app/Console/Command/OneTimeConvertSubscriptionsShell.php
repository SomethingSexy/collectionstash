<?php
class OneTimeConvertSubscriptionsShell extends AppShell {
	public $uses = array('Subscription', 'CollectibleSubscription', 'UserSubscription');

	public function main() {
		$subscriptions = $this -> Subscription -> find("all", array('contain' => false));
	}

}
?>