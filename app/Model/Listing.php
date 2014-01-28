<?php
App::uses('TransactionFactory', 'Lib/Transaction');
class Listing extends AppModel {
	public $name = 'Listing';
	public $belongsTo = array('Collectible', 'User', 'CollectiblesUser');
	public $hasMany = array('Transaction' => array('dependent' => true));
	public $actsAs = array('Containable');

	//TODO: need to check duplicates
	public $validate = array(
	//
	'ext_item_id' => array('maxLength' => array('rule' => array('maxLength', 200), 'required' => true, 'allowEmpty' => false, 'message' => 'Item is required and cannot be more than 200 characters.')),
	//
	'listing_type_id' => array('rule' => 'numeric', 'allowEmpty' => false, 'required' => true, 'message' => 'Must be a valid listing type.'),
	// this is only needed when deleting or selling
	'listing_price' => array('rule' => array('money', 'left'), 'allowEmpty' => true, 'message' => 'Please supply a valid monetary amount.'),
	//traded for, only needed when deleting or selling
	'traded_for' => array('maxLength' => array('rule' => array('maxLength', 1000), 'allowEmpty' => true, 'message' => 'Traded for must be less than 1000 characters.')),
	//
	'dups' => array('rule' => array('checkDuplicateItems'), 'message' => 'A listing with that item has already been added.'));

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
						// this should go in the database somewhere but for now add here
						// a mapping of collectible_user_remove_reason_id
						if ($val['Listing']['listing_type_id'] === '1' || $val['Listing']['listing_type_id'] === '2') {
							$results[$key]['Listing']['collectible_user_remove_reason_id'] = 1;
						} else if ($val['Listing']['listing_type_id'] === '2') {
							$results[$key]['Listing']['collectible_user_remove_reason_id'] = 3;
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
					// this should go in the database somewhere but for now add here
					// a mapping of collectible_user_remove_reason_id
					if ($results['listing_type_id'] === '1' || $results['listing_type_id'] === '2') {
						$results['collectible_user_remove_reason_id'] = 1;
					} else if ($results['listing_type_id'] === '2') {
						$results['collectible_user_remove_reason_id'] = 3;
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
							// this should go in the database somewhere but for now add here
							// a mapping of collectible_user_remove_reason_id
							if (isset($val['Listing']['listing_type_id'])) {
								if ($val['Listing']['listing_type_id'] === '1' || $val['Listing']['listing_type_id'] === '2') {
									$results[$key]['Listing']['collectible_user_remove_reason_id'] = 1;
								} else if ($val['Listing']['listing_type_id'] === '3') {
									$results[$key]['Listing']['collectible_user_remove_reason_id'] = 2;
								}
							}

						}
					}
				}
			}

		}

		return $results;
	}

	function checkDuplicateItems($check) {
		// we need these to proceed
		if (empty($check['ext_item_id']) || empty($this -> data['Listing']['listing_type_id']) || empty($this -> data['Listing']['collectible_id'])) {
			return false;
		}

		$count = $this -> find('count', array('contain' => false, 'conditions' => array('Listing.collectible_id' => $this -> data['Listing']['collectible_id'], 'Listing.ext_item_id' => $check['ext_item_id'], 'Listing.listing_type_id' => $this -> data['Listing']['listing_type_id'])));
		// return true if none found
		return $count === 0;
	}

	/**
	 *
	 */
	public function remove($id, $user) {
		$retVal = $this -> buildDefaultResponse();

		if ($this -> delete($id, true)) {
			$retVal['response']['isSuccess'] = true;
		} else {
			$retVal['response']['isSuccess'] = false;
			array_push($retVal['response']['errors'], array('message' => __('Invalid request.')));
		}

		return $retVal;
	}

	/**
	 * listing_Type_id = 1 eBay
	 * listing_type_id = 2 personal sell
	 * listing_type_id = 3 personal trade
	 */
	public function createListing($data, $user) {
		$retVal = $this -> buildDefaultResponse();

		$data['Listing']['user_id'] = $user['User']['id'];

		// if it is 2 or 3 and it is marked as a sale then they are required
		debug($data);
		if (isset($data['Listing']['listing_type_id'])) {
			if ($data['Listing']['listing_type_id'] == 1) {
				$this -> validate['listing_price']['allowEmpty'] = true;
				$this -> validate['listing_price']['required'] = false;
				$this -> validate['traded_for']['maxLength']['allowEmpty'] = true;
				$this -> validate['traded_for']['maxLength']['required'] = false;
			} else if ($data['Listing']['listing_type_id'] == 2 && $data['Listing']['active_sale']) {
				$this -> validate['listing_price']['allowEmpty'] = false;
				$this -> validate['listing_price']['required'] = true;
				$this -> validate['ext_item_id']['maxLength']['allowEmpty'] = true;
				$this -> validate['ext_item_id']['maxLength']['required'] = false;
				unset($this -> validate['dups']);
			} else if ($data['Listing']['listing_type_id'] == 3 && $data['Listing']['active_sale']) {
				$this -> validate['traded_for']['maxLength']['allowEmpty'] = false;
				$this -> validate['traded_for']['maxLength']['required'] = true;
				$this -> validate['ext_item_id']['maxLength']['allowEmpty'] = true;
				$this -> validate['ext_item_id']['maxLength']['required'] = false;
				unset($this -> validate['dups']);
			}
		}

		$this -> set($data['Listing']);

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
		$data = $transactionable -> processTransaction($data, $user);

		if (!$data) {
			$retVal['response']['isSuccess'] = false;
			$errors = array();
			$error = array();
			$error['message'] = __('There was an error retrieving the listing, either it did not exist or it is too old.');
			$error['inline'] = false;
			array_push($errors, $error);

			$retVal['response']['errors'] = $errors;

			return $retVal;
		}

		if ($this -> saveAssociated($data, array('validate' => false))) {
			$transactionId = $this -> id;
			$transaction = $this -> find('first', array('contain' => array('User', 'Transaction', 'Collectible'), 'conditions' => array('Listing.id' => $transactionId)));

			// As of now, we just need to the id but we
			// can expand this later to return more if necessary
			$retVal['response']['data'] = $transaction['Listing'];
			$retVal['response']['data']['User'] = $transaction['User'];
			$retVal['response']['data']['Transaction'] = $transaction['Transaction'];
			$retVal['response']['isSuccess'] = true;
			// since we can only add attributes through collectibles right
			// now, do not do any event stuff here
			$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$USER_ADD_LISTING, 'user' => $user, 'object' => $transaction, 'type' => 'Listing')));
			// if this is a relisting, take the relist id, if it hasn't been added
			// already then we want
			if (isset($data['Listing']['relisted']) && $data['Listing']['relisted'] && $data['Listing']['relisted_ext_id']) {

				$relisting = array();
				$relisting['Listing']['listing_type_id'] = $data['Listing']['listing_type_id'];
				$relisting['Listing']['collectible_id'] = $data['Listing']['collectible_id'];
				$relisting['Listing']['user_id'] = $user['User']['id'];
				$relisting['Listing']['ext_item_id'] = $data['Listing']['relisted_ext_id'];

				$this -> set($relisting['Listing']);

				if ($this -> validates()) {
					$relisting = $transactionable -> processTransaction($relisting, $user);
					// set this guy to false so it will be processed later, this is for the rare
					// cases of a relisting of a relisting
					$relisting['Listing']['processed'] = false;
					// save but don't worry about it failing for now
					$this -> saveAssociated($relisting);
				}
			}

		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Listing');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	/**
	 *
	 */
	public function updateListing($data, $user) {
		$retVal = $this -> buildDefaultResponse();

		// double equals is here on purpose
		if ($data['Listing']['listing_type_id'] == 1) {
			$this -> id = $data['Listing']['id'];
			$this -> saveField('flagged', $data['Listing']['flagged']);

			$retVal['response']['data'] = $data;
			$retVal['response']['isSuccess'] = true;

			return $retVal;
		} else {
			// this would only be able to update the listing_price and traded for
			
			// otherwise we should be checking for permissions here
			if ($this -> save($data, array('validate' => false))) {
				$retVal['response']['isSuccess'] = true;
			}
		}

		return $retVal;
	}

	/**
	 * Running the api through Listing so it is all contained here
	 */
	public function updateTransaction($data, $listing, $user) {
		$retVal = $this -> buildDefaultResponse();
		// grab our transaction type
		$factory = new TransactionFactory();

		$transactionable = $factory -> getTransaction($listing['Listing']['listing_type_id']);

		// if there is no transaction right now we need to create one, otherwise update
		// an existing one
		if (empty($listing['Listing']['Transaction'])) {
			$data = $transactionable -> createTransaction($data, $listing, $user);
		} else {
			$data = $transactionable -> updateTransaction($data, $listing, $user);
		}

		if ($this -> Transaction -> save($data, array('validate' => false))) {
			$retVal['response']['isSuccess'] = true;
		}

		return $retVal;
	}

	/**
	 * Running the api through Listing so it is all contained here
	 */
	public function createTransaction($data) {
		$retVal = $this -> buildDefaultResponse();
		// grab our transaction type
		$factory = new TransactionFactory();

		$transactionable = $factory -> getTransaction($listing['Listing']['listing_type_id']);

		$data = $transactionable -> createTransaction($data, $listing, $user);

		if ($this -> Transaction -> save($data, array('validate' => false))) {
			$retVal['response']['isSuccess'] = true;
		}

		return $retVal;

	}

}
?>
