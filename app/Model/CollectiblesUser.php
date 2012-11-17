<?php
App::import('Core', 'Validation');
App::uses('CakeEvent', 'Event');
App::uses('BlogEventListener', 'Event');
class CollectiblesUser extends AppModel {

	public $name = 'CollectiblesUser';
	//As of 11/29/11 doing counter cache on both stash and user, this way we have easy access to a total of users collectibles and if we open up more stashes per user
	//then we have a complete total of collectibles
	public $belongsTo = array('Stash' => array('counterCache' => true), 'Collectible' => array('counterCache' => true), 'User' => array('counterCache' => true), 'Condition', 'Merchant' => array('counterCache' => true));
	public $actsAs = array('Revision' => array('model' => 'CollectiblesUserRev', 'ignore' => array('sort_number')), 'Containable');
	public $validate = array(
	//cost
	'cost' => array('rule' => array('money', 'left'), 'allowEmpty' => true, 'message' => 'Please supply a valid monetary amount.'),
	//edition size
	//'edition_size' => array('edition_sizeRule-1' => array('rule' => array('validateEditionSize'), 'message' => 'Must be a valid edition size.', 'last' => true), 'edition_sizeRule-1' => array('rule' => array('validateEditionSizeAndAP'), 'message' => 'Cannot have an edition size and be an artist\'s proof.')),
	'edition_size' => array('edition_sizeRule-1' => array('rule' => array('validateEditionSize'), 'message' => 'Must be a valid edition size.', 'last' => true)),
	//condition
	'condition_id' => array('rule' => 'numeric', 'required' => true, 'message' => 'Required.'),
	//merchant
	'merchant_id' => array('rule' => 'numeric', 'allowEmpty' => true, 'message' => 'Required.'),
	//purchase date
	'purchase_date' => array('rule' => array('date', 'mdy'), 'allowEmpty' => true, 'message' => 'Must be a valid date.'),
	//artist proof
	'artist_proof' => array('rule' => array('boolean'), 'message' => 'Incorrect value for Artist Proof'));

	function beforeValidate() {
		if (isset($this -> data['CollectiblesUser']['merchant']) && !empty($this -> data['CollectiblesUser']['merchant'])) {
			$merchant = $this -> data['CollectiblesUser']['merchant'];
			if (!Validation::maxLength($merchant, 150)) {
				$this -> invalidate('merchant', 'Cannot be more than 150 characters.');
				return false;
			}

			if (!Validation::minLength($merchant, 4)) {
				$this -> invalidate('merchant', 'Must be at least 4 characters.');
				return false;
			}
		}

		return true;
	}

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

		if (isset($this -> data['CollectiblesUser']['merchant']) && !empty($this -> data['CollectiblesUser']['merchant'])) {
			$existingMerchant = $this -> Merchant -> find('first', array('conditions' => array('Merchant.name' => $this -> data['CollectiblesUser']['merchant'])));
			/*
			 * If it does exist, link that one, otherwise add it and then use that id
			 */
			if (!empty($existingMerchant)) {
				$this -> data['CollectiblesUser']['merchant_id'] = $existingMerchant['Merchant']['id'];
			} else {
				$newMerchant = array();
				$newMerchant['Merchant']['name'] = $this -> data['CollectiblesUser']['merchant'];
				$this -> Merchant -> create();
				if ($this -> Merchant -> saveAll($newMerchant)) {
					$this -> data['CollectiblesUser']['merchant_id'] = $this -> Merchant -> id;
				} else {
					return false;
				}
			}
		} else {
			$this -> data['CollectiblesUser']['merchant_id'] = '';
		}

		return true;
	}

	function afterFind($results) {
		// Create a dateOnly pseudofield using date field.
		foreach ($results as $key => $val) {
			if (isset($val['CollectiblesUser']['purchase_date'])) {
				if ($val['CollectiblesUser']['purchase_date'] !== '0000-00-00') {
					$results[$key]['CollectiblesUser']['purchase_date'] = date('m/d/Y', strtotime($val['CollectiblesUser']['purchase_date']));
				} else {
					$results[$key]['CollectiblesUser']['purchase_date'] = '';
				}
			}
			// If it has a merchant, add the merchant name to the merchant field for display purposes
			if (isset($val['CollectiblesUser']['merchant_id']) && !is_null($val['CollectiblesUser']['merchant_id']) && !empty($val['CollectiblesUser']['merchant_id'])) {
				if (isset($val['Merchant'])) {
					$results[$key]['CollectiblesUser']['merchant'] = $val['Merchant']['name'];
				}
			} else {
				unset($results[$key]['Merchant']);
			}

		}
		return $results;
	}

	function validateEditionSizeAndAP($check) {
		//We know we have a valid edition size before getting here
		if (isset($check['edition_size']) && !empty($check['edition_size']) && isset($this -> data['CollectiblesUser']['artist_proof']) && $this -> data['CollectiblesUser']['artist_proof']) {
			return false;
		}

		return true;
	}

	function validateEditionSize($check) {
		//TODO: At some point this should check if there is another edition size already added and warn the user
		$collectible_id = $this -> data['CollectiblesUser']['collectible_id'];
		$this -> Collectible -> recursive = -1;
		$collectible = $this -> Collectible -> findById($collectible_id);
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

	/**
	 * Use this to get the CollectiblesUser, this will guarunetee that the correct data gets returned.
	 */
	public function getUserCollectible($id) {
		return $this -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('Merchant', 'Stash', 'Condition', 'User', 'Collectible' => array('Manufacture', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'License', 'Scale'))));
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