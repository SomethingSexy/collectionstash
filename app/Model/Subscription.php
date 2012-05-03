<?php
/**
 * This model tells us what entities a user is subscribed to
 * 
 * Should we also indicate here, what they are subscribed to? Comments, the entity itself
 * 
 * For example
 * 	- If you are subscribed to the Stash, anytime something is added, you will be notified
 * 	- If you are subscribed to a collectible, anytime something is edited with that collectible, you will be notified.
 * 	- Should you be notified of different comments that are made as well? 
 * 
 * 	- I think if your settings say that you auto suscribe when posting comments, it would just be for the comments so you would suscribe to that entity, but say comments only
 *
 */
class Subscription extends AppModel {
	public $name = 'Subscription';
	public $belongsTo = array('EntityType', 'User' => array('fields' => array('id', 'username', 'email')));
	public $actsAs = array('Containable');

}
