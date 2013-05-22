<?php
App::uses('Transactionable', 'Lib/Transaction');
class EbayTransaction extends Object implements Transactionable {

	public function __construct() {
		parent::__construct();
	}

	public function processTransaction($data) {
		// Create headers to send with CURL request.

		$token = Configure::read('Settings.TransactionManager.eBay.auth_token');
		$appId = Configure::read('Settings.TransactionManager.eBay.AppID');
		
		//grab the current version of the wsdl we are using
		$wsdl_url = APP . 'vendors' . DS . 'transactions' . DS . 'ebay' . DS . 'eBaySvc.wsdl';
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
		$params = array('Version' => 821, 'ItemID' => $data['Transaction']['ext_transaction_id']);

		// make the API call
		$responseObj = $client -> __soapCall($apiCall, array($params), null, $header);

		debug($responseObj);

		// only process if Ack is success

		if ($responseObj -> Ack !== 'Success') {
			return;
		}

		$listType = $responseObj -> Item -> ListingType;
		
		// We also only want to handle listingtypes of
		//StoresFixedPrice = BIN
		//Chinese = auction
		//PersonalOffer second chance offer, we will store as BIN
		if ($listType !== 'StoresFixedPrice' && $listType !== 'Chinese' && $listType !== 'PersonalOffer') {
			return;
		}

		// determime list status, Active, Completed, Ended
		$listingStatus = $responseObj -> Item -> SellingStatus -> ListingStatus;

		// If active, gather some information but do not change processing flag
		if ($listingStatus === 'Active') {

		} else if ($listingStatus === 'Ended') {// ended but we might still need to process to get the ConvertedAmountPaid

		} else if ($listingStatus === 'Completed') {

		}

		//
		// I think I want to use this for the current price
		$responseObj -> Item -> SellingStatus -> ConvertedCurrentPrice;

	}

}
?>