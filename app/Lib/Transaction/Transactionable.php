<?php

interface Transactionable {
	/**
	 * @$model - listing model
	 */
	public function createListing($model, $data, $user);
	
	public function processTransaction($data, $user);

	public function createTransaction($data, $listing, $user);

	public function updateTransaction($data, $listing, $user);
}
?>