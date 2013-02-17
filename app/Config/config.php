<?php
$config['Settings'] = Configure::read('Settings');

$config['Settings'] = Set::merge($config['Settings'], array(
	'version' => '2.2',
  	'title' => 'My Application',
  	'registration' => array(
  		'open' => true,
  		'invite-only' => false
	),
	'Profile' => array (
		'total-invites-allowed' => 5,
		'total-admin-invites-allowed' => 9999,
		'allow-invites' => true
	),
  	'Approval' => array (
		'auto-approve' => 'false'
  	),
  	'Stash' => array (
		'total-allowed' => '1'
  	),
  	'Search' => array (
		'list-size' => 25	
  	),
  	'Collectible' => array (
		'Edit' => array (
			'allowed' => true,
			'auto-approve' => false	
		),
		'Contribute' => array(
			'allowed'=> true
		)	
  	),
	'User' => array(
		'uploads' => array(
			'allowed' => true,
			'root-folder' => 'user_uploads',
			'total-allowed' => 100
		)
	),
	'CollectibleTypes' => array(
		'Print' => '10'
	)
));

?>