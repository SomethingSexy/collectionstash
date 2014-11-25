<?php

interface Transactionable {
	/**
	 * @$model - listing model
	 */
	public function createListing($model, $data, $user);
	
	public function updateListing($model, $data, $user);
	/*
	* slowly converting this api to something better.  This will either return object[Listing] or false or object[error] if failed
	*/ 
	public function processTransaction($data, $user);

	public function createTransaction($data, $listing, $user);

	public function updateTransaction($data, $listing, $user);
}
?>