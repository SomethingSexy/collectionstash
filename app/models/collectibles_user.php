<?php
class CollectiblesUser extends AppModel {

	var $name = 'CollectiblesUser';
	var $belongsTo = array('Stash' => array('counterCache' => true), 'Collectible', 'User', 'Condition', 'Merchant');
	var $actsAs = array('Revision', 'Containable');
	var $validate = array('cost' => array('rule' => array('money', 'left'), 'required' => true, 'message' => 'Please supply a valid monetary amount.'), 'edition_size' => array('rule' => array('validateEditionSize'), 'message' => 'Must be a valid edition size.'), 'condition_id' => array('rule' => 'numeric', 'required' => true, 'message' => 'Required.'), 'merchant_id' => array('rule' => 'numeric', 'required' => true, 'message' => 'Required.'), 'purchase_date' => array('rule' => array('date', 'mdy'), 'allowEmpty' => true, 'message' => 'Must be a valid date.'));

	function beforeSave() {
		$this -> data['CollectiblesUser']['cost'] = str_replace('$', '', $this -> data['CollectiblesUser']['cost']);
		$this -> data['CollectiblesUser']['cost'] = str_replace(',', '', $this -> data['CollectiblesUser']['cost']);

		if (isset($this -> data['CollectiblesUser']['purchase_date'])) {
			if (empty($this -> data['CollectiblesUser']['purchase_date'])) {
				unset($this -> data['CollectiblesUser']['purchase_date']);
			} else {
				$this -> data['CollectiblesUser']['purchase_date'] = date('Y-m-d', strtotime($this -> data['CollectiblesUser']['purchase_date']));
			}
		}

		return true;
	}

	function afterFind($results) {
		// Create a dateOnly pseudofield using date field.
		foreach ($results as $key => $val) {
			if (isset($val['CollectiblesUser']['purchase_date'] ))
				$results[$key]['CollectiblesUser']['purchase_date']  = date('m/d/Y', strtotime($val['CollectiblesUser']['purchase_date']));
		}
		return $results;
	}

	function validateEditionSize($check) {
		debug($this -> data);
		$collectible_id = $this -> data['CollectiblesUser']['collectible_id'];
		debug($collectible_id);
		//$this->loadModel('Collectible');
		$this -> Collectible -> recursive = -1;
		$collectible = $this -> Collectible -> findById($collectible_id);
		debug($collectible);
		$showUserEditionSize = $collectible['Collectible']['showUserEditionSize'];
		$editionSize = trim($check['edition_size']);

		//first make sure this collectible shows edition size
		if ($showUserEditionSize == true) {
			//check first to make sure it is numeric
			if (!empty($editionSize)) {
				if (is_numeric($editionSize) === true) {
					$userEditionSize = intval($editionSize);
					$collectibleEditionSize = intval($collectible['Collectible']['edition_size']);
					//if the user entered edition size is greater than the collectible edition size, fail it
					if ($userEditionSize > $collectibleEditionSize) {
						return false;
					} else {
						return true;
					}
				} else {
					return false;
				}
			} else {
				//If it is empty, let it through
				return true;
			}

		} else {
			debug($showUserEditionSize);
			return true;
		}
	}

	public function getCollectibleDetail($id) {
		return $this -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('Collectible' => array('Manufacture', 'Collectibletype', 'Upload', 'License', 'Scale'))));
	}

	/**
	 * This method will return a list of users who have this collectible
	 * in their stash
	 */
	public function getListOfUsersWho($collectibleId, $editionSize = false) {
		if ($editionSize) {
			$data = $this -> find("all", array('order' => array('CollectiblesUser.edition_size' => 'ASC'), 'conditions' => array('CollectiblesUser.collectible_id' => $collectibleId), 'contain' => array('User' => array('fields' => array('id', 'username'), 'Stash'))));
		} else {
			$data = $this -> find("all", array('conditions' => array('CollectiblesUser.collectible_id' => $collectibleId), 'contain' => array('User' => array('fields' => array('id', 'username'), 'Stash'))));
		}

		debug($data);
		return $data;
	}

}
?>