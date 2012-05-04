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
 *  I might go back to calling this notifications
 * 		- Not sure I want to confuse this when I create a message center/conversation center
 * 		- You would get notified of a new message in your message center or a new conversation
 */
class Notification extends AppModel {
	public $name = 'Message';
	public $belongsTo = array('User' => array('fields' => array('id', 'username', 'email')));
	public $actsAs = array('Containable');

}
