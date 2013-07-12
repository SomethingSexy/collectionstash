<?php
/**
 *
 * I plan on having this run once a day.
 */

class ProcessCollectiblePriceShell extends AppShell {
	public $uses = array('Collectible', 'CollectiblePriceFact', 'Transaction');

	public function main() {
		// Trying this for now, use distinct to grab all collectible ids that have transactions

		$transactions = $this -> Transaction -> find('all', array('contain' => false, 'fields' => array('DISTINCT Transaction.collectible_id')));

		// then loop through all of the transactions, grab the collectible,
		//
		if (empty($transactions)) {
			return;
		}

		// loop through all of the collectibles with transactions
		foreach ($transactions as $key => $value) {
			$collectibleId = $value['Transaction']['collectible_id'];

			$collectibleTransactions = $this -> Transaction -> find('all', array('contain' => array('Listing'), 'conditions' => array('Transaction.collectible_id' => $collectibleId)));

			$average = 0;
			$total = count($collectibleTransactions);

			$averageEbay = 0;
			$totalEbay = 0;

			$averageExternal = 0;
			$totalExternal = 0;

			foreach ($collectibleTransactions as $key => $transaction) {

				// count up all of the sale_prices
				$average = $average + $transaction['Transaction']['sale_price'];
				if ($transaction['Listing']['listing_type_id'] === '1') {
					$averageEbay = $averageEbay + $transaction['Transaction']['sale_price'];
					$totalEbay = $totalEbay + 1;
				} else if ($transaction['Listing']['listing_type_id'] === '2') {
					$averageExternal = $averageExternal + $transaction['Transaction']['sale_price'];
					$totalExternal = $totalExternal + 1;
				}
			}
			if ($total !== 0) {
				$average = $average / $total;
			}

			if ($totalEbay !== 0) {
				$averageEbay = $averageEbay / $totalEbay;
			}

			if ($totalExternal !== 0) {
				$averageExternal = $averageExternal / $totalExternal;
			}

			// debug($average);
			// debug($averageEbay);
			// debug($averageExternal);

			// now that we are calculated, let's grab the collectible and see if it has a fact table

			$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => array('CollectiblePriceFact')));

			// dis means we already have calculated information for this collectible
			if (isset($collectible['CollectiblePriceFact'])) {
				// check to see if we need to update it
				$change = false;
				if ($collectible['CollectiblePriceFact']['average_price'] !== $average || $collectible['CollectiblePriceFact']['total_transactions'] !== $total) {
					$change = true;
					$collectible['CollectiblePriceFact']['average_price'] = $average;
					$collectible['CollectiblePriceFact']['total_transactions'] = $total;
				}

				if ($collectible['CollectiblePriceFact']['average_price_ebay'] !== $averageEbay || $collectible['CollectiblePriceFact']['total_transactions_ebay'] !== $totalEbay) {
					$change = true;
					$collectible['CollectiblePriceFact']['average_price_ebay'] = $averageEbay;
					$collectible['CollectiblePriceFact']['total_transactions_ebay'] = $totalEbay;
				}

				if ($collectible['CollectiblePriceFact']['average_price_external'] !== $averageExternal || $collectible['CollectiblePriceFact']['total_transactions_external'] !== $totalExternal) {
					$change = true;
					$collectible['CollectiblePriceFact']['average_price_external'] = $averageExternal;
					$collectible['CollectiblePriceFact']['total_transactions_external'] = $totalExternal;
				}

				// only update on a change, save powers
				if ($change) {
					unset($collectible['Collectible']);
					$this -> CollectiblePriceFact -> save($collectible);
				}
			} else {
				// if it isn't set then we need to create a new one and update the collectible to store da reference
				$collectible['CollectiblePriceFact'] = array();
				$collectible['CollectiblePriceFact']['average_price'] = $average;
				$collectible['CollectiblePriceFact']['average_price_ebay'] = $averageEbay;
				$collectible['CollectiblePriceFact']['average_price_external'] = $averageExternal;
				$collectible['CollectiblePriceFact']['total_transactions'] = $total;
				$collectible['CollectiblePriceFact']['total_transactions_ebay'] = $totalEbay;
				$collectible['CollectiblePriceFact']['total_transactions_external'] = $totalExternal;

				$this -> Collectible -> saveAssociated($collectible, array('fieldList' => array('Collectible' => array('collectible_price_fact_id'), 'CollectiblePriceFact' => array('average_price', 'average_price_ebay', 'average_price_external', 'total_transactions', 'total_transactions_ebay', 'total_transactions_external'))));
			}

		}
	}

}
?>