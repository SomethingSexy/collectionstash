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
		debug($this -> data);
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
	 * Used for updating, the only thing you can update right now from here
	 * is the flagged
	 */
	public function updatetListing($data, $user) {
		$retVal = $this -> buildDefaultResponse();

		$this -> id = $data['id'];
		$this -> saveField('flagged', $data['flagged']);

		$retVal['response']['data'] = $data;
		$retVal['response']['isSuccess'] = true;

		return $retVal;
	}

	/**
	 * listing_Type_id = 1 eBay
	 * listing_type_id = 2 external
	 * listing_type_id = 3 internal
	 */
	public function createListing($data, $user) {
		$retVal = $this -> buildDefaultResponse();

		$data['Listing']['user_id'] = $user['User']['id'];
		// right now we only support eBay which will be 1
		if (isset($data['Listing']['listing_type_id']) && !empty($data['Listing']['listing_type_id'])) {

		} else {
			$data['Listing']['listing_type_id'] = 1;
		}

		if ($data['Listing']['listing_type_id'] === 1) {
			$this -> set($data['Listing']);

			// Validate first
			if (!$this -> validates()) {
				$retVal['response']['isSuccess'] = false;
				$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Listing');
				$retVal['response']['errors'] = $errors;
				return $retVal;
			}
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
			debug($data['Listing']['relisted']);
			debug($data['Listing']['relisted_ext_id']);
			debug($this -> checkDuplicateItems(array('ext_item_id' => $data['Listing']['relisted_ext_id'])));
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

}
?>
