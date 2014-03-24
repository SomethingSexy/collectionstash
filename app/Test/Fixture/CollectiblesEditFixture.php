<?php
class CollectiblesEditFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = array('table' => 'collectibles_edits');
	public $records = array(
	//
	array('id' => 1, 'edit_id' => 1, 'edit_user_id' => 1, 'base_id' => 2, 'action_id' => 1, 'name'=> 'balls'));
}
?>