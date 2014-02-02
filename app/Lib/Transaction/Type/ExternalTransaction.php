<?php
App::uses('Transactionable', 'Lib/Transaction');
App::uses('BaseTransaction', 'Lib/Transaction');
class ExternalTransaction extends BaseTransaction implements Transactionable {

	public function __construct() {
		parent::__construct();
	}

	public function createListing($model, $data, $user) {
		$retVal = $this -> buildDefaultResponse();

		if (isset($data['active_sale']) && $data['active_sale']) {
			$model -> validate['sold_cost']['allowEmpty'] = false;
			$model -> validate['sold_cost']['required'] = true;
		}

		unset($model -> validate['ext_item_id']);

		$model -> set($data);

		// Validate first
		if (!$model -> validates()) {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($model -> validationErrors, 'Listing');
			$retVal['response']['errors'] = $errors;
			return $retVal;
		}

		$listingData = $this -> processTransaction($data, $user);

		if (!$listingData) {
			$retVal['response']['isSuccess'] = false;
			$errors = array();
			$error = array();
			$error['message'] = __('There was an error retrieving the listing, either it did not exist or it is too old.');
			$error['inline'] = false;
			array_push($errors, $error);

			$retVal['response']['errors'] = $errors;

			return $retVal;
		}

		if ($model -> saveAssociated($listingData, array('validate' => false))) {
			$retVal['response']['isSuccess'] = true;
			$retVal['response']['data']['id'] = $model -> id;
		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($model -> validationErrors, 'Listing');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;

	}

	public function updateListing($model, $data, $user) {
		$retVal = $this -> buildDefaultResponse();
		// this would only be able to update the listing_price and traded for
		$fieldList = array('listing_price', 'traded_for', 'listing_type_id');

		// otherwise we should be checking for permissions here
		if ($this -> save($data, array('validate' => false))) {
			$retVal['response']['isSuccess'] = true;
		}

		return $retVal;
	}

	public function processTransaction($data, $user) {
		// Create headers to send with CURL request.
		$retVal['Listing'] = array();
		$retVal['Listing']['type'] = 'BIN';
		$retVal['Listing']['listing_type_id'] = $data['listing_type_id'];
		$retVal['Listing']['collectible_id'] = $data['collectible_id'];
		if ($data['active_sale']) {
			$retVal['Listing']['processed'] = false;
			$retVal['Listing']['status'] = 'active';
			$retVal['Listing']['start_date'] = null;
			$retVal['Listing']['end_date'] = null;
		} else {
			$retVal['Listing']['processed'] = true;
			$retVal['Listing']['status'] = 'completed';
			$data['Listing']['start_date'] = null;
		}

		$retVal['Listing']['quantity'] = 1;
		$retVal['Listing']['quantity_sold'] = 1;
		$retVal['Listing']['listing_name'] = __('Sold by ') . $user['User']['username'];
		$retVal['Listing']['user_id'] = $user['User']['id'];

		$retVal['Listing']['current_price'] = $data['sold_cost'];
		$retVal['Listing']['listing_price'] = $data['sold_cost'];

		if (!$data['active_sale']) {
			$transactions = array();
			$transactions['Transaction'] = array();

			$transaction = array();
			$transaction['collectible_id'] = $data['collectible_id'];
			$transaction['sale_price'] = $data['sold_cost'];
			$transaction['sale_date'] = $data['end_date'];

			array_push($transactions['Transaction'], $transaction);

			$retVal['Transaction'] = $transactions['Transaction'];
		}

		return $retVal;
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