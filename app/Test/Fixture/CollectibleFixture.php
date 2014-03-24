<?php
class CollectibleFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = 'Collectible';
	public $records = array(
	// base collectible, no variant
	array('id' => 1, 'entity_type_id' => '1', 'revision_id' => 1, 'user_id' => 1, 'status_id' => 4, 'name' => 'Test Collectible', 'manufacture_id' => 1, 'collectibletype_id' => 1, 'license_id' => 1, 'description' => 'Test Collectible Desc', 'variant' => false, 'exclusive' => false, 'variant_collectible_id' => 0, 'numbered' => false, 'original' => false, 'custom' => false),

	// this collectible will be linked to edits
	array('id' => 2, 'entity_type_id' => '2', 'revision_id' => 2, 'user_id' => 1, 'status_id' => 4, 'name' => 'Test Collectible', 'manufacture_id' => 1, 'collectibletype_id' => 1, 'license_id' => 1, 'description' => 'Test Collectible Desc', 'variant' => false, 'exclusive' => false, 'variant_collectible_id' => 0, 'numbered' => false, 'original' => false, 'custom' => false));
}
?>