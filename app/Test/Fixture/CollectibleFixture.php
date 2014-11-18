<?php
class CollectibleFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = 'Collectible';
	public $records = array(
	// base collectible, no variant
	array('id' => 1, 'entity_type_id' => '1', 'revision_id' => 1, 'user_id' => 1, 'status_id' => 4, 'name' => 'Test Collectible', 'manufacture_id' => 1, 'collectibletype_id' => 1, 'license_id' => 1, 'description' => 'Test Collectible Desc', 'variant' => false, 'exclusive' => false, 'variant_collectible_id' => 0, 'numbered' => false, 'original' => false, 'custom' => false, 'collectible_price_fact_id' => 1, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'),
	// this collectible will be linked to edits
	array('id' => 2, 'entity_type_id' => '2', 'revision_id' => 2, 'user_id' => 1, 'status_id' => 4, 'name' => 'Test Collectible', 'manufacture_id' => 1, 'collectibletype_id' => 1, 'license_id' => 1, 'description' => 'Test Collectible Desc', 'variant' => false, 'exclusive' => false, 'variant_collectible_id' => 0, 'numbered' => false, 'original' => false, 'custom' => false, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'),
	// parent collectible
	array('id' => 3, 'entity_type_id' => '3', 'revision_id' => 3, 'user_id' => 1, 'status_id' => 4, 'name' => 'Test Collectible Parent', 'manufacture_id' => 1, 'collectibletype_id' => 1, 'license_id' => 1, 'description' => 'Test Collectible Desc', 'variant' => false, 'exclusive' => false, 'variant_collectible_id' => 0, 'numbered' => false, 'original' => false, 'custom' => false, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'),
	// variant collectible
	array('id' => 4, 'entity_type_id' => '4', 'revision_id' => 4, 'user_id' => 1, 'status_id' => 4, 'name' => 'Test Collectible Variant', 'manufacture_id' => 1, 'collectibletype_id' => 1, 'license_id' => 1, 'description' => 'Test Collectible Desc', 'variant' => true, 'exclusive' => false, 'variant_collectible_id' => 3, 'numbered' => false, 'original' => false, 'custom' => false, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'),
	// base collectible to get deleted and replaced
	array('id' => 5, 'entity_type_id' => '5', 'revision_id' => 5, 'user_id' => 1, 'status_id' => 4, 'name' => 'Test Collectible Replace', 'manufacture_id' => 1, 'collectibletype_id' => 1, 'license_id' => 1, 'description' => 'Test Collectible Desc', 'variant' => false, 'exclusive' => false, 'variant_collectible_id' => 0, 'numbered' => false, 'original' => false, 'custom' => false, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'),
	// replacement collectible
	array('id' => 6, 'entity_type_id' => '6', 'revision_id' => 6, 'user_id' => 1, 'status_id' => 4, 'name' => 'Test Collectible Replacement', 'manufacture_id' => 1, 'collectibletype_id' => 1, 'license_id' => 1, 'description' => 'Test Collectible Desc', 'variant' => false, 'exclusive' => false, 'variant_collectible_id' => 0, 'numbered' => false, 'original' => false, 'custom' => false, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'),
	// this is a variant of collectible 5 which will be deleted and replaced by 6
	array('id' => 7, 'entity_type_id' => '7', 'revision_id' => 7, 'user_id' => 1, 'status_id' => 4, 'name' => 'Test Collectible Replace Variant', 'manufacture_id' => 1, 'collectibletype_id' => 1, 'license_id' => 1, 'description' => 'Test Collectible Desc', 'variant' => true, 'exclusive' => false, 'variant_collectible_id' => 5, 'numbered' => false, 'original' => false, 'custom' => false, 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'));
}
?>