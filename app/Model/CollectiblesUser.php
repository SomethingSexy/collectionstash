<?php
App::import('Core', 'Validation');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesUser extends AppModel {

	public $findMethods = array('orderAveragePrice' => true);

	public $name = 'CollectiblesUser';
	//As of 11/29/11 doing counter cache on both stash and user, this way we have easy access to a total of users collectibles and if we open up more stashes per user
	//then we have a complete total of collectibles
	public $belongsTo = array('CollectibleUserRemoveReason', 'Listing', 'Stash' => array('counterCache' => true), 'Collectible' => array('counterCache' => true), 'User' => array('counterCache' => true), 'Condition', 'Merchant' => array('counterCache' => true));
	public $actsAs = array('Revision' => array('model' => 'CollectiblesUserRev', 'ignore' => array('sort_number')), 'Containable');
	public $validate = array(
	//cost
	'cost' => array('rule' => array('money', 'left'), 'allowEmpty' => true, 'message' => 'Please supply a valid monetary amount.'),
	//edition size
	//'edition_size' => array('edition_sizeRule-1' => array('rule' => array('validateEditionSize'), 'message' => 'Must be a valid edition size.', 'last' => true), 'edition_sizeRule-1' => array('rule' => array('validateEditionSizeAndAP'), 'message' => 'Cannot have an edition size and be an artist\'s proof.')),
	'edition_size' => array('edition_sizeRule-1' => array('rule' => array('validateEditionSize'), 'allowEmpty' => true, 'message' => 'Must be a valid edition size.', 'last' => true)),
	//condition
	'condition_id' => array('rule' => 'numeric', 'allowEmpty' => true, 'message' => 'Must be a valid condition.'),
	//merchant
	'merchant_id' => array('rule' => 'numeric', 'allowEmpty' => true, 'message' => 'Must be a valid Merchant.'),
	//purchase date
	'purchase_date' => array('rule' => array('date', 'mdy'), 'allowEmpty' => true, 'message' => 'Must be a valid date.'),
	//artist proof
	'artist_proof' => array('rule' => array('boolean'), 'message' => 'Incorrect value for Artist Proof'),
	// this is only needed when deleting
	'collectible_user_remove_reason_id' => array('rule' => 'numeric', 'allowEmpty' => true, 'message' => 'Must be a valid reason.'),
	// this is only needed when deleting
	'remove_date' => array('rule' => array('date', 'mdy'), 'allowEmpty' => true, 'message' => 'Must be a valid date.'),
	// this is only needed when deleting
	'sold_cost' => array('rule' => array('money', 'left'), 'allowEmpty' => true, 'message' => 'Please supply a valid monetary amount.'),
	//notes
	'notes' => array('maxLength' => array('rule' => array('maxLength', 1000), 'allowEmpty' => true, 'message' => 'Notes must be less than 1000 characters.')), );

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
		if (isset($this -> data['CollectiblesUser']['cost'])) {
			$this -> data['CollectiblesUser']['cost'] = str_replace('$', '', $this -> data['CollectiblesUser']['cost']);
			$this -> data['CollectiblesUser']['cost'] = str_replace(',', '', $this -> data['CollectiblesUser']['cost']);
		}

		if (isset($this -> data['CollectiblesUser']['purchase_date'])) {
			if (empty($this -> data['CollectiblesUser']['purchase_date'])) {
				unset($this -> data['CollectiblesUser']['purchase_date']);
			} else {
				$this -> data['CollectiblesUser']['purchase_date'] = date('Y-m-d', strtotime($this -> data['CollectiblesUser']['purchase_date']));
			}
		}

		if (isset($this -> data['CollectiblesUser']['remove_date'])) {
			if (empty($this -> data['CollectiblesUser']['remove_date'])) {
				unset($this -> data['CollectiblesUser']['remove_date']);
			} else {
				$this -> data['CollectiblesUser']['remove_date'] = date('Y-m-d', strtotime($this -> data['CollectiblesUser']['remove_date']));
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

	function afterFind($results, $primary = false) {

		if ($results && $primary) {
			// Create a dateOnly pseudofield using date field.
			foreach ($results as $key => $val) {
				// make sure we check if the collectibleuser is set...this is for
				// cases when count is being called
				if (isset($val['CollectiblesUser'])) {
					if (isset($val['CollectiblesUser']['purchase_date'])) {
						if ($val['CollectiblesUser']['purchase_date'] !== '0000-00-00') {
							$results[$key]['CollectiblesUser']['purchase_date'] = date('m/d/Y', strtotime($val['CollectiblesUser']['purchase_date']));
						} else {
							$results[$key]['CollectiblesUser']['purchase_date'] = '';
						}
					}
					if (isset($val['CollectiblesUser']['remove_date'])) {
						if ($val['CollectiblesUser']['remove_date'] !== null && $val['CollectiblesUser']['remove_date'] !== '0000-00-00') {
							$results[$key]['CollectiblesUser']['remove_date'] = date('m/d/Y', strtotime($val['CollectiblesUser']['remove_date']));
						} else {
							$results[$key]['CollectiblesUser']['remove_date'] = '';
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

					// TODO: Filter out collectibles that have not been activated yet.  This is for customs and originals
					// Might be better in the end to do a join on the table instead to remove those but for now this will be quickiest
					if (isset($val['Collectible']) && !empty($val['Collectible'])) {
						if ($val['Collectible']['original'] || $val['Collectible']['custom']) {
							if ($val['Collectible']['status_id'] !== '4') {
								unset($results[$key]);
							}
						}
					}

					// make sure we have a listing, these listings will only have one transaction
					if (isset($val['Listing']) && !empty($val['Listing']['id']) && !empty($val['Listing']['Transaction'])) {
						$results[$key]['CollectiblesUser']['sold_cost'] = $val['Listing']['Transaction'][0]['sale_price'];
					} else {
						unset($results[$key]['Listing']);
					}
				}
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
			return true;
		}
	}

	/**
	 * Use this to get the CollectiblesUser, this will guarunetee that the correct data gets returned.
	 */
	public function getUserCollectible($id) {
		return $this -> find("first", array('conditions' => array('CollectiblesUser.id' => $id), 'contain' => array('Listing' => array('Transaction'), 'Merchant', 'Stash', 'Condition', 'User', 'Collectible' => array('Manufacture', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'License', 'Scale'))));
	}

	/**
	 * This method will return a list of users who have this collectible
	 * in their stash
	 */
	public function getListOfUsersWho($collectibleId, $editionSize = false) {
		if ($editionSize) {
			$data = $this -> find("all", array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'order' => array('CollectiblesUser.edition_size' => 'ASC'), 'conditions' => array('CollectiblesUser.collectible_id' => $collectibleId), 'contain' => array('User' => array('fields' => array('id', 'username'), 'Stash'))));
		} else {
			$data = $this -> find("all", array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'conditions' => array('CollectiblesUser.collectible_id' => $collectibleId), 'contain' => array('User' => array('fields' => array('id', 'username'), 'Stash'))));
		}

		return $data;
	}

	public function getListOfUserWishlist($collectibleId) {
		$data = $this -> find("all", array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Wishlist"'))), 'conditions' => array('CollectiblesUser.collectible_id' => $collectibleId), 'contain' => array('User' => array('fields' => array('id', 'username'), 'Stash'))));
		return $data;
	}

	public function add($data, $user, $stashType = 'Default') {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		$retVal['response']['errors'] = array();

		if (empty($data) || empty($user)) {
			array_push($retVal['response']['errors'], array('message' => __('Invalid request.')));
			return $retVal;
		}

		// Give the user id and the stash type, we need to figure out what
		// stash to add this too
		$stash = $this -> Stash -> getStash($user['User']['id'], $stashType);
		if (!empty($stash)) {
			$data['CollectiblesUser']['stash_id'] = $stash['Stash']['id'];
			$data['CollectiblesUser']['user_id'] = $user['User']['id'];
			if ($this -> save($data)) {
				$retVal['response']['isSuccess'] = true;
				// We need to get some data to handle this event
				$collectible = $this -> Collectible -> find('first', array('contain' => array('CollectiblesUpload' => array('Upload'), 'Manufacture', 'User', 'ArtistsCollectible' => array('Artist')), 'conditions' => array('Collectible.id' => $data['CollectiblesUser']['collectible_id'])));
				$this -> getEventManager() -> dispatch(new CakeEvent('Model.Activity.add', $this, array('activityType' => ActivityTypes::$ADD_COLLECTIBLE_STASH, 'user' => $user, 'collectible' => $collectible, 'stash' => $stash)));
				// This is old school event, will be replaced by activity stuff later TODO
				$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Stash.Collectible.add', $this, array('stashId' => $stash['Stash']['id'])));
			} else {
				$retVal['response']['isSuccess'] = false;
				$errors = $this -> convertErrorsJSON($this -> validationErrors, 'CollectiblesUser');
				$retVal['response']['errors'] = $errors;
			}
		}

		return $retVal;
	}

	public function update($data, $user) {
		$retVal = $this -> buildDefaultResponse();

		//grab the collectible, we need it to determine what we can update
		$collectiblesUser = $this -> find("first", array('conditions' => array('CollectiblesUser.id' => array($data['CollectiblesUser']['id'])), 'contain' => array('Listing' => array('Transaction'), 'CollectibleUserRemoveReason', 'User', 'Collectible', 'Stash')));

		// make sure we can update it
		if (!$this -> isEditPermission($collectiblesUser, $user)) {
			$retVal['response']['code'] = 401;
			return $retVal;
		}

		if (!$collectiblesUser['CollectiblesUser']['active']) {
			$this -> validate['remove_date']['allowEmpty'] = false;
			$this -> validate['remove_date']['required'] = true;
			// if sold cost is required, then
			if ($collectiblesUser['CollectibleUserRemoveReason']['sold_cost_required']) {
				$this -> validate['sold_cost']['allowEmpty'] = false;
				$this -> validate['sold_cost']['required'] = true;
			}
		}

		// adding sold_cost here for validation but it will not be added to the collectibles_users table
		$fieldList = array('edition_size', 'cost', 'condition_id', 'merchant_id', 'purchase_date', 'artist_proof', 'remove_date', 'sold_cost', 'listing_id', 'notes', 'notes_private');
		$data['CollectiblesUser']['collectible_id'] = $collectiblesUser['CollectiblesUser']['collectible_id'];
		$dataSource = $this -> getDataSource();
		$dataSource -> begin();

		// first check if there is a cost and it is new
		if (isset($data['CollectiblesUser']['sold_cost']) && !empty($data['CollectiblesUser']['sold_cost'])) {
			// if the listing is empty then we don't haave one

			if (empty($collectiblesUser['Listing'])) {
				$listingData = array();
				$listingData['Listing']['collectible_id'] = $collectiblesUser['Collectible']['id'];
				$listingData['Listing']['current_price'] = $data['CollectiblesUser']['sold_cost'];
				$listingData['Listing']['end_date'] = date('Y-m-d', strtotime($data['CollectiblesUser']['remove_date']));
				$listingData['Listing']['listing_type_id'] = 2;
				$listing = $this -> Listing -> createListing($listingData, $user);

				if (!$listing['response']['isSuccess']) {
					$dataSource -> rollback();
					$retVal['response']['code'] = 500;
					return $retVal;
				}
				debug($listing['response']['data']['id']);
				$data['CollectiblesUser']['listing_id'] = $listing['response']['data']['id'];
			} else if ($data['CollectiblesUser']['sold_cost'] !== $collectiblesUser['CollectiblesUser']['sold_cost']) {// then check if there is not a cost but there was a cost
				$transaction['Transaction'] = $collectiblesUser['Listing']['Transaction'][0];
				$transaction['Transaction']['sale_price'] = $data['CollectiblesUser']['sold_cost'];

				if (!$this -> Listing -> Transaction -> save($transaction)) {
					$dataSource -> rollback();
					$retVal['response']['code'] = 500;
					return $retVal;
				}
			}
		} else {
			// We don't have a sold_cost, see if we had a listing previously
			if (!empty($collectiblesUser['Listing'])) {
				$data['CollectiblesUser']['listing_id'] = null;
				if (!$this -> Listing -> delete($collectiblesUser['Listing']['id'])) {
					$dataSource -> rollback();
					$retVal['response']['code'] = 500;
					return $retVal;
				}
			}
		}

		if ($this -> save($data, true, $fieldList)) {
			$retVal['response']['isSuccess'] = true;
			$dataSource -> commit();
		} else {
			$dataSource -> rollback();
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'CollectiblesUser');
			$retVal['response']['errors'] = $errors;
			return $retVal;
		}

		return $retVal;

	}

	public function remove($data, $user) {
		$retVal = $this -> buildDefaultResponse();
		// grab the collectible we are removing first, needed for the event
		$collectiblesUser = $this -> find("first", array('conditions' => array('CollectiblesUser.id' => array($data['CollectiblesUser']['id'])), 'contain' => array('User', 'Collectible', 'Stash')));

		// we need to check permissions first
		// return 401 if they are not allowed to edit this one
		if (!$this -> isEditPermission($collectiblesUser, $user)) {
			$retVal['response']['code'] = 401;

			return $retVal;
		}

		// first check to see if the collectible_user is already inactive
		// if so then just delete it
		if (!$collectiblesUser['CollectiblesUser']['active']) {
			if ($this -> delete($data['CollectiblesUser']['id'])) {
				$retVal['response']['isSuccess'] = true;
				$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$REMOVE_COLLECTIBLE_STASH, 'user' => $user, 'collectible' => $collectiblesUser, 'stash' => $collectiblesUser)));
			}

			return $retVal;
		}

		// set the data to the model so we can validate it
		$this -> set($data['CollectiblesUser']);
		// now let's validate, we need a reason first
		$this -> validate['collectible_user_remove_reason_id']['allowEmpty'] = false;
		$this -> validate['collectible_user_remove_reason_id']['required'] = true;
		// we NEED a reason here
		if (!$this -> validates()) {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'CollectiblesUser');
			$retVal['response']['errors'] = $errors;
			return $retVal;
		}

		// now that we know it has a valid reason, we need to then check that reason
		$reason = $this -> CollectibleUserRemoveReason -> find('first', array('conditions' => array('CollectibleUserRemoveReason.id' => $data['CollectiblesUser']['collectible_user_remove_reason_id']), 'contain' => false));

		// if the reason is an auto readon, then automatically remove it
		if ($reason['CollectibleUserRemoveReason']['remove']) {
			// just remove it completely
			if ($this -> delete($data['CollectiblesUser']['id'])) {
				$retVal['response']['isSuccess'] = true;
				$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$REMOVE_COLLECTIBLE_STASH, 'user' => $user, 'collectible' => $collectiblesUser, 'stash' => $collectiblesUser)));
			}
		} else {
			// history
			// otherwise we want to finish validating

			$this -> validate['remove_date']['allowEmpty'] = false;
			$this -> validate['remove_date']['required'] = true;
			if ($reason['CollectibleUserRemoveReason']['sold_cost_required']) {
				$this -> validate['sold_cost']['allowEmpty'] = false;
				$this -> validate['sold_cost']['required'] = true;
			}

			if (!$this -> validates()) {
				$retVal['response']['isSuccess'] = false;
				$errors = $this -> convertErrorsJSON($this -> validationErrors, 'CollectiblesUser');
				$retVal['response']['errors'] = $errors;
				return $retVal;
			}

			$dataSource = $this -> getDataSource();
			$dataSource -> begin();

			if (isset($data['CollectiblesUser']['sold_cost'])) {
				$listingData = array();
				$listingData['Listing']['collectible_id'] = $collectiblesUser['Collectible']['id'];
				$listingData['Listing']['current_price'] = $data['CollectiblesUser']['sold_cost'];
				$listingData['Listing']['end_date'] = date('Y-m-d', strtotime($data['CollectiblesUser']['remove_date']));
				$listingData['Listing']['listing_type_id'] = 2;
				$listing = $this -> Listing -> createListing($listingData, $user);

				if (!$listing['response']['isSuccess']) {
					$dataSource -> rollback();
					$retVal['response']['code'] = 500;
					return $retVal;
				}

				$data['CollectiblesUser']['listing_id'] = $listing['response']['data']['id'];
			}

			$data['CollectiblesUser']['active'] = false;

			if ($this -> save($data)) {
				// TODO: when we bootstrap this guy we might have to return more information here
				$updateCollectibleUser = $this -> find("first", array('conditions' => array('CollectiblesUser.id' => $this -> id), 'contain' => array('Listing' => array('Transaction'), 'CollectibleUserRemoveReason', 'User', 'Collectible', 'Stash')));

				$retVal['response']['isSuccess'] = true;
				$retVal['response']['data'] = $updateCollectibleUser['CollectiblesUser'];

				$dataSource -> commit();
				$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$REMOVE_COLLECTIBLE_STASH, 'user' => $user, 'collectible' => $collectiblesUser, 'stash' => $collectiblesUser)));
			} else {
				$dataSource -> rollback();
			}
		}

		// $this -> validate['remove_date']['allowEmpty'] = false;
		// $this -> validate['sold_cost']['allowEmpty'] = false;

		return $retVal;
	}

	/**
	 * This is used to create a stubbed out, default CollectiblesUser
	 * object.  Used if an outside model wants to add a CollectiblesUser
	 */
	public function createDefault($userId, $collectibleId) {
		$retVal = array();

		$stashId = $this -> Stash -> getStashId($userId);
		$retVal['CollectiblesUser']['user_id'] = $userId;
		$retVal['CollectiblesUser']['stash_id'] = $stashId;
		$retVal['CollectiblesUser']['collectible_id'] = $collectibleId;

		return $retVal;
	}

	public function isEditPermission($check, $user) {
		$retVal = false;

		// setup to work for when we have the collectible object
		// already or just the id
		if (is_numeric($check) || is_string($check)) {
			$collectible = $this -> find('first', array('conditions' => array('CollectiblesUser.id' => $check), 'contain' => false));
			//lol
		} else {
			// assume object
			$collectible = $check;
		}

		// they must be the current owner of this collectible to edit it
		if (!empty($collectible) && $collectible['CollectiblesUser']['user_id'] === $user['User']['id']) {
			$retVal = true;
		}

		return $retVal;
	}

	protected function _findOrderAveragePrice($state, $query, $results = array()) {
		if ($state === 'before') {
			// check to see what the sort is and then apply it here
			if (!empty($query['operation']) && $query['operation'] === 'count') {
				return $query;
			}

			if (!empty($query['sort']) && $query['sort'] === 'Collectible.average_price') {
				$query['order'] = array('Collectible.orderAveragePrice' => $query['direction']);

				return $query;
			}

			// $query['joins'] = array(
			// //array of required joins
			// );
			return $query;
		}
		return $results;
	}

}
?>