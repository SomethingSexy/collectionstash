<?php
App::uses('Transactionable', 'Lib/Transaction');
class ExternalTradeTransaction extends Object implements Transactionable {

	public function __construct() {
		parent::__construct();
	}

	public function processTransaction($data, $user) {
		// Create headers to send with CURL request.

		$data['Listing']['type'] = 'Trade';
		$data['Listing']['processed'] = true;
		$data['Listing']['status'] = 'completed';
		$data['Listing']['quantity'] = 1;
		$data['Listing']['quantity_sold'] = 1;
		$data['Listing']['listing_name'] = __('Traded by ') . $user['User']['username'];

		$transactions = array();
		$transactions['Transaction'] = array();

		$transaction = array();
		$transaction['collectible_id'] = $data['Listing']['collectible_id'];
		$transaction['sale_date'] = $data['Listing']['end_date'];
		$transaction['traded'] = true;
		$transaction['traded_for'] = $data['Listing']['traded_for'];

		array_push($transactions['Transaction'], $transaction);

		$data['Transaction'] = $transactions['Transaction'];

		return $data;
	}

}
?>