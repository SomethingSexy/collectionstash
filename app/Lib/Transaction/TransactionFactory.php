<?php
App::uses('EbayTransaction', 'Lib/Transaction/Type');
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
				echo "i equals 2";
				break;
		}

		return $retVal;
	}

}
?>