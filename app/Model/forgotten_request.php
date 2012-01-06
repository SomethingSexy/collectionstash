<?php
class ForgottenRequest extends AppModel {
	var $name = 'ForgottenRequest';
	var $actsAs = array('Containable');

	function beforeSave() {
		debug($this -> data);
		/*
		 * Before we save this new forgotten request, we need to check to see if another
		 * one exists for this user.  If it does, lets delete that one and then add this one.
		 */
		$forgottenRequest = $this -> find("first", array('conditions' => array('ForgottenRequest.user_id' => $this -> data['ForgottenRequest']['user_id'])));
		if(!empty($forgottenRequest)){
			//Do I need to check if this passed or not?
			$this -> delete($forgottenRequest['ForgottenRequest']['id']);
		}

		return true;
	}

}
?>