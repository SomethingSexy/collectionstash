<?php

interface Transactionable {
	public function processTransaction($data, $user);

	public function createTransaction($data, $listing, $user);

	public function updateTransaction($data, $listing, $user);
}
?>