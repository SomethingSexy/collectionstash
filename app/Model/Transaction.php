<?php
class Transaction extends AppModel {
	public $name = 'Transaction';
	public $belongsTo = array('Collectible', 'Listing');
	public $actsAs = array('Containable');

	public $validate = array();

}
?>
