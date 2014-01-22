<?php
App::uses('Transactionable', 'Lib/Transaction');
class ExternalTransaction extends Object implements Transactionable {

	public function __construct() {
		parent::__construct();
	}

	public function processTransaction($data, $user) {
		// Create headers to send with CURL request.

		$data['Listing']['type'] = 'BIN';
		if ($data['Listing']['active_sale']) {
			$data['Listing']['processed'] = false;
			$data['Listing']['status'] = 'active';
			$data['Listing']['start_date'] = null;
			$data['Listing']['end_date'] = null;
		} else {
			$data['Listing']['processed'] = true;
			$data['Listing']['status'] = 'completed';
			$data['Listing']['start_date'] = null;
		}

		$data['Listing']['quantity'] = 1;
		$data['Listing']['quantity_sold'] = 1;
		$data['Listing']['listing_name'] = __('Sold by ') . $user['User']['username'];

		if (!$data['Listing']['active_sale']) {
			$transactions = array();
			$transactions['Transaction'] = array();

			$transaction = array();
			$transaction['collectible_id'] = $data['Listing']['collectible_id'];
			$transaction['sale_price'] = $data['Listing']['current_price'];
			$transaction['sale_date'] = $data['Listing']['end_date'];

			array_push($transactions['Transaction'], $transaction);

			$data['Transaction'] = $transactions['Transaction'];
		}

		return $data;
	}

	public function createTransaction($data, $listing, $user) {
		$transaction = array();
		$transaction['Transaction'] = array();
		$transaction['Transaction']['collectible_id'] = $data['Listing']['collectible_id'];
		$transaction['Transaction']['sale_price'] = $data['Listing']['current_price'];
		$transaction['Transaction']['sale_date'] = $data['Listing']['end_date'];
		$transaction['Transaction']['listing_id'] = $data['Listing']['id'];

		return $transaction;
	}

	/**
	 * This method updates a single transaction so return it that way
	 */
	public function updateTransaction($data, $listing, $user) {

		$retVal['Transaction'] = $listing['Listing']['Transaction'][0];

		$retVal['Transaction']['sale_price'] = $data['sold_cost'];

		$retVal['Transaction']['sale_date'] = $data['remove_date'];

		return $retVal;
	}

}
?>