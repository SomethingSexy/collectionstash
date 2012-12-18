<?php
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class Collectible extends AppModel {
	public $name = 'Collectible';
	public $belongsTo = array('EntityType' => array('dependent' => true), 'SpecializedType' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'Revision' => array('dependent' => true), 'Manufacture' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'Collectibletype' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'License' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'Series', 'Scale' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'Retailer' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'User' => array('counterCache' => true), 'Currency');
	public $hasMany = array('CollectiblesUser', 'CollectiblesUpload' => array('dependent' => true), 'AttributesCollectible' => array('dependent' => true), 'CollectiblesTag' => array('dependent' => true));
	public $actsAs = array('Editable' => array('type' => 'collectible', 'model' => 'CollectibleEdit', 'modelAssociations' => array('belongsTo' => array('SpecializedType', 'Manufacture', 'Collectibletype', 'License', 'Scale', 'Series', 'Retailer', 'Currency')), 'compare' => array('name', 'manufacture_id', 'specialized_type_id', 'collectibletype_id', 'description', 'msrp', 'edition_size', 'numbered', 'upc', 'product_width', 'product_depth', 'license_id', 'series_id', 'variant', 'url', 'exclusive', 'retailer_id', 'variant_collectible_id', 'product_length', 'product_weight', 'scale_id', 'release', 'limited', 'code', 'pieces', 'currency_id')), 'Revision' => array('model' => 'CollectibleRev', 'ignore' => array('collectibles_user_count', 'entity_type_id')), 'Containable', 'Sluggable' => array(
	/**
	 * Ok so I want to build slugs on the fly instead of a database field, cause then I would
	 * have to worry about updates and shit...
	 *
	 * The problem is, the slug I want to build for this one has associations i want to bind,
	 * so I am thinking I set those below like so to grab those associations.  If the first one
	 * in the arry is not "Model", then do it on the model alias
	 */
	'displayField' => array('field1' => array('Model' => 'Manufacture', 'Field' => 'title'), 'field2' => array('Model' => 'License', 'Field' => 'name'), 'field3' => array('Model' => 'Collectible', 'Field' => 'name'), 'field4' => array('Model' => 'Collectibletype', 'Field' => 'name'), 'field5' => array('Model' => 'Collectible', 'Field' => 'exclusive', 'Display' => 'Exclusive'), 'field6' => array('Model' => 'Collectible', 'Field' => 'variant', 'Display' => 'Variant')), 'showPrimary' => false,
	// 'slugField' => 'theNameOfYourSlugVirtualField',
	'replacement' => '-' //the char to implode the words in entry name...
	));

	public $validate = array(
	//name field
	//'name' => array('rule' => "/^[A-Za-z0-9\s#:.-]+\z/", 'required' => true, 'message' => 'Invalid characters'),
	//Opening this up because I don't see it being a big deal.
	'name' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Name is required.'), 'maxLength' => array('rule' => array('maxLength', 200), 'message' => 'Invalid length.')),
	//manufacture field
	'manufacture_id' => array('rule' => array('validateManufactureId'), 'required' => true, 'message' => 'Must be a valid manufacture.'),
	//collectible type field
	'collectibletype_id' => array('rule' => array('validateCollectibleType'), 'required' => true, 'message' => 'Must be a valid type.'),
	//license filed
	'license_id' => array('rule' => array('validateLicenseId'), 'message' => 'Brand/License must be valid for Manufacture.'),
	//series field
	'series_id' => array('rule' => array('validateSeriesId'), 'message' => 'Please select a valid category.'),
	//description field
	'description' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Description is required.'), 'maxLength' => array('rule' => array('maxLength', 1000), 'message' => 'Invalid length.')),
	//msrp
	'msrp' => array('rule' => array('money', 'left'), 'required' => true, 'message' => 'Please supply a valid monetary amount.'),
	//edition_size
	'edition_size' => array('rule' => array('validateEditionSize'), 'allowEmpty' => true, 'message' => 'Must be numeric.'),
	//retailer
	'retailer' => array('minLength' => array('rule' => array('minLength', 4), 'allowEmpty' => true, 'message' => 'Must be at least 4 characters.'), 'maxLength' => array('rule' => array('maxLength', 150), 'message' => 'Cannot be more than 150 characters.')),
	//upc
	'upc' => array('numeric' => array('rule' => 'numeric', 'allowEmpty' => true, 'message' => 'Must be numeric.'), 'maxLength' => array('rule' => array('maxLength', 12), 'message' => 'Invalid length.')),
	//product code
	'code' => array('numeric' => array('rule' => '/^[\\w\\s-\/]+$/', 'allowEmpty' => true, 'message' => 'Invalid characters.'), 'maxLength' => array('rule' => array('maxLength', 50), 'message' => 'Invalid length.')),
	//This should be decmial or blank
	'product_length' => array('rule' => '/^(?:\d{1,3}(?:\.\d{0,6})?)?$/', 'allowEmpty' => true, 'message' => 'Must be a valid height.'),
	//This should be decmial or blank
	'product_width' => array('validValues' => array('rule' => '/^(?:\d{1,3}(?:\.\d{0,6})?)?$/', 'allowEmpty' => true, 'message' => 'Must be a valid width.'), ),
	//This should be decmial or blank
	'product_depth' => array('validValues' => array('rule' => '/^(?:\d{1,3}(?:\.\d{0,6})?)?$/', 'allowEmpty' => true, 'message' => 'Must be a valid depth.'), ),
	//url
	'url' => array('rule' => 'url', 'required' => true, 'message' => 'Must be a valid url.'),
	//numbered
	'numbered' => array('rule' => array('validateNumbered'), 'allowEmpty' => true, 'message' => 'Must be limited and have valid edition sized to be numbered.'),
	//pieces
	'pieces' => array('numeric' => array('rule' => 'numeric', 'allowEmpty' => true, 'message' => 'Must be numeric.'), 'maxLength' => array('rule' => array('maxLength', 12), 'message' => 'Invalid length.')));

	// function validateName($check) {
	// debug($check['name']);
	// return preg_match("/^[A-Za-z0-9\s#:.'-]+\z/", $check['name']) === 1;
	// }
	function beforeSave() {
		$this -> data = $this -> processBeforeSave($this -> data);
		return true;
	}

	private function processBeforeSave($data) {
		$returnData = $data;
		//Update Edition Size stuff
		//This just makes sure that if limited is not set, we clear out the edition size
		if (isset($returnData['Collectible']['limited'])) {
			$limited = $returnData['Collectible']['limited'];
			if (isset($returnData['Collectible']['edition_size'])) {
				$editionSize = $returnData['Collectible']['edition_size'];
				if (trim($editionSize) != '' && !$limited) {
					$returnData['Collectible']['edition_size'] = '';
				}
			}
		}

		//For whatever reason, cakephp year the put another array under the field
		//Ok so we are allowing release to be null now cause it makes most sense
		//Before saving if it is empty or it is = to 0000 unset it completely
		//so it sets it to null.
		if (isset($returnData['Collectible']['release'])) {
			if (is_array($returnData['Collectible']['release'])) {
				if ($returnData['Collectible']['release']['year'] != '0000' && $returnData['Collectible']['release']['year'] !== '') {
					$year = $returnData['Collectible']['release']['year'];
					$returnData['Collectible']['release'] = $year;
				} else {
					unset($returnData['Collectible']['release']);
				}

			}

		}

		//Check to see if these are set, if they are not, default them to false
		//8-30-11 commented this out because of edit collectible overriding existing data
		// if (!isset($this -> data['Collectible']['exclusive'])) {
		// $this -> data['Collectible']['exclusive'] = 0;
		// }
		// if (!isset($this -> data['Collectible']['variant'])) {
		// $this -> data['Collectible']['variant'] = 0;
		// }
		if (isset($returnData['Collectible']['msrp'])) {
			$returnData['Collectible']['msrp'] = str_replace('$', '', $returnData['Collectible']['msrp']);
			$returnData['Collectible']['msrp'] = str_replace(',', '', $returnData['Collectible']['msrp']);
		}
		//It always should be but just double check
		//Trim the white space away from beginning and end, since this is a core search field, keep it clean
		if (isset($returnData['Collectible']['name'])) {
			$returnData['Collectible']['name'] = trim($data['Collectible']['name']);
		}
		if (isset($returnData['Collectible']['description'])) {
			$returnData['Collectible']['description'] = trim($returnData['Collectible']['description']);
		}

		// If it is set already well then don't do anything
		if (!isset($returnData['Collectible']['retailer_id'])) {
			if (isset($returnData['Collectible']['retailer']) && !empty($returnData['Collectible']['retailer'])) {
				$existingRetailer = $this -> Retailer -> find('first', array('conditions' => array('Retailer.name' => $returnData['Collectible']['retailer'])));
				/*
				 * If it does exist, link that one, otherwise add it and then use that id
				 */
				if (!empty($existingRetailer)) {
					$returnData['Collectible']['retailer_id'] = $existingRetailer['Retailer']['id'];
				} else {
					$newRetailer = array();
					$newRetailer['Retailer']['name'] = $returnData['Collectible']['retailer'];
					$this -> Retailer -> create();
					if ($this -> Retailer -> saveAll($newRetailer)) {
						$returnData['Collectible']['retailer_id'] = $this -> Retailer -> id;
					} else {
						return false;
					}
				}
			}
		}

		return $returnData;
	}

	/**
	 * This is kind of lame but this is a call back to handle what to do before
	 * editing this collectible.
	 */
	public function beforeSaveEdit($editData) {
		return $this -> processBeforeSave($editData);
	}

	function doAfterFind($results, $primary = false) {
		if ($results) {
			$showEditionSize = false;
			//TODO not sure this is really needed anymore
			if (isset($results['edition_size'])) {
				if (is_numeric($results['edition_size'])) {
					$showEditionSize = true;
				}
			}
			//TODO figure out a better way to do this
			$results['showUserEditionSize'] = $showEditionSize;
			//Cleans up default no set years
			//Removing this now because of the edit...a release of 0000 just means no year...probably should allow null
			if (isset($results['release']) && $results['release'] === '0000') {
				$results['release'] = '';
			}

			if (isset($results['series_id']) && !empty($results['series_id'])) {
				$fullSeriesPath = $this -> Series -> buildSeriesPathName($results['series_id']);
				$results['seriesPath'] = $fullSeriesPath;
			}
		}
		return $results;
	}

	/**
	 * This is a helper method that will update a series path if a series has
	 * been added for the passed in collectible.  Not pretty but this is used for the
	 * cases that we cannot use the after fine helper method.
	 */
	public function addSeriesPath(&$collectible) {
		if (isset($collectible['Collectible']['series_id']) && !empty($collectible['Collectible']['series_id'])) {
			$fullSeriesPath = $this -> Series -> buildSeriesPathName($collectible['Collectible']['series_id']);
			$collectible['Collectible']['seriesPath'] = $fullSeriesPath;
		}
	}

	function validateProductWidthDepthId($check) {
		$collectibleTypeId = $this -> data['Collectible']['collectibletype_id'];

		if ($collectibleTypeId != 1 && empty($check['collectibletype_id'])) {
			return false;
		} else {
			return true;
		}
	}

	function validateNumbered($check) {
		debug($this -> data['Collectible']['limited'] === '1');
		if (isset($check['numbered']) && $check['numbered'] === '1' && isset($this -> data['Collectible']['limited']) && $this -> data['Collectible']['limited'] === '1' && empty($this -> data['Collectible']['edition_size'])) {
			return false;
		}

		return true;
	}

	function validateEditionSize($check) {
		$isValid = false;
		$isInt = false;
		$editionSize = trim($check['edition_size']);

		//If it is unknown leave empty, which will eventually be a zero.
		if ($editionSize == '') {
			return true;
		}

		// First check if it's a numeric value as either a string or number
		if (is_numeric($editionSize) === TRUE) {
			// It's a number, but it has to be an integer
			if ((int)$editionSize == $editionSize) {
				if ($editionSize > 0) {
					return TRUE;
				}
				// return $isInt;
				// It's a number, but not an integer, so we fail
			}
			// Not a number
		}

		return false;
	}

	function validateManufactureId($check) {
		$result = $this -> Manufacture -> find('count', array('id' => $check['manufacture_id']));
		return $result > 0;
	}

	function validateLicenseId($check) {
		$result = $this -> Manufacture -> LicensesManufacture -> find('first', array('conditions' => array('LicensesManufacture.manufacture_id' => $this -> data['Collectible']['manufacture_id'], 'LicensesManufacture.license_id' => $check['license_id']), 'contain' => false));
		debug($result);
		if ($result) {
			return true;

		} else {
			return false;
		}
	}

	function validateCollectibleType($check) {
		$result = $this -> Manufacture -> CollectibletypesManufacture -> find('first', array('conditions' => array('CollectibletypesManufacture.manufacture_id' => $this -> data['Collectible']['manufacture_id'], 'CollectibletypesManufacture.collectibletype_id' => $check['collectibletype_id']), 'contain' => false));
		debug($result);
		if ($result) {
			return true;

		} else {
			return false;
		}
	}

	/*
	 * This is going to validate the series based on the manufacturer.  If the manufacturer does not
	 * have a series id set, then it will let it pass as null
	 *
	 * If the manufacturer does have a series id, then a series id MUST be set.
	 */
	function validateSeriesId($check) {
		//grab the manufacturer first
		$manufacturer = $this -> Manufacture -> find('first', array('conditions' => array('Manufacture.id' => $this -> data['Collectible']['manufacture_id']), 'contain' => false));

		if (!empty($manufacturer['Manufacture']['series_id'])) {

			//Check to see if a series is set
			if (isset($check['series_id']) && !empty($check['series_id'])) {
				/*
				 * To validate this we need to get the parent of this series and see if the parent matches in the database
				 * If the getparentnode call, returns nothing, that means we are at the top level already
				 */
				$paths = $this -> Series -> getPath($check['series_id']);

				if (!empty($paths)) {
					reset($paths);
					$parentNode = current($paths);
					$parentSeriesId = $parentNode['Series']['id'];
					//Now query to see if this is a valid hierarchy
					if ($manufacturer['Manufacture']['series_id'] === $parentSeriesId) {
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				/*
				 * Returning false because there is a possible series but the series did
				 * not select it is so it is invalid.
				 */
				return false;
			}
		} else {
			//if there is no series id for this manufacturer then make sure we unset
			unset($this -> data['Collectible']['series_id']);
			return true;
		}
	}

	public function getCollectibleNameById($collectibleId) {
		//$this->Behaviors->attach('Containable');
		$result = $this -> find("first", array("conditions" => array("Collectible.id" => $collectibleId), ));

		return $result['Collectible']['name'];
	}

	public function getAllCollectibles() {
		return $this -> find('all');
	}

	public function getPendingCollectibles() {
		$collectible = $this -> find("all", array('conditions' => array('Collectible.state' => 1)));

		return $collectible;
	}

	public function getNumberOfPendingCollectibles() {
		$count = $this -> find("count", array('conditions' => array('Collectible.state' => 1)));

		return $count;
	}

	public function getPendingCollectiblesByUserId($userId) {
		$count = $this -> find("count", array('conditions' => array('Collectible.user_id' => $userId, 'Collectible.state' => 1)));
		return $count;
	}

	public function getNumberofCollectiblesInStash($collectibleId) {
		$count = $this -> CollectiblesUser -> find("count", array('conditions' => array('CollectiblesUser.collectible_id' => $collectibleId)));
		$this -> CollectiblesUser -> Behaviors -> attach('Containable');
		//TODO finish this, we want to return all userids to output other users hwo have this
		$count2 = $this -> CollectiblesUser -> find("all", array('conditions' => array('CollectiblesUser.collectible_id' => $collectibleId), 'contain' => array('Stash' => array('fields' => 'user_id')), 'group' => array('stash_id')));

		return $count;
	}

	/**
	 * This method will return a list of collectible variants by
	 * the given id.
	 */
	public function getCollectibleVariants($collectibleId) {
		$collectibles = $this -> find('all', array('contain' => array('CollectiblesUpload' => array('Upload')), 'conditions' => array('Collectible.variant_collectible_id' => $collectibleId, 'Collectible.state' => 0)));

		return $collectibles;
	}

	/**
	 * This method will return a count of all collectibles that have been approved
	 */
	public function getCollectibleCount() {
		$collectiblesCount = $this -> find('count', array('conditions' => array('Collectible.state' => 0)));
		return $collectiblesCount;
	}

	/**
	 * This method, give a Collectible model, will check to see if any other collectibles currently exist. If
	 * they do it will return a list of those collectibles.
	 */
	public function doesCollectibleExist($collectible = null) {
		//if (UPC) OR (Manufacturer AND Product Code) OR (Manufacturer AND License AND CollectibleType AND LIKE Name)
		$returnList = array();

		if (!is_null($collectible) && isset($collectible['Collectible'])) {
			//This will be used to store all conditions from this search
			$conditions = array();
			$orConditions = array();
			/**
			 * To Handle an OR situation it needs to be organized like so
			 * array('OR'=>array(blah=>blah,blah=>blah),id=>blah,id=>blah)
			 *
			 * Anything inside of an array inside of an OR will automatically be AND for you
			 */
			//First check to see if we have a UPC
			if (isset($collectible['Collectible']['upc']) && !empty($collectible['Collectible']['upc'])) {
				array_push($orConditions, array('Collectible.upc' => $collectible['Collectible']['upc']));
			}

			//If we have a product code lets check against that too
			if (isset($collectible['Collectible']['code']) && !empty($collectible['Collectible']['code'])) {
				array_push($orConditions, array('Collectible.code' => $collectible['Collectible']['code'], 'Collectible.manufacture_id' => $collectible['Collectible']['manufacture_id']));
			}

			//Always add this last one:
			//(Manufacturer AND License AND CollectibleType AND LIKE Name
			array_push($orConditions, array('Collectible.manufacture_id' => $collectible['Collectible']['manufacture_id'], 'Collectible.license_id' => $collectible['Collectible']['license_id'], 'Collectible.collectibletype_id' => $collectible['Collectible']['collectibletype_id'], 'Collectible.name LIKE' => '%' . $collectible['Collectible']['name'] . '%'));
			//Now add all these to an OR
			array_push($conditions, array('OR' => $orConditions));
			/*
			 * Check to see if this is a variant, if it is variant then lets only return
			 * collectibles that they are the same variant of
			 */
			if (isset($collectible['Collectible']['variant']) && $collectible['Collectible']['variant']) {
				array_push($conditions, array('AND' => array('Collectible.variant_collectible_id' => $collectible['Collectible']['variant_collectible_id'])));
			}
			debug($conditions);

			$returnList = $this -> find("all", array("conditions" => array($conditions), "contain" => array('SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'))));
		}

		return $returnList;

	}

	public function publishEdit($editId) {
		$retVal = false;
		//Grab out edit collectible
		$collectibleEditVersion = $this -> findEdit($editId);
		debug($collectibleEditVersion);
		$collectible = array();

		$currentVersionCollectible = $this -> find("first", array('contain' => false, 'conditions' => array('Collectible.id' => $collectibleEditVersion['CollectibleEdit']['base_id'])));
		debug($currentVersionCollectible);
		$collectible = $this -> compareEdit($collectibleEditVersion, $currentVersionCollectible);

		/*
		 * Lets build our update array based on what has changed from the latest version of the collectible(as of now:)) and the one we are editing.  We only
		 * want to submit those changes, no need to update the rest.  We might overwrite changes here by accident.  If this becomes a problem then we will have
		 * to indicate at the edit process, exactly what the user changed so we do not do any accidental updates...TODO
		 */
		$changedString = '_changed';
		$updateFields = array();
		$changed = false;
		foreach ($collectible['Collectible'] as $key => $value) {
			if (substr_compare($key, $changedString, -strlen($changedString), strlen($changedString)) === 0) {
				//product_width_changed
				//0, 14
				//total length - (_changed) length
				$field = substr($key, 0, strlen($key) - strlen($changedString));
				//$updateFields[$field] = $collectible['Collectible'][$field];
				//$updateFields['Collectible.'.$field] = '\''.$collectible['Collectible'][$field].'\'';
				$updateFields['Collectible'][$field] = $collectible['Collectible'][$field];
				$changed = true;
			}
		}

		if ($changed) {
			$updateFields['Revision']['action'] = 'E';
			if (!is_null($notes)) {
				$updateFields['Revision']['notes'] = $notes;
			}
			//Make sure I grab the user id that did this edit
			$updateFields['Revision']['user_id'] = $collectibleEditVersion['CollectibleEdit']['edit_user_id'];
			$updateFields['Collectible']['id'] = $collectibleEditVersion['CollectibleEdit']['base_id'];

			if ($this -> saveAll($updateFields, array('validate' => false))) {
				$retVal = true;
			}
			if ($retVal) {
				$message = 'We have approved your change to the following <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectibleEditVersion['CollectibleEdit']['base_id'] . '">' . $collectibleEditVersion['CollectibleEdit']['name'] . '</a>';
				$this -> notifyUser($collectibleEditVersion['CollectibleEdit']['edit_user_id'], $message);
			}

		} else {
			$retVal = $this -> denyEdit($editId);
		}

		return $retVal;
	}

	public function denyEdit($editId) {
		$retVal = false;
		debug($editId);
		// Grab the fields that will need to updated
		$collectibleEditVersion = $this -> findEdit($editId);
		debug($collectibleEditVersion);
		// Right now we can really only add or edit
		if ($collectibleEditVersion['Action']['action_type_id'] === '1') {//Add
			//TODO: Add does not go through here yet so it should not happen

		} else if ($collectibleEditVersion['Action']['action_type_id'] === '2') {// Edit
			if ($this -> deleteEdit($collectibleEditVersion)) {
				$retVal = true;
			}

		} else if ($collectibleEditVersion['Action']['action_type_id'] === '4') {// Delete
			// If we are deny a delete, then we are keeping it out there
			// so just delete the edit
			if ($this -> deleteEdit($collectibleEditVersion)) {
				$retVal = true;
			}

		}

		if ($retVal) {
			$message = 'We have denied your change to the following <a href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectibleEditVersion['CollectibleEdit']['base_id'] . '">' . $collectibleEditVersion['CollectibleEdit']['name'] . '</a>';
			$this -> notifyUser($collectibleEditVersion['CollectibleEdit']['edit_user_id'], $message);
		}

		return $retVal;
	}

	public function add($collectible, $userId) {

		/* Since they confirmed, now set to pending = 1.  I really don't like how
		 this is setup right now but it works because of the image thing.
		 A 1 means that this collectible needs to be approved by an admin first
		 *
		 * TODO: If we are not auto approving, then do we need to make sure that attributes_collectible is set to not active?
		 * */
		$pendingState = '1';
		if (Configure::read('Settings.Approval.auto-approve') == 'true') {
			$pendingState = '0';
		}

		$collectible['Collectible']['state'] = $pendingState;

		//user id of the person who added this collectible
		$collectible['Collectible']['user_id'] = $userId;

		//Adding this here, we need to clean this up later and put it all in the model
		$collectible['EntityType']['type'] = 'collectible';

		$dataSource = $this -> getDataSource();
		$dataSource -> begin();

		$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> ADD, null);
		if ($this -> Revision -> save($revision)) {
			$revisionId = $this -> Revision -> id;
			$collectible['Collectible']['revision_id'] = $revisionId;

			if (isset($collectible['AttributesCollectible']) && !empty($collectible['AttributesCollectible'])) {
				foreach ($collectible['AttributesCollectible'] as $key => $attributesCollectible) {
					$collectible['AttributesCollectible'][$key]['revision_id'] = $revisionId;
					// If the attribute id is set, that means this is an existing attribute we are adding,
					// otherwise if I don't have an attribute id, then this is a new one
					if (!isset($attributesCollectible['attribute_id']) && isset($attributesCollectible['Attribute'])) {
						$collectible['AttributesCollectible'][$key]['Attribute']['revision_id'] = $revisionId;
						$collectible['AttributesCollectible'][$key]['Attribute']['user_id'] = $userId;
						$collectible['AttributesCollectible'][$key]['Attribute']['EntityType']['type'] = 'attribute';
						$collectible['AttributesCollectible'][$key]['Attribute']['status_id'] = 2;
					}
				}
			}

			if (isset($collectible['CollectiblesUpload'])) {
				// This should also update the revision for the upload
				// since we are only allowing one on submission this should be fine
				$collectible['CollectiblesUpload'][0]['revision_id'] = $revisionId;
				$collectible['CollectiblesUpload'][0]['status_id'] = 2;
				$collectible['CollectiblesUpload'][0]['Upload']['revision_id'] = $revisionId;
				$collectible['CollectiblesUpload'][0]['Upload']['id'] = $collectible['CollectiblesUpload'][0]['upload_id'];
			}

			$this -> create();
			debug($collectible);
			if ($this -> saveAll($collectible, array('validate' => false, 'deep' => true))) {				$dataSource -> commit();
				$id = $this -> id;
				$addCollectible = $this -> findById($id);
				$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$USER_SUBMIT_NEW, 'user' => $addCollectible, 'object' => $addCollectible, 'type' => 'Collectible')));
				return true;
			} else {
				$dataSource -> rollback();
				return false;
			}
		} else {
			$dataSource -> rollback();
			return false;
		}
	}

}
?>
