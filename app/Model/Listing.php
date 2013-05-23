<?php
class Listing extends AppModel {
	public $name = 'Listing';
	public $belongsTo = array('Collectible', 'User');
	public $hasMany = array('Transaction' => array('dependent' => true));
	public $actsAs = array('Containable');

	public $validate = array('ext_transaction_id' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Transaction Id is required.'), 'maxLength' => array('rule' => array('maxLength', 200), 'message' => 'Invalid length.')));

	public function createListing($data, $user) {
		$retVal = $this -> buildDefaultResponse();

		$data['Listing']['user_id'] = $user['User']['id'];
		// right now we only support eBay which will be 1
		$data['Listing']['listing_type_id'] = 1;

		if ($this -> save($data)) {
			$transactionId = $this -> id;
			$transaction = $this -> find('first', array('contain' => array('User'), 'conditions' => array('Listing.id' => $transactionId)));
			// As of now, we just need to the id but we
			// can expand this later to return more if necessary
			$retVal['response']['data'] = $transaction['Listing'];
			$retVal['response']['data']['User'] = $transaction['User'];
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
