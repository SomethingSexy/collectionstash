<?php
/**
 * This is kind of like a mesasge center actually.  It will contain notifications and messages
 * to the user.
 * 
 * Where should I store notification settings?
 *	
 * 
 * 	- How do you want to be notified?
 * 		- email
 * 		- message
 * 		- or is message always on and you can't turn that off? Then maybe this should be a message object and the notification object tells me how to notify the user
 * 
 *
 */
class Message extends AppModel {
	public $name = 'Message';
	public $belongsTo = array('User' => array('fields' => array('id', 'username', 'email')));
	public $actsAs = array('Containable');

}
