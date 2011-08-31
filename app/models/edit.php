<?php
class Edit extends AppModel {
	var $name = 'Edit';
	var $actsAs = array('Containable');
	var $belongsTo = array('CollectibleEdit', 'User' => array('counterCache' => true), 'UploadEdit', 'Collectible');

}
?>
