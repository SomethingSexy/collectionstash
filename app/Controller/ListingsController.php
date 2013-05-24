<?php

App::uses('Sanitize', 'Utility');
App::uses('TransactionFactory', 'Lib/Transaction');
/**
 * Need to enable PHP SSL and PHP_SOAP
 */
class ListingsController extends AppController {

	public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');

	/**
	 * This is going to do nothing for now.  The page has static text, unless the user is logged in then
	 * they will see the catalog page.
	 */
	public function index() {

		$transaction['Listing'] = array();
		$transaction['Listing']['listing_type_id'] = 1;
		$transaction['Listing']['ext_item_id'] = '140875618962';
		$transaction['Listing']['collectible_id'] = '234';

		$response = $this -> Listing -> createListing($transaction, $this -> getUser());

		debug($response);
		// first we are going to process it
		// $factory = new TransactionFactory();
		//
		// $transactionable = $factory -> getTransaction($transaction['Listing']['listing_type_id']);
		//
		// $transactionable -> processTransaction($transaction);
	}

	/**
	 * This will be used to update and maintain transactions
	 */
	public function listing($id = null) {
		// need to be logged in
		if (!$this -> isLoggedIn()) {
			$this -> response -> statusCode(401);
			return;
		}

		// create
		if ($this -> request -> isPost()) {
			$transaction['Listing'] = $this -> request -> input('json_decode', true);
			$transaction['Listing'] = Sanitize::clean($transaction['Listing']);

			$response = $this -> Listing -> createListing($transaction, $this -> getUser());

			$this -> set('returnData', $response);
		} else if ($this -> request -> isPut()) {// update
			$transaction = $this -> request -> input('json_decode', true);
			// no need to clean for now on the update
			//$transaction = Sanitize::clean($transaction);

			$response = $this -> Listing -> updatetListing($transaction, $this -> getUser());

			$this -> set('returnData', $response);
		} else if ($this -> request -> isDelete()) {// delete
			// I think it makes sense to use rest delete
			// for changing the status to a delete
			// although I am going to physically delete it
			// not change the status :)
			$response = $this -> Listing -> remove($id, $this -> getUser());

			if (!$response['response']['isSuccess']) {
				$this -> response -> statusCode(400);
			}

			$this -> set('returnData', $response);

		}
	}

	/**
	 * This will be used to retrieve multiple transactions, not sure if I will be using this one or not
	 */
	public function transactions() {

	}

}
?>
