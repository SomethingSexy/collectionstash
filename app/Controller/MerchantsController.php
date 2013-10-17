<?php
class MerchantsController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	public function getMerchantList() {
		$query = $this -> request -> query['query'];
		$merchants = $this -> Merchant -> find('list', array('fields' => array('Merchant.id', 'Merchant.name'), 'conditions' => array('Merchant.name LIKE' => $query . '%')));
		$keys = array_keys($merchants);
		$values = array_values($merchants);
		$this -> set('returnData', $values);
	}

}
?>