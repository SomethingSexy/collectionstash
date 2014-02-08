<?php
App::uses('Transactionable', 'Lib/Transaction');
App::uses('BaseTransaction', 'Lib/Transaction');
class ExternalTradeTransaction extends BaseTransaction implements Transactionable {

	public function __construct() {
		parent::__construct();
	}

	public function createListing($model, $data, $user) {
		$retVal = $this -> buildDefaultResponse();

		if (isset($data['active_sale']) && $data['active_sale']) {
			$model -> validate['traded_for']['maxLength']['allowEmpty'] = false;
			$model -> validate['traded_for']['maxLength']['required'] = true;
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

		$model -> validate['traded_for']['maxLength']['allowEmpty'] = false;
		$model -> validate['traded_for']['maxLength']['required'] = true;
		unset($model -> validate['ext_item_id']);
		// this would only be able to update the listing_price and traded for
		$fieldList = array('listing_price', 'current_price', 'traded_for', 'listing_type_id');

		// updating just in case they are switching between types
		$data['Listing']['listing_name'] = __('Traded by ') . $user['User']['username'];
		// null these out
		$data['Listing']['current_price'] = null;
		$data['Listing']['listing_price'] = null;

		// otherwise we should be checking for permissions here
		if ($model -> save($data)) {
			$retVal['response']['isSuccess'] = true;
		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($model -> validationErrors, 'Listing');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	public function processTransaction($data, $user) {
		// Create headers to send with CURL request.
		$retVal['Listing'] = array();
		$retVal['Listing']['type'] = 'Trade';
		$retVal['Listing']['listing_type_id'] = $data['listing_type_id'];
		$retVal['Listing']['collectible_id'] = $data['collectible_id'];
		if ($data['active_sale']) {
			$retVal['Listing']['processed'] = false;
			$retVal['Listing']['status'] = 'active';
			$retVal['Listing']['start_date'] = null;
			$retVal['Listing']['end_date'] = null;
			$retVal['Listing']['traded_for'] = $data['traded_for'];
		} else {
			$retVal['Listing']['processed'] = true;
			$retVal['Listing']['status'] = 'completed';
			$retVal['Listing']['start_date'] = null;
		}

		$retVal['Listing']['quantity'] = 1;
		$retVal['Listing']['quantity_sold'] = 1;
		$retVal['Listing']['listing_name'] = __('Traded by ') . $user['User']['username'];

		if (!$data['active_sale']) {
			$transactions = array();
			$transactions['Transaction'] = array();

			$transaction = array();
			$transaction['collectible_id'] = $data['collectible_id'];
			$transaction['sale_date'] = $data['end_date'];
			$transaction['traded'] = true;
			$transaction['traded_for'] = $data['traded_for'];

			array_push($transactions['Transaction'], $transaction);

			$retVal['Transaction'] = $transactions['Transaction'];

		}

		return $retVal;
	}

	public function createTransaction($data, $listing, $user) {
		$transaction = array();
		$transaction['Transaction'] = array();
		$transaction['Transaction']['collectible_id'] = $data['Listing']['collectible_id'];
		$transaction['Transaction']['traded_for'] = $data['Listing']['traded_for'];
		$transaction['Transaction']['sale_date'] = $data['Listing']['end_date'];
		$transaction['Transaction']['listing_id'] = $data['Listing']['id'];

		return $transaction;
	}

	public function updateTransaction($data, $listing, $user) {
		$retVal['Transaction'] = $listing['Listing']['Transaction'][0];

		$retVal['Transaction']['traded_for'] = $data['traded_for'];

		$retVal['Transaction']['sale_date'] = $data['remove_date'];

		return $retVal;
	}

}
?>