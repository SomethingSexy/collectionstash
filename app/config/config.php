<?php
$config['Settings'] = Configure::read('Settings');

$config['Settings'] = Set::merge(ife(empty($config['Settings']), array(), $config['Settings']), array(
	'version' => '1.0.213',
  	'title' => 'My Application',
  	'registration' => array(
  		'open' => true,
  		'invite-only' => true
	),
	'Profile' => array (
		'total-invites-allowed' => 5,
		'total-admin-invites-allowed' => 9999,
		'allow-invites' => true
	),
  	'Approval' => array (
		'auto-approve' => 'true'
  	),
  	'Stash' => array (
		'total-allowed' => '1'
  	),
  	'Search' => array (
		'list-size' => 25	
  	),
  	'Collectible' => array (
		'Edit' => array (
			'auto-approve' => true	
		)	
  	)
));

?>