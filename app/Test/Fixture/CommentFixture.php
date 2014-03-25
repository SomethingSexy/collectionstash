<?php
class CommentFixture extends CakeTestFixture {

	// Optional.
	// Set this property to load fixtures to a different test datasource
	public $useDbConfig = 'test';
	public $import = 'Comment';
	public $records = array(
	//
	array('id' => 1, 'entity_type_id' => 1, 'user_id' => 2, 'comment' => 'heyo', 'created' => '2007-03-18 10:39:23', 'modified' => '2007-03-18 10:41:31'));
}
?>