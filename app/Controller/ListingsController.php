<?php
App::uses('Sanitize', 'Utility');
App::uses('TransactionFactory', 'Lib/Transaction');
/**
 * Need to enable PHP SSL and PHP_SOAP
 */
class ListingsController extends AppController
{
    
    public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');
    /**
     * This is going to do nothing for now.  The page has static text, unless the user is logged in then
     * they will see the catalog page.
     */
    public function index() {
        //
        // $transaction['Listing'] = array();
        // $transaction['Listing']['listing_type_id'] = 1;
        // $transaction['Listing']['ext_item_id'] = '161047990034';
        // $transaction['Listing']['collectible_id'] = '234';
        
        // // $response = $this -> Listing -> createListing($transaction, $this -> getUser());
        // //
        // // debug($response);
        // // first we are going to process it
        // $factory = new TransactionFactory();
        
        // $transactionable = $factory -> getTransaction($transaction['Listing']['listing_type_id']);
        
        // debug($transactionable -> processTransaction($transaction));
        // $transactions = $this->Listing->find('all', array('contain' => array('Transaction'), 'limit' => 100, 'conditions' => array('Listing.processed' => 0, 'Listing.listing_type_id' => 1, 'Listing.end_date <' => date('Y-m-d H:i:s'))));
        // debug($transactions);
    }
    /**
     * This will be used to update and maintain transactions
     *
     * This should handle all types
     */
    public function listing($id = null) {
        $this->autoRender = false;
        // need to be logged in
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        // create
        if ($this->request->isPost()) {
            $transaction = $this->request->input('json_decode', true);
            $transaction = Sanitize::clean($transaction);
            
            $response = $this->Listing->createListing($transaction, $this->getUser());
            
            if (!$response['response']['isSuccess']) {
                if (!empty($response['response']['data'])) {
                    $this->response->statusCode(400);
                    $this->response->body(json_encode($response['response']['data']));
                } else {
                    $this->response->statusCode(500);
                    $this->response->body(json_encode($response));
                }
            } else {
                $this->response->body(json_encode($response['response']['data']));
            }
        } else if ($this->request->isPut()) { // update
            $transaction['Listing'] = $this->request->input('json_decode', true);
            $transaction['Listing'] = Sanitize::clean($transaction['Listing']);
            // no need to clean for now on the update
            $response = $this->Listing->updateListing($transaction, $this->getUser());
            
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
            }
            
            $this->response->body(json_encode($response['response']['data']));
        } else if ($this->request->isDelete()) { // delete
            // have to be a user admin to delete
            if (!$this->isUserAdmin()) {
                $this->response->statusCode(401);
                return;
            }
            // I think it makes sense to use rest delete
            // for changing the status to a delete
            // although I am going to physically delete it
            // not change the status :)
            $response = $this->Listing->remove($id, $this->getUser());
            
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
            }
            
            $this->set('returnData', $response);
        }
    }
    /**
     * This will be used to retrieve multiple transactions, not sure if I will be using this one or not
     */
    public function transactions() {
    }
}
?>
