<?php
App::uses('Sanitize', 'Utility');
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
		// Create headers to send with CURL request.

		$token = Configure::read('Settings.TransactionManager.eBay.auth_token');
		$appId = Configure::read('Settings.TransactionManager.eBay.AppID');
		//download when ready
		$wsdl_url = 'http://developer.ebay.com/webservices/latest/eBaySvc.wsdl';
		// downloaded from http://developer.ebay.com/webservices/latest/eBaySvc.wsdl

		$apiCall = 'GetItemTransactions';

		$client = new SoapClient($wsdl_url, array('trace' => 1, 'exceptions' => true, 'location' => 'https://api.ebay.com/wsapi?callname=' . $apiCall . '&appid=' . $appId . '&siteid=0&version=821&routing=new'));

		$requesterCredentials = new stdClass();
		$requesterCredentials -> eBayAuthToken = $token;

		$header = new SoapHeader('urn:ebay:apis:eBLBaseComponents', 'RequesterCredentials', $requesterCredentials);

		// the API call parameters
		//221229498879
		//
		//370144056958
		//171041720659 - active biding list
		//390595100332 - sold listing | ListingStatus = completed
		//230981171092 - sold listing - buy it now
		//230980042238 sold listing - buy it now - best offer accepted
		//260852933448  example of older one, ListStatus is completed but no transaction, has a QuantitySold 1 and a price
		//160384368644 example of one that does not exist anymore
		$params = array('Version' => 821, 'ItemID' => '160384368644');

		$responseObj = $client -> __soapCall($apiCall, array($params), null, $header);
		// make the API call
		debug($responseObj);
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
