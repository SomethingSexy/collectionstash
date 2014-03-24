<?php
class CollectiblesUploadsEditFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = array('table' => 'collectibles_uploads_edits');
	public $records = array(
	//
	array('id' => 1, 'edit_id' => 2, 'edit_user_id' => 1, 'action_id' => 2, 'upload_id' => 1, 'collectible_id' => 2));

}
?>