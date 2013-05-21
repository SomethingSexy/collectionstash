<?php
class Transaction extends AppModel {
	public $name = 'Transaction';
	public $belongsTo = array('Collectible');
	public $actsAs = array('Containable');

	public $validate = array();

}
?>
