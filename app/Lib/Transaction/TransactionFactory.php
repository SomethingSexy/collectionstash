<?php
App::uses('EbayTransaction', 'Lib/Transaction/Type');
App::uses('ExternalTransaction', 'Lib/Transaction/Type');
App::uses('ExternalTradeTransaction', 'Lib/Transaction/Type');
class TransactionFactory extends Object {

	private $user;

	private $comment;

	private $entity;

	public function __construct() {

		parent::__construct();
	}

	public function getTransaction($transactionTypeId) {
		$retVal = null;

		switch ($transactionTypeId) {
			case 1 :
				$retVal = new EbayTransaction();
				break;
			case 2 :
				$retVal = new ExternalTransaction();
				break;
			case 3 :
				$retVal = new ExternalTradeTransaction();
				break;
		}

		return $retVal;
	}

}
?>