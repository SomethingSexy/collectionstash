<?php
App::uses('AuthComponent', 'Controller/Component');
class User extends AppModel {
	var $name = 'User';
	var $actsAs = array('Containable');
	var $hasMany = array('Notification', 'Activity', 'Edit', 'Collectible', 'UserPointYearFact', 'UserPointFact', 'Stash' => array('dependent' => true), 'CollectiblesUser' => array('dependent' => true), 'Invite', 'UserUpload' => array('dependent' => true), 'Subscription' => array('dependent' => true));
	//TODO should I add here 'Collectible'? Since technically a user has many collectible because of the ones they added
	var $hasOne = array('Profile' => array('dependent' => true));

	public $validate = array(
	//username
	'username' => array(
	//valid values
	'validValues' => array('rule' => 'alphaNumeric', 'required' => true, 'allowEmpty' => false, 'message' => 'Alphanumeric only.'),
	//valid length
	'validLength' => array('rule' => array('maxLength', 40), 'message' => 'Maximum 40 characters long'),
	//valid min length
	'validLengthMin' => array('rule' => array('minLength', 3), 'message' => 'Minimum 3 characters long'),
	//unique
	'uniqueUserName' => array('rule' => array('isUnqiueUserName'), 'message' => 'That username exists already. Try again.')),
	//password
	'new_password' => array('samePass' => array('rule' => array('validateSamePassword'), 'required' => true, 'allowEmpty' => false, 'message' => 'Password and confirm password are not the same.'), 'validChars' => array('rule' => array('validatePasswordChars'), 'last' => true, 'message' => 'Must be at least 8 characters long and contain one uppercase and one numeric.')),
	//email
	'email' => array('validValues' => array('rule' => array('email', true), 'required' => true, 'allowEmpty' => false, 'message' => 'Enter a valid email'),
	//Unique email
	'uniqueEmail' => array('rule' => array('isUnqiueEmail'), 'message' => 'A user with that email already exists.')),
	//first
	'first_name' => array('rule' => 'alphaNumeric', 'required' => true, 'allowEmpty' => false, 'message' => 'Alphanumeric only.'),
	//last, allow space now
	'last_name' => array('rule' => '/^[a-z0-9 ]*$/i', 'required' => true, 'allowEmpty' => false, 'message' => 'Alphanumeric only.'));

	function beforeSave() {
		//Make sure there is no space around the email, seems to be an issue with sending when there is
		if (isset($this -> data['User']['email'])) {
			$this -> data['User']['email'] = trim($this -> data['User']['email']);
		}

		return true;
	}

	/**
	 * This validates to make sure the new and confirm password are the same
	 */
	function validateSamePassword() {
		$valid = true;
		if (strcmp($this -> data['User']['new_password'], $this -> data['User']['confirm_password'])) {
			$valid = false;
		}
		return $valid;
	}

	function isUnqiueUserName() {
		$valid = true;
		if (isset($this -> data['User']['username']) && !empty($this -> data['User']['username'])) {
			$userCount = $this -> find('count', array('conditions' => array('User.username' => $this -> data['User']['username'])));
			if ($userCount > 0) {
				$valid = false;
			}
		}
		return $valid;
	}

	function isUnqiueEmail() {
		$valid = true;
		if (isset($this -> data['User']['email']) && !empty($this -> data['User']['email'])) {
			$emailCount = $this -> find('count', array('conditions' => array('User.email' => $this -> data['User']['email'])));
			if ($emailCount > 0) {
				$valid = false;
			}

		}
		return $valid;
	}

	/**
	 * This validates to make sure that the password follows our rules
	 */
	function validatePasswordChars() {
		$valid = true;
		if (!empty($this -> data['User']['new_password']) && !preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $this -> data['User']['new_password'])) {
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
		$user['User']['password'] = AuthComponent::password($this -> data['User']['new_password']);
		$data = array('id' => $user['User']['id'], 'password' => $user['User']['password'], 'force_password_reset' => false);
		if ($this -> save($data, false, array('password', 'force_password_reset'))) {
			return true;
		} else {
			return false;
		}

	}

	public function getUser($username) {
		$retVal = $this -> find('first', array('contain' => array('Profile'), 'conditions' => array('User.username' => $username)));
		return $retVal;
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

	/**
	 * The data for this will be given to us as $user['User]
	 */
	public function createUser($userData) {
		$userData['User']['admin'] = 0;
		$userData['User']['status'] = 1;
		$userData['Profile'] = array();
		//Set the invites to 0, as they invite people we will increase this number
		$userData['Profile']['invites'] = 0;
		$userData['Stash'] = array();
		$userData['Stash']['0'] = array();
		$userData['Stash']['0']['name'] = 'Default';
		//Need to put this here to create the entity
		// TODO: Update Stash to use the EntityTypeBehavior to automate this shit
		$userData['Stash']['0']['EntityType']['type'] = 'stash';
		$userData['Stash']['1'] = array();
		$userData['Stash']['1']['name'] = 'Wishlist';
		//Need to put this here to create the entity
		// TODO: Update Stash to use the EntityTypeBehavior to automate this shit
		$userData['Stash']['1']['EntityType']['type'] = 'stash';
		if ($this -> saveAssociated($userData, array('deep' => true))) {
			//Find the user
			$user = $this -> find("first", array('conditions' => array('User.id' => $this -> id)));
			//Subscribe them to their own stash

			$this -> Subscription -> addSubscription($user['Stash'][0]['entity_type_id'], $user['User']['id']);
			return true;
		}

		return false;
	}

}
?>
