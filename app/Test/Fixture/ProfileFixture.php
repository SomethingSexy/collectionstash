<?php
class ProfileFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = 'Profile';
	public $records = array(
	// base collectible, no variant
	array('id' => 1, 'user_id' => 1, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'));
}
?>