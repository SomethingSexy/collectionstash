<?php
App::uses('Transactionable', 'Lib/Transaction');
App::uses('BaseTransaction', 'Lib/Transaction');
class EbayTransaction extends BaseTransaction implements Transactionable
{
    
    public function __construct() {
        parent::__construct();
    }
    /**
     *
     */
    public function createListing($model, $data, $user) {
        $retVal = $this->buildDefaultResponse();
        
        $model->validate['listing_price']['allowEmpty'] = true;
        $model->validate['listing_price']['required'] = false;
        $model->validate['traded_for']['maxLength']['allowEmpty'] = true;
        $model->validate['traded_for']['maxLength']['required'] = false;
        
        $model->set($data);
        // Validate first
        if (!$model->validates()) {
            $retVal['response']['isSuccess'] = false;
            $retVal['response']['data'] = $model->validationErrors;
            return $retVal;
        }
        
        $listingData['Listing'] = $data;
        $listingData['Listing']['user_id'] = $user['User']['id'];
        
        $listingData = $this->processTransaction($listingData, $user);
        
        if (!$listingData || isset($listData['error'])) {
            $retVal['response']['isSuccess'] = false;
            $retVal['response']['isSuccess']['message'] = __('There was an error retrieving the listing, either it did not exist or it is too old.');
            $retVal['response']['errors'] = $errors;
            
            return $retVal;
        }
        
        if ($model->saveAssociated($listingData, array('validate' => false))) {
            $retVal['response']['isSuccess'] = true;
            $retVal['response']['data']['id'] = $model->id;
            // if this is a relisting, take the relist id, if it hasn't been added
            // already then we want
            if (isset($listingData['Listing']['relisted']) && $listingData['Listing']['relisted'] && $listingData['Listing']['relisted_ext_id']) {
                
                $relisting = array();
                $relisting['Listing']['listing_type_id'] = $listingData['Listing']['listing_type_id'];
                $relisting['Listing']['collectible_id'] = $listingData['Listing']['collectible_id'];
                $relisting['Listing']['user_id'] = $user['User']['id'];
                $relisting['Listing']['ext_item_id'] = $listingData['Listing']['relisted_ext_id'];
                
                $model->set($relisting['Listing']);
                
                if ($model->validates()) {
                    $relisting = $this->processTransaction($relisting, $user);
                    // set this guy to false so it will be processed later, this is for the rare
                    // cases of a relisting of a relisting
                    $relisting['Listing']['processed'] = false;
                    // save but don't worry about it failing for now
                    $model->saveAssociated($relisting);
                }
            }
        } else {
            $retVal['response']['isSuccess'] = false;
            $retVal['response']['data'] = $model->validationErrors;
        }
        
        return $retVal;
    }
    
    public function updateListing($model, $data, $user) {
        $retVal = $this->buildDefaultResponse();
        
        $model->id = $data['Listing']['id'];
        $model->save($data, array('fieldList' => array('flagged')));
        
        $retVal['response']['data'] = $data;
        $retVal['response']['isSuccess'] = true;
        
        return $retVal;
    }
    
