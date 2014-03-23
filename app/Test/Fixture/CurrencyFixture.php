<?php
class CurrencyFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = array('model' => 'Currency', 'records' => true);
}
?>