<?php
/**
 *
 *
 */
class Subscription extends AppModel {
	public $name = 'Subscription';
	public $belongsTo = array('EntityType', 'User' => array('fields' => array('id', 'username')));
	public $actsAs = array('Containable');

}
