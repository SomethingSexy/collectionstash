<?php
App::uses('Transactionable', 'Lib/Transaction');
class ExternalTradeTransaction extends Object implements Transactionable {

	public function __construct() {
		parent::__construct();
	}

	public function processTransaction($data, $user) {
		// Create headers to send with CURL request.

		$data['Listing']['type'] = 'Trade';
		if ($data['Listing']['active_sale']) {
			$data['Listing']['processed'] = false;
			$data['Listing']['status'] = 'active';
		} else {
			$data['Listing']['processed'] = true;
			$data['Listing']['status'] = 'completed';
		}

		$data['Listing']['quantity'] = 1;
		$data['Listing']['quantity_sold'] = 1;
		$data['Listing']['listing_name'] = __('Traded by ') . $user['User']['username'];

		if (!$data['Listing']['active_sale']) {
			$transactions = array();
			$transactions['Transaction'] = array();

			$transaction = array();
			$transaction['collectible_id'] = $data['Listing']['collectible_id'];
			$transaction['sale_date'] = $data['Listing']['end_date'];
			$transaction['traded'] = true;
			$transaction['traded_for'] = $data['Listing']['traded_for'];

			array_push($transactions['Transaction'], $transaction);

			$data['Transaction'] = $transactions['Transaction'];

		}

		return $data;
	}

	public function createTransaction($data, $listing, $user) {

	}

	public function updateTransaction($data, $listing, $user) {
		$retVal = $listing['Listing']['Transaction'][0];

		$retVal['Transaction']['traded_for'] = $data['traded_for'];

		$retVal['Transaction']['sale_date'] = $data['remove_date'];
	}

}
?>