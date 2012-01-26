<?php
class Collectible extends AppModel {
	var $name = 'Collectible';
	var $belongsTo = array('SpecializedType' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'Revision', 'Manufacture' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'Collectibletype' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'License' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'Series', 'Scale' => array('counterCache' => true, 'counterScope' => array('Collectible.state' => 0)), 'Retailer', 'User' => array('counterCache' => true));

	var $hasMany = array('CollectiblesUser', 'Upload' => array('dependent' => true), 'AttributesCollectible' => array('dependent' => true), 'CollectiblesTag' => array('dependent' => true));

	var $actsAs = array('Revision' => array('model' => 'CollectibleRev'), 'Containable', 'Sluggable' => array(
	/**
	 * Ok so I want to build slugs on the fly instead of a database field, cause then I would
	 * have to worry about updates and shit...
	 *
	 * The problem is, the slug I want to build for this one has associations i want to bind,
	 * so I am thinking I set those below like so to grab those associations.  If the first one
	 * in the arry is not "Model", then do it on the model alias
	 */
		'displayField' => array('field1' => array('Model' => 'Manufacture', 'Field' => 'title'), 'field2' => array('Model' => 'License', 'Field' => 'name'), 'field3' => array('Model' => 'Collectible', 'Field' => 'name'), 'field4' => array('Model' => 'Collectibletype', 'Field' => 'name')), 'showPrimary' => false,
	// 'slugField' => 'theNameOfYourSlugVirtualField',
		'replacement' => '-' //the char to implode the words in entry name...
	));

	var $validate = array(
	//name field
		'name' => array('rule' => '/^[\\w\\s-.:&#]+$/', 'required' => true, 'message' => 'Invalid characters'),
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
		'edition_size' => array('rule' => array('validateEditionSize'), 'message' => 'Must be numeric.'),
	//upc
		'upc' => array('numeric' => array('rule' => 'numeric', 'allowEmpty' => true, 'message' => 'Must be numeric.'), 'maxLength' => array('rule' => array('maxLength', 12), 'message' => 'Invalid length.')),
	//product code
		'code' => array('numeric' => array('rule' => 'alphanumeric', 'allowEmpty' => true, 'message' => 'Must be alphanumeric.'), 'maxLength' => array('rule' => array('maxLength', 50), 'message' => 'Invalid length.')),
	//This should be decmial or blank
		'product_length' => array('rule' => '/^(?:\d{1,3}(?:\.\d{0,6})?)?$/', 'allowEmpty' => true, 'message' => 'Must be a valid height.'),
	//This should be decmial or blank
		'product_width' => array('validValues' => array('rule' => '/^(?:\d{1,3}(?:\.\d{0,6})?)?$/', 'message' => 'Must be a valid width.'), ),
	//This should be decmial or blank
		'product_depth' => array('validValues' => array('rule' => '/^(?:\d{1,3}(?:\.\d{0,6})?)?$/', 'message' => 'Must be a valid depth.'), ),
	//url
		'url' => array('rule' => 'url', 'required' => true, 'message' => 'Must be a valid url.'),
	//numbered
		'numbered' => array('rule' => array('validateNumbered'), 'message' => 'Must be limited and have valid edition sized to be numbered.'));

	function beforeSave() {
		debug($this -> data);
		//Update Edition Size stuff
		//This just makes sure that if limited is not set, we clear out the edition size
		if (isset($this -> data['Collectible']['limited'])) {
			$limited = $this -> data['Collectible']['limited'];
			if (isset($this -> data['Collectible']['edition_size'])) {
				$editionSize = $this -> data['Collectible']['edition_size'];
				if (trim($editionSize) != '' && !$limited) {
					$this -> data['Collectible']['edition_size'] = '';
				}
			}
		}

		//For whatever reason, cakephp year the put another array under the field
		if (isset($this -> data['Collectible']['release']) && is_array($this -> data['Collectible']['release'])) {
			$year = $this -> data['Collectible']['release']['year'];
			$this -> data['Collectible']['release'] = $year;
		}

		//Check to see if these are set, if they are not, default them to false
		//8-30-11 commented this out because of edit collectible overriding existing data
		// if (!isset($this -> data['Collectible']['exclusive'])) {
		// $this -> data['Collectible']['exclusive'] = 0;
		// }
		// if (!isset($this -> data['Collectible']['variant'])) {
		// $this -> data['Collectible']['variant'] = 0;
		// }
		if (isset($this -> data['Collectible']['msrp'])) {
			$this -> data['Collectible']['msrp'] = str_replace('$', '', $this -> data['Collectible']['msrp']);
			$this -> data['Collectible']['msrp'] = str_replace(',', '', $this -> data['Collectible']['msrp']);
		}
		//It always should be but just double check
		//Trim the white space away from beginning and end, since this is a core search field, keep it clean
		if (isset($this -> data['Collectible']['name'])) {
			$this -> data['Collectible']['name'] = trim($this -> data['Collectible']['name']);
		}

		debug($this -> data);
		return true;
	}

	function doAfterFind($results, $primary = false) {
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
		if (isset($results['release']) && $results['release'] === '0000') {
			$results['release'] = '';
		}

		if(isset($results['series_id']) && !empty($results['series_id'])){
			$fullSeriesPath = $this -> Series -> buildSeriesPathName($results['series_id']);
			$results['seriesPath'] = $fullSeriesPath;
		}
		return $results;
	}
	
	/**
	 * This is a helper method that will update a series path if a series has
	 * been added for the passed in collectible.  Not pretty but this is used for the
	 * cases that we cannot use the after fine helper method.
	 */
	public function addSeriesPath(&$collectible){
		if(isset($collectible['Collectible']['series_id']) && !empty($collectible['Collectible']['series_id'])){
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
		debug($count2);

		return $count;
	}

	/**
	 * This method will return a list of collectible variants by
	 * the given id.
	 */
	public function getCollectibleVariants($collectibleId) {
		$collectibles = $this -> find('all', array('conditions' => array('Collectible.variant_collectible_id' => $collectibleId, 'Collectible.state' => 0)));

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

			$returnList = $this -> find("all", array("conditions" => array($conditions), "contain" => array('SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'Upload')));
		}

		return $returnList;

	}

}
?>
