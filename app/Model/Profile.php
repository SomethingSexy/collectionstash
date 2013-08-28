<?php
class Profile extends AppModel {
	var $name = 'Profile';
	var $belongsTo = array('User');
	var $actsAs = array('Containable');

	public function updateProfile($data, $user) {
		$retVal = $this -> buildDefaultResponse();

		$this -> id = $data['id'];
		$this -> save($data, false, array('email_newsletter', 'email_notification', 'modified'));

		$retVal['response']['data'] = $data;
		$retVal['response']['isSuccess'] = true;

		return $retVal;
	}

}
?>
