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

			$average = $average / $total;
			$averageEbay = $averageEbay / $totalEbay;
			$averageExternal = $averageExternal / $totalExternal;

			debug($average);
			debug($averageEbay);
			debug($averageExternal);
		}
	}

}
?>