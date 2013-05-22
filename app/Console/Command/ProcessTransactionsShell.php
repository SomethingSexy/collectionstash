<?php
/**
 * This will process transactions
 *
 *
 * This will run once every hour.
 */
App::uses('TransactionFactory', 'Lib/Transaction');
class ProcessTransactionsShell extends AppShell {
	public $uses = array('Transaction');

	public function main() {
		// first get all pending transactions

		$factory = new TransactionFactory();

		$transactions = $this -> Transaction -> find('all', array('contain' => false, 'limit' => 50, 'conditions' => array('Transaction.processed' => 0)));

		foreach ($transactions as $key => $value) {
			$transactionable = $factory -> getTransaction($value['Transaction']['transaction_type_id']);

			$transactionable -> processTransaction($value);
		}
	}

}
?>