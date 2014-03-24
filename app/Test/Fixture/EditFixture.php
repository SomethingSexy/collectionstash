<?php
class EditFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = 'Edit';
	public $records = array(
	// for collectible
	array('id' => 1, 'user_id' => 1, 'status' => '0'),
	// collectibles upload
	array('id' => 2, 'user_id' => 1, 'status' => '0'),
	// attributes collectible
	array('id' => 3, 'user_id' => 1, 'status' => '0'),
	// artists collectible
	array('id' => 4, 'user_id' => 1, 'status' => '0'),
	// collectibles tag
	array('id' => 5, 'user_id' => 1, 'status' => '0'));
}
?>