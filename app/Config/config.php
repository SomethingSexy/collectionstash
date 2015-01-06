<?php
$config['Settings'] = Configure::read('Settings');

$config['Settings'] = Set::merge($config['Settings'], array(
	'version' => '3.7.0',
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
			'auth_token' => 'AgAAAA**AQAAAA**aAAAAA**hRBpVA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFkYulD5aHoAudj6x9nY+seQ**odcBAA**AAMAAA**dR+S6uivWJQiQDDBOU/PHiW/n+RvLWfBqqmmJFGW91VNQCm8ukSZC9XtQufewFj9bn1cVUr/GN9oTWWdOOePLSMksFEPWLAwj8b1BqE7xK464BqxLGA6f36bpuFzClkS7MntjWoeOHQdzRP8gRudAbCqYTQbcEvtchKEBgeD8nXAOU4GWRd3NlxftaPUZr6pxIKh7ah2REbhw4IGmCYHZSYPXHci6tci2edNFsvTQhOZHOTzG16ZFevxQX9bnkt+/FX7JKCd+0yrhXJMy4QRqpb3w5QEBhsU0EHqBOv2GoyNJJZlLhWX8JqqudSuclMbxLA9h1H5W6ys3ylenWBjR7TlxU0c0cr1QP3va+CliLRjM1zp0JRbEbYnq98F+VByZoPe6Vp7yhdpLQY78QUeEHguIo/8BZsP8uyBlW0w0Zj2j9G+xS+P8c/EPXcH7lSWcAWG5p273sUmeWEHAHfwlEWj/dQlcitBU3oIV/IQycNSSAjY45V+n3lg1/3YmVmyoxLXG8dIfJzxd5SBDOUtWbGswqMmKTEvq+/8+o4i0cviSiX+c+3H67OBYzPnI+O/ukDM/S7FKwQ08bB7oOB0tnfnS549juybs8jsAXMWC7SCq0v5he6Z93Z2HLF8qazNghCBWQInNPVXIM/cef32HdaXLMyx3I5EkX8+cALRcO4OE33GlxxxQKm1zsIGRVlkR2CPzk9+jRt2ZJYvuch5ma3M6FIyvrdqov2twUyYrjJYLK6gGE2Le5Bmh6+oFbv6',
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