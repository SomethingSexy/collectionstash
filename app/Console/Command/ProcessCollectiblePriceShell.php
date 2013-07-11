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
		debug($transactions);
		
		// then loop through all of the transactions, grab the collectible,
		// 

	}

}
?>