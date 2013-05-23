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

		// setting returnall for now but we will need see performance
		$params = array('Version' => 821, 'ItemID' => $data['Listing']['ext_item_id'], 'DetailLevel' => 'ReturnAll');

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
			$data['Listing']['type'] = 'BIN';
		} else if ($listType === 'Chinese') {
			$data['Listing']['type'] = 'auction';
			// if it is an aucion, store the number of bids
			// there might not be a bid count if the time was unsold
			if (isset($responseObj -> Item -> SellingStatus -> BidCount)) {
				$data['Listing']['number_of_bids'] = $responseObj -> Item -> SellingStatus -> BidCount;
			}

		} else if ($listType === 'PersonalOffer') {
			$data['Listing']['type'] = 'BIN';
		}

		// this should all be the same
		$data['Listing']['listing_price'] = $responseObj -> Item -> StartPrice -> _;
		$data['Listing']['current_price'] = $responseObj -> Item -> SellingStatus -> ConvertedCurrentPrice -> _;

		// might have to conveert these
		$data['Listing']['start_date'] = $responseObj -> Item -> ListingDetails -> StartTime;
		$data['Listing']['end_date'] = $responseObj -> Item -> ListingDetails -> EndTime;
		$data['Listing']['listing_name'] = $responseObj -> Item -> Title;
		$data['Listing']['quantity'] = $responseObj -> Item -> Quantity;
		$data['Listing']['url'] = $responseObj -> Item -> ListingDetails -> ViewItemURLForNaturalSearch ;

		// If active, gather some information but do not change processing flag
		if ($listingStatus === 'Active') {
			$data['Listing']['status'] = 'active';
		} else if ($listingStatus === 'Ended') {// ended but we might still need to process to get the ConvertedAmountPaid
			$data['Listing']['status'] = 'ended';
		} else if ($listingStatus === 'Completed') {
			$data['Listing']['status'] = 'completed';
			$data['Listing']['processed'] = true; 
		}

		// now we need to see if there is a transaction

		// we also need to handle multiple depending on the type.

		//TransactionPrice only for Best Offer Items

		// If StoresFixedPrice and BestOfferSale = true, check ConvertedTransactionPrice, for what they paid

		// I think I always want the ConvertedTransactionPrice

		// also want TransactionID for external

		$transactions = array();
		$transactions['Transaction'] = array();

		if (isset($responseObj -> TransactionArray)) {

			if ($responseObj -> ReturnedTransactionCountActual === 1) {
				// single time
				$transaction = $this -> processItemTransaction($responseObj -> TransactionArray -> Transaction, $data['Listing']['collectible_id']);
				array_push($transactions['Transaction'], $transaction);

			} else if ($responseObj -> ReturnedTransactionCountActual > 1) {
				// array of items
				$responseObj -> TransactionArray -> Transaction;

			}
		}
		$data['Transaction'] = $transactions['Transaction'];

		debug($data);
		
		return $data;
	}

	/**
	 * This will process a single transasction
	 */
	private function processItemTransaction($ebayTransaction, $collectibleId) {
		$retVal = array();

		$retVal['ext_transaction_id'] = $ebayTransaction -> TransactionID;
		$retVal['collectible_id'] = $collectibleId;
		$retVal['sale_price'] = $ebayTransaction -> ConvertedTransactionPrice -> _;
		$retVal['sale_date'] = $ebayTransaction -> CreatedDate;
		$retVal['bestOffer'] = $ebayTransaction -> BestOfferSale;

		return $retVal;

	}

}
?>