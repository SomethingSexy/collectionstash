<?php
App::uses('Sanitize', 'Utility');
/**
 * Need to enable PHP SSL and PHP_SOAP
 */
class TransactionController extends AppController {

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
		$params = array('Version' => 821, 'ItemID' => '230980042238');

		$responseObj = $client -> __soapCall($apiCall, array($params), null, $header);
		// make the API call
		debug($responseObj);
	}

}
?>
