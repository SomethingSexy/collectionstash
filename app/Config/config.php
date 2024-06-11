<?php
$config['Settings'] = Configure::read('Settings');

$config['Settings'] = Set::merge($config['Settings'], array(
	'version' => '4.2.0',
  	'title' => 'My Application',
  	'domain' => 'http://collectionstash.com',
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
			'auto-approve' => false,
			'allow-import' => true
		),
		'Contribute' => array(
			'allowed'=> true
		),
		'upload-directory' => 'files'	
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
	),
	'TransactionManager' => array(
		'enabled' => true,
		'eBay' => array(
			'version' => '821',
			'api_endpoint'=> 'https://api.ebay.com/ws/api.dll', // 'https://api.sandbox.ebay.com/ws/api.dll'
			'site_id' => 0,
			'auth_token' => '',
                        'DEVID' => '',
			'AppID' => '',
			'CertID'=> '',
			'campid' => ''
		)
	),
	'Layout' => array(
		'Static' => array(
			'collection_stash_documentation' => 'fluid',
			'change_log' => 'fluid'
		)
	
	), 
	'Twitter' => array(
		'name' => ''
	)
));

?>