    public function processTransaction($data, $user) {
        // Create headers to send with CURL request.
        
        $token = Configure::read('Settings.TransactionManager.eBay.auth_token');
        $appId = Configure::read('Settings.TransactionManager.eBay.AppID');
        //grab the current version of the wsdl we are using
        $wsdl_url = APP . 'Vendor' . DS . 'transactions' . DS . 'ebay' . DS . 'eBaySvc.wsdl';
        // downloaded from http://developer.ebay.com/webservices/latest/eBaySvc.wsdl
        
        $apiCall = 'GetItemTransactions';
        
        $client = new SoapClient($wsdl_url, array('trace' => 1, 'exceptions' => true, 'location' => 'https://api.ebay.com/wsapi?callname=' . $apiCall . '&appid=' . $appId . '&siteid=0&version=821&routing=new'));
        
        $requesterCredentials = new stdClass();
        $requesterCredentials->eBayAuthToken = $token;
        
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
        $params = array('Version' => 821, 'ItemID' => $data['Listing']['ext_item_id'], 'DetailLevel' => 'ReturnAll', 'AffiliateTrackingDetails' => array('TrackingID' => '5337341146', 'TrackingPartnerCode' => 9));
       
        debug($params);
        // make the API call
        $responseObj = $client->__soapCall($apiCall, array($params), null, $header);

        debug( $responseObj);
        // only process if Ack is success
        
        if ($responseObj->Ack !== 'Success') {
            // else i f error code is 17 that means it is missing or gone, so delete
            if ($responseObj->Errors->ErrorCode === '17') {
                // convert to our arbitary code system :)
                return array('error' => array('code' => '1', 'missing' => true));
            } else {
                // we don't handle yet
                return false;
            }
        }
        
        $listType = $responseObj->Item->ListingType;
        // determime list status, Active, Completed, Ended
        $listingStatus = $responseObj->Item->SellingStatus->ListingStatus;
        // We also only want to handle listingtypes of
        //StoresFixedPrice = BIN
        //Chinese = auction
        //PersonalOffer second chance offer, we will store as BIN
        //FixedPriceItem = multiple
        if ($listType !== 'StoresFixedPrice' && $listType !== 'Chinese' && $listType !== 'PersonalOffer' && $listType !== 'FixedPriceItem') {
            return false;
        }
        // process the list type
        if ($listType === 'StoresFixedPrice') {
            $data['Listing']['type'] = 'BIN';
        } else if ($listType === 'Chinese') {
            $data['Listing']['type'] = 'Auction';
            // if it is an aucion, store the number of bids
            // there might not be a bid count if the time was unsold
            if (isset($responseObj->Item->SellingStatus->BidCount)) {
                $data['Listing']['number_of_bids'] = $responseObj->Item->SellingStatus->BidCount;
            }
        } else if ($listType === 'PersonalOffer') {
            $data['Listing']['type'] = 'BIN';
        } else if ($listType === 'FixedPriceItem') {
            $data['Listing']['type'] = 'Store';
        }
        // this should all be the same
        $data['Listing']['listing_price'] = $responseObj->Item->StartPrice->_;
        $data['Listing']['current_price'] = $responseObj->Item->SellingStatus->ConvertedCurrentPrice->_;
        // might have to conveert these
        $data['Listing']['start_date'] = $responseObj->Item->ListingDetails->StartTime;
        $data['Listing']['end_date'] = $responseObj->Item->ListingDetails->EndTime;
        $data['Listing']['listing_name'] = $responseObj->Item->Title;
        $data['Listing']['quantity'] = $responseObj->Item->Quantity;
        $data['Listing']['quantity_sold'] = $responseObj->Item->SellingStatus->QuantitySold;
        $data['Listing']['url'] = $responseObj->Item->ListingDetails->ViewItemURLForNaturalSearch . '&campid=5337341146&customid=&toolid=10001';
        
        if (isset($responseObj->Item->ConditionID)) {
            $data['Listing']['condition_ext_id'] = $responseObj->Item->ConditionID;
        }
        if (isset($responseObj->Item->ConditionDisplayName)) {
            $data['Listing']['condition_name'] = $responseObj->Item->ConditionDisplayName;
        }
        // If active, gather some information but do not change processing flag
        if ($listingStatus === 'Active') {
            $data['Listing']['status'] = 'active';
        } else if ($listingStatus === 'Ended') { // ended but we might still need to process to get the ConvertedAmountPaid
            $data['Listing']['status'] = 'ended';
        } else if ($listingStatus === 'Completed') {
            // TODO How do we handle unsold stuff?
            // It is completed by quantity sold = 0
            $data['Listing']['status'] = 'completed';
            $data['Listing']['processed'] = true;
            // here we need to check to see if it was relisted
            // if it is completed but unsold and there is a
            // RelistedItemID, we will specify on the return to also
            if ($responseObj->Item->SellingStatus->QuantitySold === 0 && isset($responseObj->Item->ListingDetails->RelistedItemID) && !empty($responseObj->Item->ListingDetails->RelistedItemID)) {
                $data['Listing']['relisted'] = true;
                $data['Listing']['relisted_ext_id'] = $responseObj->Item->ListingDetails->RelistedItemID;
            }
        }
        // now we need to see if there is a transaction
        
        // we also need to handle multiple depending on the type.
        
        //TransactionPrice only for Best Offer Items
        
        // If StoresFixedPrice and BestOfferSale = true, check ConvertedTransactionPrice, for what they paid
        
        // I think I always want the ConvertedTransactionPrice
        
        // also want TransactionID for external
        
        $transactions = array();
        $transactions['Transaction'] = array();
        
        if (isset($responseObj->TransactionArray)) {
            
            if ($responseObj->ReturnedTransactionCountActual === 1) {
                // single time
                $transaction = $this->processItemTransaction($responseObj->TransactionArray->Transaction, $data['Listing']['collectible_id']);
                array_push($transactions['Transaction'], $transaction);
            } else if ($responseObj->ReturnedTransactionCountActual > 1) {
                // array of items
                foreach ($responseObj->TransactionArray->Transaction as $key => $value) {
                    $transaction = $this->processItemTransaction($value, $data['Listing']['collectible_id']);
                    array_push($transactions['Transaction'], $transaction);
                }
            }
        } else {
            // there can be a chance on older ones that might not have transactions
            // but are completed
            if ($listingStatus === 'Completed' && $responseObj->Item->SellingStatus->QuantitySold === 1) {
                $transaction = array();
                //$transaction['ext_transaction_id'] = $ebayTransaction -> TransactionID;
                $transaction['collectible_id'] = $data['Listing']['collectible_id'];
                $transaction['sale_price'] = $responseObj->Item->SellingStatus->ConvertedCurrentPrice->_;
                $transaction['sale_date'] = $responseObj->Item->ListingDetails->EndTime;
                
                array_push($transactions['Transaction'], $transaction);
            }
        }
        // before we add them to the return data, let's see if we have any existing
        // transactions that were adding previously
        // This should only happen when we are processing existing ones that
        // have not been completed before
        if (!empty($data['Transaction'])) {
            foreach ($transactions['Transaction'] as $key => $processedTransaction) {
                $alreadyProcessed = false;
                foreach ($data['Transaction'] as $key => $existingTransaction) {
                    if ($processedTransaction['ext_transaction_id'] === $existingTransaction['ext_transaction_id']) {
                        $alreadyProcessed = true;
                    }
                }
                
                if (!$alreadyProcessed) {
                    array_push($data['Transaction'], $processedTransaction);
                }
            }
        } else { // otherwise just add all
            $data['Transaction'] = $transactions['Transaction'];
        }
        
        return $data;
    }
    
    public function createTransaction($data, $listing, $user) {
    }
    
    public function updateTransaction($data, $listing, $user) {
    }
    /**
     * This will process a single transasction
     */
    private function processItemTransaction($ebayTransaction, $collectibleId) {
        $retVal = array();
        
        $retVal['ext_transaction_id'] = $ebayTransaction->OrderLineItemID;
        $retVal['collectible_id'] = $collectibleId;
        $retVal['sale_price'] = $ebayTransaction->ConvertedTransactionPrice->_;
        $retVal['sale_date'] = $ebayTransaction->CreatedDate;
        if (!is_null($ebayTransaction->BestOfferSale)) {
            $retVal['bestOffer'] = $ebayTransaction->BestOfferSale;
        } else {
            $retVal['bestOffer'] = false;
        }
        
        return $retVal;
    }
}
?>