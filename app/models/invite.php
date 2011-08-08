<?php
class Invite extends AppModel {
	var $name = 'Invite';
	var $actsAs = array('Containable');
	var $belongsTo = array('User' => array('counterCache' => true));
	var $validate = array('email' => array('rule' => array('email', true), 'message' => 'Enter a valid email'));
	
	function beforeValidate() {
		$valid = true;
		if(!$this -> id) {
			if($this -> find('count', array('conditions' => array('Invite.email' => $this -> data['User']['email']))) > 0) {
				debug($valid);
				$this -> invalidate('email', 'A person with that email address has already been invited.');
				$valid = false;
			}
		}

		return $valid;
	}

}
?>
