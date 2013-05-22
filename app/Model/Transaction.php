<?php
class Transaction extends AppModel {
	public $name = 'Transaction';
	public $belongsTo = array('Collectible', 'User');
	public $actsAs = array('Containable');

	public $validate = array('ext_transaction_id' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Transaction Id is required.'), 'maxLength' => array('rule' => array('maxLength', 200), 'message' => 'Invalid length.')));

	public function createTransaction($data, $user) {
		$retVal = $this -> buildDefaultResponse();

		$data['Transaction']['user_id'] = $user['User']['id'];
		// right now we only support eBay which will be 1
		$data['Transaction']['transaction_type_id'] = 1;
		if ($this -> save($data)) {
			$transactionId = $this -> id;
			$transaction = $this -> find('first', array('contain' => false, 'conditions' => array('Transaction.id' => $transactionId)));
			// As of now, we just need to the id but we
			// can expand this later to return more if necessary
			$retVal['response']['data'] = $transaction['Transaction'];
			$retVal['response']['isSuccess'] = true;
			// since we can only add attributes through collectibles right
			// now, do not do any event stuff here
		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Transaction');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

}
?>
