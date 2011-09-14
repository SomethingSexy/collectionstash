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
		'allow-invites' => false
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
  	'Email' => array(
		'username' => 'admin@collectionstash.com',
		'from' => 'Collection Stash <admin@collectionstash.com>',
		'password' => 'oblivion1968',
		'host' => 'smtpout.secureserver.net',
		'port' => '25',
		'timeout' => '30'
	)
));

?>