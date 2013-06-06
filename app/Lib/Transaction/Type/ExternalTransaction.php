<?php
App::uses('Transactionable', 'Lib/Transaction');
class ExternalTransaction extends Object implements Transactionable {

	public function __construct() {
		parent::__construct();
	}

	public function processTransaction($data) {
		// Create headers to send with CURL request.

		$data['Listing']['type'] = 'BIN';
		$data['Listing']['processed'] = true;
		$data['Listing']['status'] = 'completed';
		$data['Listing']['quantity'] = 1;
		$data['Listing']['quantity_sold'] = 1;

		$transactions = array();
		$transactions['Transaction'] = array();

		$transaction = array();
		$transaction['collectible_id'] = $data['Listing']['collectible_id'];
		$transaction['sale_price'] = $data['Listing']['current_price'];
		$transaction['sale_date'] = $data['Listing']['end_date'];

		array_push($transactions['Transaction'], $transaction);

		return $data;
	}

}
?>