<?php
$config['Settings'] = Configure::read('Settings');

$config['Settings'] = Set::merge($config['Settings'], array(
	'version' => '2.12.2',
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
	),
	'TransactionManager' => array(
		'enabled' => true,
		'eBay' => array(
			'version' => '821',
			'api_endpoint'=> 'https://api.ebay.com/ws/api.dll', // 'https://api.sandbox.ebay.com/ws/api.dll'
			'site_id' => 0,
			// this is for TESTUSER_cvetan pw god222
			'auth_token' => 'AgAAAA**AQAAAA**aAAAAA**7bGaUQ**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFkYulD5aHoAudj6x9nY+seQ**odcBAA**AAMAAA**/DJhI5+iMb9VFE2P+nNY3vbw4ItBJx2tjFXysdQr8pXWwKpYpDmH4Nq84zIVaL8xh4V0mgpA/gRSwWjbQScrIOi187JEyU5k+7eLrBGPG/rsR11/1Bf6u39LYmc51xSKDbbW5gTgR/Z+3qQ7YjnnD/KN0lrXVbj5N2TGYCDhatt0ttQxJMo14dSWU7kPLd6CC/opSYOIq9O/dRh2PRU6CxqrLL4DzfQt93cyU/T545Hhgh52FcYxv8MYI6uOk52cd9VX91/gpQgkfgGo4NJNuXTXTychE6r0YnpjLwH3y6sTR4FlPjoY9cG3S6T8YHVzhkBv9qW3RKHpOuNpOW4KAZ1ye9agPfhMuxLA6FoDeRpdLBRISVZd8p8DBRF+4gPaPYO6XcZmgqfEAJj3kXnwF0IQ0S2IxvqM8FuF6OH10G+2tsZ5vzue2V9XEQr8BE+5AcGz/nfbcUDb9xGJdCzXMANiZGT6F/P2eNg68JRkEgSRu9K2LlR2L05nWGJqLcj5CyI7q64WOPwDzc+14LJucOsI0MUG9aoZisCveXMdpUIEd/n8/alwgZ9u2B1XXfEm8v4ygNIWF2VyWc+CeEA9AueaaWWr6t2wnwjM/3Ec2HQ59xX0fAC2mMJSl0BBjtyRz530/Zu1kdvJc/dBdp89tjR5z6XoXm5K5TRcAQFLThh5G0EgGy+ldgTW47lunyw0bGkYMYKT8lbVYn48DMeXzv1MKSVTkBtB7j9bcqK+UTSlrQmAUef58GsDYNv4WmeD',
			'DEVID' => '01a7f22e-6659-4d2b-bf79-fff2e4868c47',
			'AppID' => 'Collecti-fc7c-44cb-b604-478517afbe7f',
			'CertID'=> '542ce264-1ee9-40f7-a06b-2ccf5b57c442'
		)
	),
	'Layout' => array(
		'Static' => array(
			'collection_stash_documentation' => 'fluid',
			'change_log' => 'fluid'
		)
	
	), 
	'Twitter' => array(
		'name' => 'collectionstash'
	)
));

?>