<?php
class Edit extends AppModel {
	var $name = 'Edit';
	var $actsAs = array('Containable');
	var $belongsTo = array('CollectibleEdit', 'User', 'UploadEdit');

	// function doAfterFind($results) {
// 
		// debug($results);
		// return $results;
	// }

	// function afterFind($results) {
// 
		// debug($results);
		// return $results;
	// }

}
?>
