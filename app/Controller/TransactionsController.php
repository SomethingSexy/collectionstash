<?php

App::uses('Sanitize', 'Utility');
App::uses('TransactionFactory', 'Lib/Transaction');
/**
 * Need to enable PHP SSL and PHP_SOAP
 */
class TransactionsController extends AppController {

	public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');

	/**
	 * This is going to do nothing for now.  The page has static text, unless the user is logged in then
	 * they will see the catalog page.
	 */
	public function index() {

		$transaction['Transaction'] = array();
		$transaction['Transaction']['transaction_type_id'] = 1;
		$transaction['Transaction']['ext_transaction_id'] = '230981171092';

		// first we are going to process it
		$factory = new TransactionFactory();

		$transactionable = $factory -> getTransaction($transaction['Transaction']['transaction_type_id']);

		$transactionable -> processTransaction($transaction);
	}

	/**
	 * This will be used to update and maintain transactions
	 */
	public function transaction($id = null) {
		// need to be logged in
		if (!$this -> isLoggedIn()) {
			$this -> response -> statusCode(401);
			return;
		}

		// create
		if ($this -> request -> isPost()) {
			$transaction['Transaction'] = $this -> request -> input('json_decode', true);
			$transaction['Transaction'] = Sanitize::clean($transaction['Transaction']);

			$response = $this -> Transaction -> createTransaction($transaction, $this -> getUser());

			$this -> set('returnData', $response);
		} else if ($this -> request -> isPut()) {// update
			$transaction['Transaction'] = $this -> request -> input('json_decode', true);
			$transaction['Transaction'] = Sanitize::clean($transaction['Transaction']);

			$response = $this -> Transaction -> updatetTransaction($transaction, $this -> getUser());

			$request = $this -> request -> input('json_decode');
			debug($request);
			if (!$response['response']['isSuccess'] && $response['response']['code'] = 401) {
				$this -> response -> statusCode(401);
			} else {
				// request becomes an actual object and not an array
				$request -> isEdit = $response['response']['data']['isEdit'];
			}

			$this -> set('returnData', $request);
		} else if ($this -> request -> isDelete()) {// delete
			// I think it makes sense to use rest delete
			// for changing the status to a delete
			// although I am going to physically delete it
			// not change the status :)
			$response = $this -> Transaction -> remove($id, $this -> getUser());

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
