<?php
class CollectiblesUser extends AppModel {

	var $name = 'CollectiblesUser';
	var $belongsTo = array('Stash', 'Collectible', 'User', 'Condition', 'Merchant');
	var $validate = array('cost' => array('rule' => array('money', 'left'), 'required' => true, 'message' => 'Please supply a valid monetary amount.'), 'edition_size' => array('rule' => array('validateEditionSize'), 'message' => 'Must be numeric.'), 'condition_id' => array('rule' => 'numeric', 'required' => true, 'message' => 'Required.'), 'merchant_id' => array('rule' => 'numeric', 'required' => true, 'message' => 'Required.'));
	var $actsAs = array('Revision', 'Containable');

	function validateEditionSize($check) {
		debug($this->data);	
		$collectible_id = $this->data['CollectiblesUser']['collectible_id'];
		debug($collectible_id);
		//$this->loadModel('Collectible');
		$this->Collectible->recursive = -1; 
		$collectible = $this->Collectible->findById($collectible_id);	
		debug($collectible);	
		$showUserEditionSize = $collectible['Collectible']['showUserEditionSize'];
		$editionSize = trim($check['edition_size']);

		if($showUserEditionSize == TRUE) {
			if(is_numeric($editionSize) === TRUE) {
				return true;
			} else {
				return false;
			}

		} else {
			debug($showUserEditionSize);
			return true;
		}
	}

	public function getCollectibleDetail($id) {
		return $this -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('Collectible' => array('Manufacture', 'Collectibletype', 'Upload', 'License'))));
	}

	public function getListOfUsersWho($collectibleId) {
		$data = $this -> find("all", array('conditions' => array('CollectiblesUser.collectible_id' => $collectibleId), 'contain' => array('Stash' => array('User' => array('fields' => array('id', 'username'))))));
		$users = array();
		foreach($data as $key => $user) {
			$users[$key]['User'] = $user['Stash']['User'];
		}
		return $users;
	}

}

?>