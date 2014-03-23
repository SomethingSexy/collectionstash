<?php
class EntityTypeFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = array('model' => 'EntityType');
	public $records = array( array('id' => 1, 'type' => 'collectible', 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'));
}
?>