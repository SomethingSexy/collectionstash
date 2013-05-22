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

		// 121097551501 - multiple for sale with a bunch sold

		//261217151509 ended unsold
		$params = array('Version' => 821, 'ItemID' => $data['Transaction']['ext_transaction_id']);

		// make the API call
		$responseObj = $client -> __soapCall($apiCall, array($params), null, $header);

		debug($responseObj);

		// only process if Ack is success

		if ($responseObj -> Ack !== 'Success') {
			return;
		}

		$listType = $responseObj -> Item -> ListingType;
		// determime list status, Active, Completed, Ended
		$listingStatus = $responseObj -> Item -> SellingStatus -> ListingStatus;

		// We also only want to handle listingtypes of
		//StoresFixedPrice = BIN
		//Chinese = auction
		//PersonalOffer second chance offer, we will store as BIN
		//FixedPriceItem = multiple
		if ($listType !== 'StoresFixedPrice' && $listType !== 'Chinese' && $listType !== 'PersonalOffer' && $listType !== 'FixedPriceItem') {
			return;
		}
		// process the list type
		if ($listType === 'StoresFixedPrice') {
			$data['Transaction']['type'] = 'BIN';
		} else if ($listType === 'Chinese') {
			$data['Transaction']['type'] = 'auction';
			// if it is an aucion, store the number of bids
			// there might not be a bid count if the time was unsold
			if (isset($responseObj -> Item -> SellingStatus -> BidCount)) {
				$data['Transaction']['number_of_bids'] = $responseObj -> Item -> SellingStatus -> BidCount;
			}

		} else if ($listType === 'PersonalOffer') {
			$data['Transaction']['type'] = 'BIN';
		}

		// this should all be the same
		$data['Transaction']['listing_price'] = $responseObj -> Item -> SellingStatus -> ConvertedCurrentPrice;

		// If active, gather some information but do not change processing flag
		if ($listingStatus === 'Active') {
			$data['Transaction']['status'] = 'active';
		} else if ($listingStatus === 'Ended') {// ended but we might still need to process to get the ConvertedAmountPaid
			$data['Transaction']['status'] = 'ended';
		} else if ($listingStatus === 'Completed') {
			$data['Transaction']['status'] = 'completed';
		}

		// now we need to see if there is a transaction

		// we also need to handle multiple depending on the type.
		if (isset($responseObj -> TransactionArray)) {

			if ($responseObj -> ReturnedTransactionCountActual === 1) {
				// single time
				$responseObj -> TransactionArray -> Transaction;

			} else if ($responseObj -> ReturnedTransactionCountActual > 1) {
				// array of items

				$responseObj -> TransactionArray -> Transaction;
			}
		}

	}	
	
	/**
	 * This will process a single transasction
	 */
	private function processItemTransaction($transaction) {

	}

}
?>