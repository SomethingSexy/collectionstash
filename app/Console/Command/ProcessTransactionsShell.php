<?php
/**
 * This will process transactions
 *
 *
 * This will run once every hour.
 * 
 * It will looking for any listings that have not finished processing and process them if it can.
 * 
 * It will have to sync up any transactions as well. 
 */
App::uses('TransactionFactory', 'Lib/Transaction');
class ProcessTransactionsShell extends AppShell {
	public $uses = array('Listing');

	public function main() {
		// first get all pending transactions

		$factory = new TransactionFactory();

		$transactions = $this -> Listing -> find('all', array('contain' => false, 'limit' => 50, 'conditions' => array('Listing.processed' => 0)));

		foreach ($transactions as $key => $value) {
			$transactionable = $factory -> getTransaction($value['Listing']['listing_type_id']);
			
			// TODO: This might return multiple transactions, if so we will have to add those
			$transactionable -> processTransaction($value);
		}
	}

}
?>