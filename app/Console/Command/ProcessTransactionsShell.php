<?php
/**
 * This will process transactions
 *
 *
 * This will run once every hour.
 *
 * It will looking for any listings that have not finished processing and process them if it can.
 *
 * It will have to sync up any transactions as well.
 *
 * TODO: This should also handle updating end date times, for cases when it is buy it now that just keeps getting extended
 *
 */
App::uses('TransactionFactory', 'Lib/Transaction');
class ProcessTransactionsShell extends AppShell {
	public $uses = array('Listing');

	public function main() {

		if (Configure::read('Settings.TransactionManager.enabled')) {
			// first get all pending transactions

			$factory = new TransactionFactory();

			// we will handle 100 for now and this will run once an hour.
			// processed is 0 and end date is less than current date
			$transactions = $this -> Listing -> find('all', array('contain' => array('Transaction'), 'limit' => 100, 'conditions' => array('Listing.processed' => 0, 'Listing.end_date <' => date('Y-m-d H:i:s'))));

			foreach ($transactions as $key => $value) {

				$transactionable = $factory -> getTransaction($value['Listing']['listing_type_id']);

				// make sure it isn't null
				if (!is_null($transactionable)) {

					// the processedListing will contain
					$processedListing = $transactionable -> processTransaction($value, array());
					//make sure it isn't false.  We need to do something that if we cannot update, indicate that on the listing - TODO
					if ($processedListing) {						
						// now since this should have all of the ids already saved, I should be able
						// to do a saveAssociated and whamo
						if ($this -> Listing -> saveAssociated($processedListing, array('validate' => false))) {
					
						}
					}
				}
			}
		}

	}

}
?>