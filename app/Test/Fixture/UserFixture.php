<?php
class UserFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = 'User';
	public $records = array(
	// base collectible, no variant
	array('id' => 1, 'username' => 'CollectibleStash', 'password' => 'balls', 'first_name' => 'Tyler', 'last_name' => 'Cvetan', 'email' => 'tyler.cvetan@gmail.com', 'admin' => true, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'),
	array('id' => 2, 'username' => 'Balls', 'password' => 'balls', 'first_name' => 'Tyler', 'last_name' => 'Cvetan', 'email' => 'tyler.cvetan@gmail.com', 'admin' => false, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'));
}
?>