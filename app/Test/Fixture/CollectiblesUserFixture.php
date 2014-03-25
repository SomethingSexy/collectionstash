<?php
class CollectiblesUserFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = 'CollectiblesUser';
	public $records = array(
	//
	array('id' => 1, 'user_id' => 1, 'collectible_id' => 1, 'stash_id' => 1, 'listing_id' => '51b938ad-e988-4011-999e-555d4537ee41', 'sale' => true), 
	//
	array('id' => 2, 'user_id' => 2, 'collectible_id' => 5, 'stash_id' => 2, 'sale' => false),
	
	array('id' => 3, 'user_id' => 2, 'collectible_id' => 5, 'stash_id' => 2, 'listing_id' => '51b938ad-e988-4011-777e-555d4537ee41', 'sale' => true)
	);
}
?>