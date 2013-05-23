<?php
App::uses('TransactionFactory', 'Lib/Transaction');
class Listing extends AppModel {
	public $name = 'Listing';
	public $belongsTo = array('Collectible', 'User');
	public $hasMany = array('Transaction' => array('dependent' => true));
	public $actsAs = array('Containable');

	//TODO: need to check duplicates
	public $validate = array('ext_item_id' => array('maxLength' => array('rule' => array('maxLength', 200), 'required' => true, 'allowEmpty' => false, 'message' => 'Item is required and cannot be more than 200 characters.'), 'dups' => array('rule' => array('checkDuplicateItems'), 'message' => 'A listing with that item has already been added.')));

	function afterFind($results, $primary = false) {
		if ($results) {
			// If it is primary handle all of these things
			if ($primary) {
				foreach ($results as $key => $val) {
					if (isset($val['Listing'])) {
						if (isset($val['Listing']['start_date'])) {
							$datetime = strtotime($val['Listing']['start_date']);
							$datetime = date("m/d/y g:i A", $datetime);
							$results[$key]['Listing']['start_date'] = $datetime;
						}
						if (isset($val['Listing']['end_date'])) {
							$datetime = strtotime($val['Listing']['end_date']);
							$datetime = date("m/d/y g:i A", $datetime);
							$results[$key]['Listing']['end_date'] = $datetime;
						}
					}
				}
			} else {
				if (isset($results[$this -> primaryKey])) {

					if (isset($results['start_date'])) {
						$datetime = strtotime($results['start_date']);
						$datetime = date("m/d/y g:i A", $datetime);
						$results['start_date'] = $datetime;
					}
					if (isset($results['end_date'])) {
						$datetime = strtotime($results['end_date']);
						$datetime = date("m/d/y g:i A", $datetime);
						$results['end_date'] = $datetime;
					}
				} else {

					foreach ($results as $key => $val) {

						if (isset($val['Listing'])) {
							if (isset($val['Listing']['start_date'])) {
								$datetime = strtotime($val['Listing']['start_date']);
								$datetime = date("m/d/y g:i A", $datetime);
								$results[$key]['Listing']['start_date'] = $datetime;
							}
							if (isset($val['Listing']['end_date'])) {
								$datetime = strtotime($val['Listing']['end_date']);
								$datetime = date("m/d/y g:i A", $datetime);
								$results[$key]['Listing']['end_date'] = $datetime;
							}
						}
					}
				}
			}

		}
		return $results;
	}

	function checkDuplicateItems($check) {
		debug($check);
		// we need these to proceed
		if (empty($check['ext_item_id']) || empty($this -> data['Listing']['listing_type_id']) || empty($this -> data['Listing']['collectible_id'])) {
			return false;
		}

		$count = $this -> find('count', array('contain' => false, 'conditions' => array('Listing.collectible_id' => $this -> data['Listing']['collectible_id'], 'Listing.ext_item_id' => $check['ext_item_id'], 'Listing.listing_type_id' => $this -> data['Listing']['listing_type_id'])));
		debug($count);
		// return true if none found
		return $count === 0;
	}

	public function createListing($data, $user) {
		$retVal = $this -> buildDefaultResponse();

		$data['Listing']['user_id'] = $user['User']['id'];
		// right now we only support eBay which will be 1
		$data['Listing']['listing_type_id'] = 1;

		$this -> set($data['Listing']);
		debug($this -> data);
		// Validate first
		if (!$this -> validates()) {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Listing');
			$retVal['response']['errors'] = $errors;
			return $retVal;
		}

		$factory = new TransactionFactory();

		$transactionable = $factory -> getTransaction($data['Listing']['listing_type_id']);

		// TODO: If it comes back with an error, do not save and send error message to user
		$data = $transactionable -> processTransaction($data);

		debug($data);

		if (!$data) {
			$retVal['response']['isSuccess'] = false;
			$errors = array();
			$error = array();
			$error['message'] = __('There was an error retrieving the item or the item was too old.');
			$error['inline'] = false;
			array_push($errors, $error);

			$retVal['response']['errors'] = $errors;

			return $retVal;
		}

		if ($this -> saveAssociated($data)) {
			$transactionId = $this -> id;
			$transaction = $this -> find('first', array('contain' => array('User', 'Transaction'), 'conditions' => array('Listing.id' => $transactionId)));
			// As of now, we just need to the id but we
			// can expand this later to return more if necessary
			$retVal['response']['data'] = $transaction['Listing'];
			$retVal['response']['data']['User'] = $transaction['User'];
			$retVal['response']['data']['Transaction'] = $transaction['Transaction'];
			$retVal['response']['isSuccess'] = true;
			// since we can only add attributes through collectibles right
			// now, do not do any event stuff here
		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Listing');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

}
?>
