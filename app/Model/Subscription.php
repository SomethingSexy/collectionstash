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
 * 
 * A subscription will indicate what they are subscribing to, the whole entity, comments or both
 * 
 * Because comments don't have their own entity, if someone wants to subscribe to the comments, we will have to code
 * it in a way that they are subscribed to the entity but for the comments only.
 * 
 * Then when a comment has been posted for an entity, we will kick off an event and it will check the subcriptions for
 * that entity that are specific to comments or both
 * 
 */
class Subscription extends AppModel {
	public $name = 'Subscription';
	public $belongsTo = array('EntityType', 'User' => array('fields' => array('id', 'username', 'email')));
	public $actsAs = array('Containable');

}
