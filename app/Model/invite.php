<?php
class Invite extends AppModel {
	var $name = 'Invite';
	var $actsAs = array('Containable');
	var $belongsTo = array('User' => array('counterCache' => true));
	var $validate = array('email' => array('rule' => array('email', true), 'message' => 'Enter a valid email'));

	function beforeValidate() {
		$valid = true;
		if (isset($this -> data['Invite']['email']) && !empty($this -> data['Invite']['email'])) {
			if ($this -> find('count', array('conditions' => array('Invite.email' => $this -> data['Invite']['email']))) > 0) {
				$this -> invalidate('email', 'A person with that email address has already been invited.');
				$valid = false;
			}
			if ($valid) {
				if ($this -> User -> find('count', array('conditions' => array('User.email' => $this -> data['Invite']['email']))) > 0) {
					$this -> invalidate('email', 'A person with that email address has already registered.');
					$valid = false;
				}
			}
		} else {
			$valid = false;
			$this -> invalidate('email', 'Email is required.');
		}

		return $valid;
	}

}
?>
