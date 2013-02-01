<?php
/**
 * This will be a one time shell that will take the total points prior to storing
 * activities and add them to the user point fact table so that we have the base
 * line in there
 *
 */
class OneTimeUpdateCollectibleStatusShell extends AppShell {
	public $uses = array('Collectible');

	public function main() {

		// Just grab all of the users and I will do any manually processing, should be faster
		$collectibles = $this -> Collectible -> find("all", array('contain' => false));

		foreach ($collectibles as $key => $collectible) {
			$statusId = null;

			if ($collectible['Collectible']['state'] === '0') {
				// approved
				$statusId = 4;
			} else if ($collectible['Collectible']['state'] === '1') {
				// pending
				$statusId = 2;
			} else if ($collectible['Collectible']['state'] === '2') {
				$statusId = 6;
			}

			$this -> Collectible -> id = $collectible['Collectible']['id'];
			$this -> Collectible -> saveField('status_id', $statusId, false);
		}

	}

}
?>