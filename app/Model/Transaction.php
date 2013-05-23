<?php
class Transaction extends AppModel {
	public $name = 'Listing';
	public $belongsTo = array('Collectible', 'Listing');
	public $actsAs = array('Containable');

	public $validate = array();

}
?>
