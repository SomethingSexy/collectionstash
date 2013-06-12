<?php

interface Transactionable {
	public function processTransaction($data, $user);
}
?>