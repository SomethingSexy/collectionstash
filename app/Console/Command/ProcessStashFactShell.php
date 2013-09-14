<?php
/**
 * This will process the user's stash fact data
 *
 * this will run once a day
 *
 */

class ProcessStashFactShell extends AppShell {
	public $uses = array('Stash', 'Collectible', 'CollectiblesUser', 'StashFact', 'Listing');

	private function isfloat($f) {
		return ($f == (string)(float)$f);
	}

	public function main() {
		// grab all stashes that are set to "Default"

		// just grab the stash data, we will grab subsequent data as we need it
		$stashes = $this -> Stash -> find('all', array('conditions' => array('Stash.name' => 'Default'), 'contain' => false));
		//$collectibleUsers = $this -> CollectiblesUser -> find('all', array('conditions' => array('CollectiblesUser.stash_id' => 1), 'contain' => array('Listing' => array('Transaction'), 'Collectible' => array('CollectiblePriceFact'))));
		foreach ($stashes as $key => $stash) {
			$stashFact = $this -> StashFact -> find('first', array('conditions' => array('StashFact.stash_id' => $stash['Stash']['id']), 'contain' => false));

			if (empty($stashFact)) {
				$stashFact['StashFact'] = array();
				$stashFact['StashFact']['stash_id'] = $stash['Stash']['id'];
				$this -> StashFact -> create();
			}

			// then find all collectibles for those stashes, we wil need the collectible table and the CollectiblePriceFact table
			$collectibleUsers = $this -> CollectiblesUser -> find('all', array('conditions' => array('CollectiblesUser.stash_id' => $stash['Stash']['id']), 'contain' => array('Listing' => array('Transaction'), 'Collectible' => array('CollectiblePriceFact'))));

			$msrp = 0;
			$totalPaid = 0;
			$countCollectiblesPaid = 0;
			$totalSold = 0;
			$countCollectiblesSold = 0;
			$currentValue = 0;
			$countCollectibleCurrentValue = 0;
			// this is the count of how many collectibles have been removed and indicated they were sold
			$countCollectiblesRemoveSold = 0;

			foreach ($collectibleUsers as $key => $collectibleUser) {
				// calculate the msrp value of the stash
				$msrp = $msrp + $collectibleUser['Collectible']['msrp'];
				// then calculate the current value
				if (!is_null($collectibleUser['Collectible']['collectible_price_fact_id']) && isset($collectibleUser['Collectible']['CollectiblePriceFact']) && $this -> isfloat($collectibleUser['Collectible']['CollectiblePriceFact']['average_price'])) {
					$currentValue = $currentValue + $collectibleUser['Collectible']['CollectiblePriceFact']['average_price'];
					$countCollectibleCurrentValue = $countCollectibleCurrentValue + 1;
				}

				// calculate the total paid of the stash
				// TODO: need to make sure when we remove cost, it is null
				if (!is_null($collectibleUser['CollectiblesUser']['cost'])) {
					$totalPaid = $totalPaid + $collectibleUser['CollectiblesUser']['cost'];
					$countCollectiblesPaid = $countCollectiblesPaid + 1;
				}
				// calculate the total sold cost of the stash
				if (!is_null($collectibleUser['CollectiblesUser']['listing_id']) && isset($collectibleUser['Listing']) && $this -> isfloat($collectibleUser['Listing']['current_price'])) {
					$countCollectiblesSold = $countCollectiblesSold + 1;
					$totalSold = $totalSold + $collectibleUser['Listing']['current_price'];
				}

				if (!$collectibleUser['CollectiblesUser']['active'] && $collectibleUser['CollectiblesUser']['collectible_user_remove_reason_id'] == 1) {
					$countCollectiblesRemoveSold = $countCollectiblesRemoveSold + 1;
				}

			}

			$stashFact['StashFact']['msrp_value'] = $msrp;
			$stashFact['StashFact']['total_paid'] = $totalPaid;
			$stashFact['StashFact']['count_collectibles_paid'] = $countCollectiblesPaid;
			$stashFact['StashFact']['total_sold'] = $totalSold;
			$stashFact['StashFact']['count_collectibles_sold'] = $countCollectiblesSold;
			$stashFact['StashFact']['current_value'] = $currentValue;
			$stashFact['StashFact']['count_collectibles_current_value'] = $countCollectibleCurrentValue;
			$stashFact['StashFact']['count_collectibles_remove_sold'] = $countCollectiblesRemoveSold;

			unset($stashFact['StashFact']['modified']);

			$this -> StashFact -> save($stashFact);

		}

	}

}
?>