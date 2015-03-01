<?php
class UserSubscription extends AppModel {
	public $name = 'UserSubscription';
	public $belongsTo = array('User', 'Subscription');
	public $actsAs = array('Containable');
}
?>