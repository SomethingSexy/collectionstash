<?php
/**
 *
 *
 */
class Notification extends AppModel {
	public $name = 'Notification';
	public $belongsTo = array('User' => array('fields' => array('id', 'username', 'email')));
	public $actsAs = array('Containable');

}
