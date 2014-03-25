<?php
class ListingFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = 'Listing';
	public $records = array(
	//
	array('id' => '51b938ad-e988-4011-999e-555d4537ee41', 'user_id' => 1, 'collectible_id' => 1, 'listing_type_id' => 2, 'type' => '', 'listing_price' => '10.00', 'ext_item_id' => '1211067449763434', 'listing_name' => 'balls', 'quantity' => 1, 'quantity_sold' => 0, 'status' => '', 'processed' => false),
	//
	array('id' => '51a3b576-b5fc-422b-8ebf-0e414537ee41', 'user_id' => 1, 'collectible_id' => 5, 'listing_type_id' => 1, 'ext_item_id' => '121106744976', 'type' => 'Store', 'listing_name' => 'joker premium format exclusive sideshow', 'listing_price' => '549.99', 'current_price' => '549.99', 'quantity' => 1, 'quantity_sold' => 1, 'status' => 'completed', 'processed' => true),
	//
	array('id' => '51b938ad-e988-4011-777e-555d4537ee41', 'user_id' => 2, 'collectible_id' => 5, 'listing_type_id' => 2, 'type' => '', 'listing_price' => '10.00', 'ext_item_id' => '', 'listing_name' => 'balls', 'quantity' => 1, 'quantity_sold' => 0, 'status' => '', 'processed' => false));
}
?>