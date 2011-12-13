<?php
class User extends AppModel {
	var $name = 'User';
	var $actsAs = array('ExtendAssociations', 'Containable');
	var $hasMany = array('Stash', 'CollectiblesUser', 'Invite', 'UserUploads');
	//TODO should I add here 'Collectible'? Since technically a user has many collectible because of the ones they added
	var $hasOne = array('Profile' => array('dependent' => true));

	function beforeValidate() {
		$valid = true;
		if (!$this -> id) {
			if (isset($this -> data['User']['username']) && $this -> find('count', array('conditions' => array('User.username' => $this -> data['User']['username']))) > 0) {
				$this -> invalidate('username_unique');
				$valid = false;
			}
			if ($this -> find('count', array('conditions' => array('User.email' => $this -> data['User']['email']))) > 0) {
				debug($valid);
				$this -> invalidate('email', 'A user with that email already exists.');
				$valid = false;
			}
		}

		return $valid;
	}

	var $validate = array('username' => array('validValues' => array('rule' => 'alphaNumeric', 'required' => true, 'message' => 'Alphanumeric only.'), 'validLength' => array('rule' => array('maxLength', '40'), 'message' => 'Maximum 40 characters long'), 'validLengthMin' => array('rule' => array('minLength', '3'), 'message' => 'Minimum 3 characters long')), 'new_password' => array('samePass' => array('rule' => array('validateSamePassword'), 'required' => true, 'message' => 'Password and confirm password are not the same.'), 'validChars' => array('rule' => array('validatePasswordChars'), 'last' => true, 'required' => true, 'message' => 'Must be at least 8 characters long and contain one uppercase and one numeric.')), 'email' => array('rule' => array('email', true), 'message' => 'Enter a valid email'), 'first_name' => array('rule' => 'alphaNumeric', 'required' => true), 'last_name' => array('rule' => 'alphaNumeric', 'required' => true));

	/**
	 * This validates to make sure the new and confirm password are the same
	 */
	function validateSamePassword() {
		$valid = true;
		if (strcmp($this -> data['User']['new_password'], $this -> data['User']['confirm_password'])) {
			$valid = false;
			debug($valid);
		}
		return $valid;
	}

	/**
	 * This validates to make sure that the password follows our rules
	 */
	function validatePasswordChars() {
		$valid = true;
		if (!preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $this -> data['User']['new_password'])) {
			//doesnt mneet our 1 upper, one lower, 1 digit or special character require,ent
			$valid = false;

		}

		return $valid;
	}

	/**
	 * This method returns true or false if the given email address
	 * is a valid user's email address
	 */
	public function isValidUserEmail($user) {
		if ($this -> find('count', array('conditions' => array('User.email' => $user['User']['email']))) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * This method will return the User Model given a email address.
	 */
	public function getUserByEmail($user) {
		return $this -> find("first", array('conditions' => array('User.email' => $user['User']['email']), 'contain' => false));
	}

	/**
	 * This method will change the users password given the standard 'new_passowrd' and 'confirm_password', right
	 * now this method assumes you have validated the data.  Returns true if the update was successful
	 */
	public function changePassword($user) {
		//Let's has the password first
		$user['User']['password'] = Security::hash($user['User']['new_password']);
		$this -> id = $user['User']['id'];
		if ($this -> saveField('password', $user['User']['password'], false)) {
			return true;
		} else {
			return false;
		}

	}

	public function getUser($username) {
		$this -> recursive = 0;
		return $this -> findByUsername($username);
	}

	public function getAllCollectiblesByUser($username) {
		//debug($username);
		$result = $this -> findByUsername($username);
		//better way to do this? this gives me the manufacture for each collectible
		//Update this to use the contains option
		$this -> CollectiblesUser -> recursive = 2;
		$this -> CollectiblesUser -> bindModel(array('belongsTo' => array('User', 'Collectible')));

		if ($result['User']['admin']) {
			$joinRecords = $this -> CollectiblesUser -> find("all", array('conditions' => array('CollectiblesUser.user_id' => $result['User']['id'])));
		} else {
			$joinRecords = $this -> CollectiblesUser -> find("all", array('conditions' => array('CollectiblesUser.user_id' => $result['User']['id'], 'Collectible.pending' => 0)));
		}

		return $joinRecords;
	}

	public function getNumberOfStashesByUser($username) {
		$result = $this -> findByUsername($username);
		$count = $this -> find("first", array('conditions' => array('User.id' => $result['User']['id']), 'fields' => array('User.stash_count'), 'contain' => false));

		return $count['User']['stash_count'];
	}

	public function getNumberOfCollectiblesByUser($username) {
		$result = $this -> findByUsername($username);
		$count = $this -> find("first", array('conditions' => array('User.id' => $result['User']['id']), 'fields' => array('User.stash_count'), 'contain' => false));
		return $count['User']['stash_count'];
	}

	public function getCollectibleByUser($username, $collectibleid) {
		$result = $this -> findByUsername($username);
		//$this->CollectiblesUser->recursive = 2;
		//$this->CollectiblesUser->bindModel(array('belongsTo' => array('User', 'Collectible')));

		$joinRecords = $this -> CollectiblesUser -> find("first", array('conditions' => array('CollectiblesUser.user_id' => $result['User']['id'], 'CollectiblesUser.id' => $collectibleid), 'contain' => array('Collectible')));

		return $joinRecords;
	}

	/**
	 * Creates an activation hash for the current user.
	 *
	 *      @param Void
	 *      @return String activation hash.
	 */
	function getActivationHash() {
		if (!isset($this -> id)) {
			return false;
		}
		return substr(Security::hash(Configure::read('Security.salt') . $this -> field('created') . date('Ymd')), 0, 8);
	}

}
?>
