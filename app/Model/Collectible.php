<?php
/**
 * Decided against a Custom model/table for now because there was only one field.
 *
 * If I have other fields eventually then we can expand it into another table
 */
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class Collectible extends AppModel {
	public $name = 'Collectible';
	public $belongsTo = array('CollectiblePriceFact', 'CustomStatus', 'Status', 'EntityType' => array('dependent' => true), 'SpecializedType' => array('counterCache' => true, 'counterScope' => array('Collectible.status_id' => 4)), 'Revision' => array('dependent' => true), 'Manufacture' => array('counterCache' => true, 'counterScope' => array('Collectible.status_id' => 4)), 'Collectibletype' => array('counterCache' => true, 'counterScope' => array('Collectible.status_id' => 4)), 'License' => array('counterCache' => true, 'counterScope' => array('Collectible.status_id' => 4)), 'Series', 'Scale' => array('counterCache' => true, 'counterScope' => array('Collectible.status_id' => 4)), 'Retailer' => array('counterCache' => true, 'counterScope' => array('Collectible.status_id' => 4)), 'User' => array('counterCache' => true, 'counterScope' => array('Collectible.status_id' => 4)), 'Currency');
	public $hasMany = array('Transaction', 'Listing' => array('dependent' => true), 'CollectiblesUser' => array('dependent' => true), 'CollectiblesUpload' => array('dependent' => true), 'AttributesCollectible' => array('dependent' => true), 'CollectiblesTag' => array('dependent' => true), 'ArtistsCollectible' => array('dependent' => true));
	public $actsAs = array('Editable' => array('type' => 'collectible', 'model' => 'CollectibleEdit', 'modelAssociations' => array('belongsTo' => array('SpecializedType', 'Manufacture', 'Collectibletype', 'License', 'Scale', 'Series', 'Retailer', 'Currency')), 'compare' => array('official', 'signed', 'name', 'manufacture_id', 'specialized_type_id', 'collectibletype_id', 'description', 'msrp', 'edition_size', 'numbered', 'upc', 'product_width', 'product_depth', 'license_id', 'series_id', 'variant', 'url', 'exclusive', 'retailer_id', 'variant_collectible_id', 'product_length', 'product_weight', 'scale_id', 'release', 'limited', 'code', 'pieces', 'currency_id')), 'Revision' => array('model' => 'CollectibleRev', 'ignore' => array('collectibles_user_count', 'entity_type_id', 'status_id')), 'Containable', 'Sluggable' => array(
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
	//manufacturer is now not required for any types.  This is for customs and pieces that might not have a manufacturer
	'manufacture_id' => array('rule' => array('validateManufactureId'), 'required' => false, 'allowEmpty' => true, 'message' => 'Must be a valid manufacture.'),
	//collectible type field
	'collectibletype_id' => array('rule' => array('validateCollectibleType'), 'required' => true, 'message' => 'Must be a valid type.'),
	//license filed
	// updating so that a brand is now not officially required
	'license_id' => array('rule' => array('validateLicenseId'), 'required' => false, 'allowEmpty' => true, 'message' => 'Brand/License must be valid for Manufacture.'),
	//series field
	'series_id' => array('rule' => array('validateSeriesId'), 'message' => 'Please select a valid category.'),
	//description field
	'description' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Description is required.'), 'maxLength' => array('rule' => array('maxLength', 1000), 'message' => 'Invalid length.')),
	//msrp
	'msrp' => array('rule' => array('money', 'left'), 'required' => false, 'allowEmpty' => true, 'message' => 'Please supply a valid monetary amount.'),
	//edition_size
	'edition_size' => array('rule' => array('validateEditionSize'), 'allowEmpty' => true, 'message' => 'Must be numeric.'),
	//retailer
	'retailer' => array('minLength' => array('rule' => array('minLength', 4), 'allowEmpty' => true, 'message' => 'Retailer/Venue must be at least 4 characters.'), 'maxLength' => array('rule' => array('maxLength', 150), 'message' => 'Cannot be more than 150 characters.')),
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
		// if it is a custom, also set official to false
		// just to make sure nothing gets passed the client side
		if (isset($returnData['Collectible']['custom']) && $returnData['Collectible']['custom']) {
			$returnData['Collectible']['official'] = false;
			$returnData['Collectible']['limited'] = true;
			$returnData['Collectible']['edition_size'] = 1;
		} else if (isset($returnData['Collectible']['original']) && $returnData['Collectible']['original']) {
			$returnData['Collectible']['limited'] = true;
			$returnData['Collectible']['edition_size'] = 1;
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

	/**
	 * Decided to directly use afterFind to get more power
	 * some dups in code but I think this gives me the ability
	 * to do whatever I need
	 */
	function afterFind($results, $primary = false) {
		if ($results) {
			// If it is primary handle all of these things
			if ($primary) {
				foreach ($results as $key => $val) {
					if (isset($val['Collectible'])) {

						$showEditionSize = false;
						//TODO not sure this is really needed anymore
						if (isset($val['Collectible']['edition_size'])) {
							if (is_numeric($val['Collectible']['edition_size'])) {
								$showEditionSize = true;
							}
						}
						//TODO figure out a better way to do this
						$results[$key]['Collectible']['showUserEditionSize'] = $showEditionSize;
						//Cleans up default no set years
						//Removing this now because of the edit...a release of 0000 just means no year...probably should allow null
						if (isset($val['Collectible']['release']) && $val['Collectible']['release'] === '0000') {
							$results[$key]['Collectible']['release'] = '';
						}

						if (isset($val['Collectible']['series_id']) && !empty($val['Collectible']['series_id'])) {
							$fullSeriesPath = $this -> Series -> buildSeriesPathName($val['Collectible']['series_id']);
							$results[$key]['Collectible']['seriesPath'] = $fullSeriesPath;
						}

						if (isset($val['Collectible']['retailer_id']) && !empty($val['Collectible']['retailer_id'])) {
							$existingRetailer = $this -> Retailer -> find('first', array('contain' => false, 'conditions' => array('Retailer.id' => $val['Collectible']['retailer_id'])));
							$results[$key]['Collectible']['retailer'] = $existingRetailer['Retailer']['name'];
						}

						$descriptionTitle = $val['Collectible']['name'];

						if (isset($val['Collectibletype']) && !empty($val['Collectibletype'])) {
							$descriptionTitle = $descriptionTitle . ' ' . $val['Collectibletype']['name'];
						}

						if (isset($val['Collectible']['custom']) && $val['Collectible']['custom']) {
							$results[$key]['Collectible']['displayTitle'] = $val['Collectible']['name'] . __(' a custom by ') . $val['User']['username'];
							$descriptionTitle = $val['Collectible']['name'] . __(' a custom by ') . $val['User']['username'];
						} else if ((isset($val['Manufacture']) && !empty($val['Manufacture'])) || (isset($val['ArtistsCollectible']) && !empty($val['ArtistsCollectible']))) {
							$itemTitle = $val['Collectible']['name'] . ' By ';

							$descriptionTitle = $descriptionTitle . ' By ';

							if ($val['Collectible']['collectibletype_id'] === Configure::read('Settings.CollectibleTypes.Print')) {
								if (!empty($val['ArtistsCollectible'])) {
									// assume the first on is primary for now :)
									$artist = $val['ArtistsCollectible'][0];
									$itemTitle .= $artist['Artist']['name'];
									$descriptionTitle .= $artist['Artist']['name'];
								} else if (!empty($val['Manufacture'])) {
									// otherwise if there is a manufacturer, use that
									$itemTitle .= $val['Manufacture']['title'];
									$descriptionTitle .= $val['Manufacture']['title'];
								}
							} else if (!empty($val['Manufacture']['title'])) {
								$itemTitle .= $val['Manufacture']['title'];
								$descriptionTitle .= $val['Manufacture']['title'];
							} else if (!empty($val['ArtistsCollectible'])) {
								// assume the first on is primary for now :)
								$artist = $val['ArtistsCollectible'][0];

								$itemTitle .= $artist['Artist']['name'];
								$descriptionTitle .= $artist['Artist']['name'];
							}

							$results[$key]['Collectible']['displayTitle'] = $itemTitle;
						} else {

							// fall back
							if (isset($val['Collectible']['name'])) {
								$results[$key]['Collectible']['displayTitle'] = $val['Collectible']['name'];
							} else {
								$results[$key]['Collectible']['displayTitle'] = '';
							}

						}

						$results[$key]['Collectible']['descriptionTitle'] = $descriptionTitle;

						if (isset($val['Collectible']['custom']) && !$val['Collectible']['custom']) {
							unset($results[$key]['CustomStatus']);
						}

						if (!isset($val['Collectible']['collectible_price_fact_id']) || is_null($val['Collectible']['collectible_price_fact_id'])) {
							unset($results[$key]['CollectiblePriceFact']);
						}
					}
				}
			} else {
				if (isset($results[$this -> primaryKey])) {
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

					if (isset($results['retailer_id']) && !empty($results['retailer_id'])) {
						$existingRetailer = $this -> Retailer -> find('first', array('contain' => false, 'conditions' => array('Retailer.id' => $results['retailer_id'])));
						$results['retailer'] = $existingRetailer['Retailer']['name'];
					}

					$descriptionTitle = $results['name'];

					if (isset($results['Collectibletype']) && !empty($results['Collectibletype'])) {
						$descriptionTitle = $descriptionTitle . ' ' . $results['Collectibletype']['name'];
					}

					if (isset($results['custom']) && $results['custom']) {
						if (isset($results['User'])) {
							$results['displayTitle'] = $results['name'] . __(' a custom by ') . $results['User']['username'];
							$descriptionTitle = $results['name'] . __(' a custom by ') . $results['User']['username'];
						} else {
							$results['displayTitle'] = $results['name'] . __(' a custom');
							$descriptionTitle = $results['name'] . __(' a custom');
						}

					} else if ((isset($results['Manufacture']) && !empty($results['Manufacture'])) || (isset($results['ArtistsCollectible']) && !empty($results['ArtistsCollectible']))) {
						$itemTitle = $results['name'] . ' By ';
						$descriptionTitle = $descriptionTitle . ' By ';

						if ($results['collectibletype_id'] === Configure::read('Settings.CollectibleTypes.Print')) {
							if (!empty($results['ArtistsCollectible'])) {
								// assume the first on is primary for now :)
								$artist = $results['ArtistsCollectible'][0];
								$itemTitle .= $artist['Artist']['name'];
								$descriptionTitle .= $artist['Artist']['name'];
							} else if (!empty($results['Manufacture'])) {
								// otherwise if there is a manufacturer, use that
								$itemTitle .= $results['Manufacture']['title'];
								$descriptionTitle .= $results['Manufacture']['title'];
							}
						} else if (!empty($results['Manufacture']['title'])) {
							// otherwise if there is a manufacturer, use that
							$itemTitle .= $results['Manufacture']['title'];
							$descriptionTitle .= $results['Manufacture']['title'];
						} else if (!empty($results['ArtistsCollectible'])) {
							// assume the first on is primary for now :)
							$artist = $results['ArtistsCollectible'][0];
							$itemTitle .= $artist['Artist']['name'];
							$descriptionTitle .= $artist['Artist']['name'];
						}

						$results['displayTitle'] = $itemTitle;
					} else {

						// fall back
						if (isset($results['name'])) {
							$results['displayTitle'] = $results['name'];
						} else {
							$results['displayTitle'] = '';
						}

					}

					$results['descriptionTitle'] = $descriptionTitle;

					if (isset($results['custom']) && !$results['custom']) {
						unset($results['CustomStatus']);
					}

					if (!isset($results['collectible_price_fact_id']) || is_null($results['collectible_price_fact_id'])) {
						unset($results['CollectiblePriceFact']);
					}
				} else {

					foreach ($results as $key => $val) {
						$showEditionSize = false;
						//TODO not sure this is really needed anymore
						if (isset($val['Collectible']['edition_size'])) {
							if (is_numeric($val['Collectible']['edition_size'])) {
								$showEditionSize = true;
							}
						}
						//TODO figure out a better way to do this
						$results[$key]['Collectible']['showUserEditionSize'] = $showEditionSize;
						//Cleans up default no set years
						//Removing this now because of the edit...a release of 0000 just means no year...probably should allow null
						if (isset($val['Collectible']['release']) && $val['Collectible']['release'] === '0000') {
							$results[$key]['Collectible']['release'] = '';
						}

						if (isset($val['Collectible']['series_id']) && !empty($val['Collectible']['series_id'])) {
							$fullSeriesPath = $this -> Series -> buildSeriesPathName($val['Collectible']['series_id']);
							$results[$key]['Collectible']['seriesPath'] = $fullSeriesPath;
						}

						if (isset($val['Collectible']['retailer_id']) && !empty($val['Collectible']['retailer_id'])) {
							$existingRetailer = $this -> Retailer -> find('first', array('contain' => false, 'conditions' => array('Retailer.id' => $val['Collectible']['retailer_id'])));
							$results[$key]['Collectible']['retailer'] = $existingRetailer['Retailer']['name'];
						}

						if (isset($val['Collectible']['name'])) {
							$descriptionTitle = $val['Collectible']['name'];
						}

						if (isset($val['Collectibletype']) && !empty($val['Collectibletype'])) {
							$descriptionTitle = $descriptionTitle . ' ' . $val['Collectibletype']['name'];
						}

						if (isset($val['Collectible']['custom']) && $val['Collectible']['custom']) {
							$results[$key]['Collectible']['displayTitle'] = $val['Collectible']['name'] . __(' a custom');
							$descriptionTitle = $val['Collectible']['name'] . __(' a custom');
						} else {
							// fall back
							if (isset($val['Collectible']['name'])) {
								$results[$key]['Collectible']['displayTitle'] = $val['Collectible']['name'];
							} else {
								$results[$key]['Collectible']['displayTitle'] = '';
							}
						}

						$results[$key]['Collectible']['descriptionTitle'] = $descriptionTitle;

						if (isset($val['Collectible']['custom']) && !$val['Collectible']['custom']) {
							unset($results[$key]['CustomStatus']);
						}

						if (!isset($val['Collectible']['collectible_price_fact_id']) || is_null($val['Collectible']['collectible_price_fact_id'])) {
							unset($results[$key]['CollectiblePriceFact']);
						}
					}
				}
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
		if (isset($check['manufacture_id']) && !empty($check['manufacture_id'])) {
			$result = $this -> Manufacture -> find('count', array('id' => $check['manufacture_id']));
			return $result > 0;
		}

	}

	function validateLicenseId($check) {
		if (isset($check['license_id']) && !empty($check['license_id'])) {
			$result = $this -> Manufacture -> LicensesManufacture -> find('first', array('conditions' => array('LicensesManufacture.manufacture_id' => $this -> data['Collectible']['manufacture_id'], 'LicensesManufacture.license_id' => $check['license_id']), 'contain' => false));
			if ($result) {
				return true;

			} else {
				return false;
			}
		}
		return true;
	}

	/**
	 * Validate license method for print types, also going to be used for customs and originals
	 */
	public function validatePrintLicenseId($check) {
		debug($this -> data['Collectible']['manufacture_id']);
		// first make sure it is set
		if (isset($check['license_id']) && !empty($check['license_id'])) {
			// then check to see if we have a valid manufacturer set
			// if we do then do the standard check
			if (isset($this -> data['Collectible']['manufacture_id']) && !empty($this -> data['Collectible']['manufacture_id'])) {
				$result = $this -> Manufacture -> LicensesManufacture -> find('first', array('conditions' => array('LicensesManufacture.manufacture_id' => $this -> data['Collectible']['manufacture_id'], 'LicensesManufacture.license_id' => $check['license_id']), 'contain' => false));
				if ($result) {
					return true;

				} else {
					debug($this -> data['Collectible']['manufacture_id']);
					return false;
				}
			} else {
				// if they do not have a manufacturer and we know we are validating a print then we can
				// just check to see that what they entered is a valid brand
				$result = $this -> License -> find('first', array('contain' => false, 'conditions' => array('License.id' => $check['license_id'])));
				if ($result) {
					return true;

				} else {
					debug($this -> data['Collectible']['manufacture_id']);
					return false;
				}
			}
		}

		return true;
	}

	function validateCollectibleType($check) {
		// if we don't have a valid manufacturer yet, then we can't validate
		// this so return true for now
		if (is_null($this -> data['Collectible']['manufacture_id'])) {
			return true;
		}

		$result = $this -> Manufacture -> CollectibletypesManufacture -> find('first', array('conditions' => array('CollectibletypesManufacture.manufacture_id' => $this -> data['Collectible']['manufacture_id'], 'CollectibletypesManufacture.collectibletype_id' => $check['collectibletype_id']), 'contain' => false));
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

	/**
	 * This will get all pending collectibles
	 */
	public function getPendingCollectibles($options = array()) {
		if (isset($options['conditions'])) {
			$options = array_merge($options['conditions'], array('Collectible.status_id' => 2));
		} else {
			$options['conditions'] = array('Collectible.status_id' => 2);
		}

		if (!isset($options['contain'])) {
			$options['contain'] = array('Status', 'CollectiblesUpload' => array('Upload'));
		}

		$collectible = $this -> find("all", $options);

		return $collectible;
	}

	public function getNumberOfPendingCollectibles() {
		$count = $this -> find("count", array('conditions' => array('Collectible.status_id' => 2)));

		return $count;
	}

	public function getPendingCollectiblesByUserId($userId) {
		$count = $this -> find("count", array('conditions' => array('Collectible.user_id' => $userId, 'Collectible.status_id' => 2)));
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
		$collectibles = $this -> find('all', array('contain' => array('CollectiblesUpload' => array('Upload')), 'conditions' => array('Collectible.variant_collectible_id' => $collectibleId, 'Collectible.status_id' => 4)));

		return $collectibles;
	}

	/**
	 * This method will return a count of all collectibles that have been approved
	 */
	public function getCollectibleCount() {
		$collectiblesCount = $this -> find('count', array('conditions' => array('Collectible.status_id' => 4)));
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

			// Using regexp searches for dup list to better handle words out of order
			$names = explode(' ', $collectible['Collectible']['name']);
			$regSearch = array();
			foreach ($names as $key => $value) {
				// in case any weird characters get in there that this will trim
				$name = trim($value);
				array_push($regSearch, array('Collectible.name REGEXP' => '[[:<:]]' . $name . '[[:>:]]'));
			}

			// we need to add the name regex search to this array so that these will be bundled in an AND condition together
			array_push($orConditions, array('Collectible.manufacture_id' => $collectible['Collectible']['manufacture_id'], 'Collectible.license_id' => $collectible['Collectible']['license_id'], 'Collectible.collectibletype_id' => $collectible['Collectible']['collectibletype_id'], $regSearch));

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

			// Since collectibles are added from the beginning, make sure to exclude this one
			array_push($conditions, array('NOT' => array('Collectible.id' => array($collectible['Collectible']['id']))));

			$returnList = $this -> find("all", array("conditions" => array($conditions), "contain" => array('SpecializedType', 'Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'))));
		}

		return $returnList;

	}

	public function publishEdit($editId) {
		$retVal = false;
		//Grab out edit collectible
		$collectibleEditVersion = $this -> findEdit($editId);
		$collectible = array();

		$currentVersionCollectible = $this -> find("first", array('contain' => false, 'conditions' => array('Collectible.id' => $collectibleEditVersion['CollectibleEdit']['base_id'])));
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
				$subject = __('Your edit has been approved.');
				$this -> notifyUser($collectibleEditVersion['CollectibleEdit']['edit_user_id'], $message, $subject);
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
			$subject = __('Your edit has been denied.');
			$this -> notifyUser($collectibleEditVersion['CollectibleEdit']['edit_user_id'], $message, $subject);
		}

		return $retVal;
	}

	/**
	 * This method is used to copy a collectible and save it (mainly for variant creating purposes)
	 */
	public function createCopy($collectibleId, $userId, $variant = false) {
		$retVal = $this -> buildDefaultResponse();

		$collectible = $this -> find("first", array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => array('CollectiblesTag', 'AttributesCollectible')));
		if (!$collectible) {
			$retVal['response']['isSuccess'] = false;
			return;
		}
		$collectible['Collectible']['user_id'] = $userId;
		$collectible['Collectible']['status_id'] = 1;

		$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> DRAFT, null);
		$collectible['Revision'] = $revision['Revision'];
		$collectible['EntityType']['type'] = 'collectible';

		if ($variant) {
			$collectible['Collectible']['variant'] = true;
			$collectible['Collectible']['variant_collectible_id'] = $collectible['Collectible']['id'];
		}

		unset($collectible['Collectible']['id']);
		unset($collectible['Collectible']['revision_id']);
		unset($collectible['Collectible']['entity_type_id']);
		unset($collectible['Collectible']['created']);
		unset($collectible['Collectible']['modified']);
		unset($collectible['Collectible']['collectibles_user_count']);

		// Then we need to loop through each
		foreach ($collectible['AttributesCollectible'] as $key => $attributesCollectible) {
			unset($collectible['AttributesCollectible'][$key]['revision_id']);
			unset($collectible['AttributesCollectible'][$key]['collectible_id']);
			unset($collectible['AttributesCollectible'][$key]['id']);

		}
		foreach ($collectible['CollectiblesTag'] as $key => $collectiblesTag) {
			unset($collectible['CollectiblesTag'][$key]['revision_id']);
			unset($collectible['CollectiblesTag'][$key]['collectible_id']);
			unset($collectible['CollectiblesTag'][$key]['id']);
		}

		debug($collectible);
		$this -> set($collectible);

		// no need to check if it validates because it is all internal
		if ($this -> saveAll($collectible, array('validate' => false, 'deep' => true))) {
			$retVal['response']['isSuccess'] = true;
			$retVal['response']['data']['collectible_id'] = $this -> id;
		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Attribute');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	/**
	 * this method creates the initial collectible, used when adding
	 */
	public function createInitial($collectibleTypeId, $original, $custom, $userId) {
		$retVal = $this -> buildDefaultResponse();

		$collectible['Collectible'] = array();
		$collectible['Collectible']['user_id'] = $userId;
		$collectible['Collectible']['collectibletype_id'] = $collectibleTypeId;
		$collectible['Collectible']['status_id'] = 1;
		$revision = $this -> Revision -> buildRevision($userId, $this -> Revision -> DRAFT, null);
		$collectible['Revision'] = $revision['Revision'];
		$collectible['EntityType']['type'] = 'collectible';

		// If it is a custom, indicate that
		// also set the edition size to 1
		// At this point we should also add the collectible user entry as well
		if ($custom === true || $custom === 'true') {
			$collectible['Collectible']['custom'] = $custom;
			$collectible['Collectible']['edition_size'] = 1;
			$collectible['Collectible']['limited'] = true;

			//TODO: We should update this to not create the CollectibleUser until
			// they change the status to Active
			//Get a stubbed collectibles user object
			// $defaultCollectiblesUser = $this -> CollectiblesUser -> createDefault($userId);
			// $collectible['CollectiblesUser'][0] = $defaultCollectiblesUser['CollectiblesUser'];
			// create the initial custom table as well
			$collectible['Collectible']['custom_status_id'] = 1;
		} else if ($original === true || $original === 'true') {
			// by default make the official false, however it could
			// be true
			$collectible['Collectible']['official'] = false;
			$collectible['Collectible']['original'] = true;
			// if it is an original piece, auto set the edition size to 1
			// do I need a flag for this as well?
			$collectible['Collectible']['edition_size'] = 1;
			$collectible['Collectible']['limited'] = true;

			//TODO: We should update this to not create the CollectibleUser until
			// they change the status to Active
			//Get a stubbed collectibles user object
			// $defaultCollectiblesUser = $this -> CollectiblesUser -> createDefault($userId);
			// $collectible['CollectiblesUser'][0] = $defaultCollectiblesUser['CollectiblesUser'];
		}
		$this -> set($collectible);
		// Only field we need to validate
		if ($this -> User -> validates(array('fieldList' => array('collectibletype_id')))) {
			// valid
			if ($this -> saveAssociated($collectible, array('validate' => false, 'deep' => true))) {
				$retVal['response']['isSuccess'] = true;
				$retVal['response']['data']['id'] = $this -> id;
			} else {
				$retVal['response']['isSuccess'] = false;
				$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Attribute');
				$retVal['response']['errors'] = $errors;
			}
		} else {
			// invalid
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Attribute');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

	public function getCollectible($id) {
		$retVal = $this -> buildDefaultResponse();

		if (!$id) {
			$retVal['response']['isSuccess'] = false;
			$retVal['response']['errors'] = array('message', __('Invalid request.'));
		}
		// TODO: We really need to start caching collectibles I think...we are fetching A LOT of data
		// 12/17/12 - Welp I was right, this is WAY too many joins for my little server to handle
		//$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Currency', 'SpecializedType', 'Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Revision' => array('User'), 'Attribute' => array('AttributeCategory', 'Manufacture', 'Scale', 'AttributesCollectible' => array('Collectible' => array('fields' => array('id', 'name')))), 'conditions' => array('AttributesCollectible.active' => 1)))));
		$collectible = $this -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('CollectiblePriceFact', 'Listing' => array('User', 'Transaction'), 'CustomStatus', 'Status', 'Currency', 'SpecializedType', 'Manufacture', 'User' => array('fields' => array('User.username', 'User.admin')), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag'), 'ArtistsCollectible' => array('Artist'), 'AttributesCollectible' => array('Revision' => array('User'), 'Attribute' => array('User', 'AttributeCategory', 'Manufacture', 'Artist', 'Scale', 'AttributesUpload' => array('Upload')), 'conditions' => array('AttributesCollectible.active' => 1)))));

		// so let's do this manually and try that out
		if (!empty($collectible['AttributesCollectible'])) {
			// ok if we have some of these
			// loop through each one
			foreach ($collectible['AttributesCollectible'] as $key => $attributesCollectible) {
				//'AttributesCollectible' => array('Collectible' )
				if (!empty($attributesCollectible['Attribute'])) {
					$existingAttributeCollectibles = $this -> AttributesCollectible -> find('all', array('joins' => array( array('alias' => 'Collectible2', 'table' => 'collectibles', 'type' => 'inner', 'conditions' => array('Collectible2.id = AttributesCollectible.collectible_id', 'Collectible2.status_id = "4"'))), 'conditions' => array('AttributesCollectible.attribute_id' => $attributesCollectible['Attribute']['id']), 'contain' => array('Collectible' => array('fields' => array('id', 'name')))));
					$collectible['AttributesCollectible'][$key]['Attribute']['AttributesCollectible'] = $existingAttributeCollectibles;
				}
			}
		}

		if (!empty($collectible)) {
			$variants = $this -> getCollectibleVariants($id);
			$retVal['response']['data']['collectible'] = $collectible;
			$retVal['response']['data']['variants'] = $variants;
			if (isset($retVal['response']['data']['collectible']['Collectible']['description'])) {
				// why the fuck do I need to do this?
				$description = str_replace('\n', "\n", $retVal['response']['data']['collectible']['Collectible']['description']);
				$description = str_replace('\r', "\r", $description);

				$retVal['response']['data']['collectible']['Collectible']['description'] = $description;
			}

		} else {
			$retVal['response']['isSuccess'] = false;
			// Missing
			$retVal['response']['code'] = 1;
		}

		return $retVal;
	}

	/**
	 * This is the method that now gets called anytime we need to save changes
	 * to a collectible (core data).  It will figure out the status
	 * of the collectible and whether it should submit an edit or
	 */
	public function saveCollectible($collectible, $user, $autoUpdate = false) {

		$retVal = $this -> buildDefaultResponse();
		// Given id, look up status
		// if it is anything but active allow real time update
		// if it is draft,  the only person who can update it is an admin
		// or the user who submitted it

		// other make it an edit
		$hasPermission = false;
		if ($autoUpdate === 'false' || $autoUpdate === false) {
			$autoUpdate = $this -> allowAutoUpdate($collectible['Collectible']['id'], $user);
		}

		// make sure no hackers :)
		unset($collectible['Collectible']['user_id']);
		// Make sure they have the aibility to do anything
		if ($this -> isEditPermission($collectible['Collectible']['id'], $user)) {
			// TODO: Since they can update anything they want at any time
			// we will have to make all of the validation rules not required
			if ($autoUpdate === true || $autoUpdate === 'true') {
				$this -> id = $collectible['Collectible']['id'];
				// TODO: This will have to do a saveAssociated now, because
				// we will
				$this -> saveAssociated($collectible, array('validate' => false));
				$retVal['response']['isSuccess'] = true;
				$retVal['response']['data']['isEdit'] = false;

				// However, we only want to trigger this activity on collectibles that have been APPROVED already
				if ($this -> triggerActivity($collectible['Collectible']['id'], $user)) {
					$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$USER_EDIT, 'user' => $user, 'edit' => $collectible, 'editType' => 'Collectible')));
				}
			} else {
				$action = array();
				$action['Action']['action_type_id'] = 2;
				$returnData = $this -> saveEdit($collectible, $collectible['Collectible']['id'], $user['User']['id'], $action);
				if ($returnData) {
					$retVal['response']['isSuccess'] = true;
					$retVal['response']['data']['isEdit'] = true;
				}
			}
		} else {
			$retVal['response']['code'] = 401;
		}

		return $retVal;

	}

	/**
	 * This will handle removing the collectible.  If the collectible is
	 * a draft and it is the user who submitted it, then automatically
	 * delete it.
	 *
	 * If it is submitted, do not allow delete unless by an admin
	 *
	 * If it is active, do not allow delete unless by an admin
	 *
	 * Edit is not supported yet
	 */
	public function remove($collectibleId, $user) {
		$retVal = $this -> buildDefaultResponse();
		$collectible = $this -> find('first', array('contain' => false, 'conditions' => array('Collectible.id' => $collectibleId)));
		if (!empty($collectible)) {
			$allowDelete = false;
			// If it is a custom or an original, just make sure the user ids match
			if ($collectible['Collectible']['original'] || $collectible['Collectible']['custom']) {
				if ($collectible['Collectible']['user_id'] === $user['User']['id']) {
					$allowDelete = true;
				}
			} else if ($collectible['Collectible']['status_id'] === '1') {
				if ($collectible['Collectible']['user_id'] === $user['User']['id']) {
					$allowDelete = true;
				}
			} else if ($collectible['Collectible']['status_id'] === '2') {
				if ($user['User']['admin']) {
					$allowDelete = true;
				}
			} else if ($collectible['Collectible']['status_id'] === '4') {
				if ($user['User']['admin']) {
					$allowDelete = true;
				}
			} else {
				$retVal['response']['isSuccess'] = false;
				array_push($retVal['response']['errors'], array('message' => __('You do not have permission to do that.')));
			}

			if ($allowDelete) {
				if ($this -> delete($collectibleId, true)) {
					$retVal['response']['isSuccess'] = true;
				} else {
					$retVal['response']['isSuccess'] = false;
					array_push($retVal['response']['errors'], array('message' => __('Invalid request.')));
				}
			} else {
				$retVal['response']['isSuccess'] = false;
				array_push($retVal['response']['errors'], array('message' => __('You do not have permission to do that.')));
			}

		} else {
			$retVal['response']['isSuccess'] = false;
			array_push($retVal['response']['errors'], array('message' => __('Invalid request.')));

		}

		return $retVal;

	}

	/**
	 * This method will update the status of a collectible, mainly from the user's perspective
	 * If the change the status from draft to submitted then we will need to run a validation check
	 * against the collectible (tags and attributes will be automatically validated and we can have a collectible without a photo)
	 */
	public function updateStatus($collectibleId, $user, $ignoreDupCheck = false) {
		$retVal = $this -> buildDefaultResponse();
		$this -> read(null, $collectibleId);
		// if it is valid
		$status = $this -> data['Collectible']['status_id'];

		$triggerActivity = false;
		$addCollectibleUser = false;
		// custsom go from draft to active
		if ($this -> data['Collectible']['custom'] || $this -> data['Collectible']['original']) {
			// if the status is 1 change to 2 for a submit
			if ($status === '1') {
				$status = 4;
				$addCollectibleUser = true;
			} else {
				// should never happen
			}
			// also always ignore dup check for customs
			$ignoreDupCheck = true;
		} else {
			// if the status is 1 change to 2 for a submit
			if ($status === '1') {
				$status = 2;
				$triggerActivity = true;
			} else if ($status === '2') {
				$status = 1;
			}

		}

		//if we are changing it to a 2 or 4, then we need to validate
		if ($status == 2 || $status == 4) {
			if (!$this -> validateCollectible()) {
				$retVal['response']['isSuccess'] = false;
				$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Collectible');
				$retVal['response']['errors'] = $errors;
				return $retVal;
			}

			// if it validates, then also do dup checking
			// Based on the status we are changing too
			// If we are submitted it, then we need to
			// do a check to see if a similar collectible
			// exists
			if (!$ignoreDupCheck) {
				$dupList = $this -> doesCollectibleExist($this -> data);

				if (!empty($dupList) && !$ignoreDupCheck) {
					$retVal['response']['isSuccess'] = false;
					$retVal['response']['data']['dupList'] = $dupList;
					return $retVal;
				}
			}

		}

		unset($this -> data);
		//change the status
		if ($this -> saveField('status_id', $status, false)) {
			$statusDetail = $this -> Status -> find('first', array('contain' => false, 'conditions' => array('Status.id' => $status)));
			$retVal['response']['isSuccess'] = true;
			$retVal['response']['data']['status'] = $statusDetail['Status'];

			// this will also handle triggering an activity event for adding
			if ($addCollectibleUser) {
				// TODO/FYI this adds an add to stash event, not an event type 11, so the points earned are a lot lower.
				$defaultCollectiblesUser = $this -> CollectiblesUser -> createDefault($user['User']['id'], $collectibleId);
				$this -> CollectiblesUser -> add($defaultCollectiblesUser, $user);
			} else if ($triggerActivity) {
				$collectible = $this -> find('first', array('contain' => array('User', 'Manufacture', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'ArtistsCollectible' => array('Artist')), 'conditions' => array('Collectible.id' => $collectibleId)));
				$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$USER_SUBMIT_NEW, 'user' => $user, 'object' => $collectible, 'type' => 'Collectible')));
			}
		}

		return $retVal;
	}

	/**
	 * Get the status of a collectible
	 */
	public function getStatus($collectibleId) {
		$collectible = $this -> find('first', array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => array('Status')));
		if ($collectible && !empty($collectible)) {
			return $collectible['Status'];
		} else {
			return null;
		}
	}

	public function isStatusDraft($collectibleId) {
		$retVal = false;
		$status = $this -> getStatus($collectibleId);
		if (!is_null($status)) {
			if ($status['id'] === '1') {
				$retVal = true;
			}
		}

		return $retVal;
	}

	/**
	 * This determines if they can update the collectible realtime
	 * or it has to go through the edit process
	 */
	public function allowAutoUpdate($collectibleId, $user) {
		$retVal = false;
		// If they are an admin then they can always update
		if ($user['User']['admin']) {
			$retVal = true;
			return $retVal;
		}

		$collectible = $this -> find('first', array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => array('Status', 'User')));
		// IF status is 1 (draft) regardless of type
		if ($collectible['Status']['id'] === '1') {
			// if the user performing the action is the owner of the collectible or it is an admin
			// auto update
			if ($collectible['Collectible']['user_id'] === $user['User']['id']) {
				$retVal = true;
			}
		} else {
			// now check type, if it custom or original then it can be updated at any point if permission is there
			if ($collectible['Collectible']['custom'] || $collectible['Collectible']['original']) {
				if ($collectible['Collectible']['user_id'] === $user['User']['id']) {
					$retVal = true;
				}
			}
		}

		return $retVal;
	}

	/**
	 * This is used to check if we can automatically add (status 4) an attribute
	 * being added to a collectible
	 */
	public function allowAutoAddAttribute($collectibleId, $user) {
		$retVal = false;
		// If they are an admin then they can always update
		if ($user['User']['admin']) {
			$retVal = true;
			return $retVal;
		}

		$collectible = $this -> find('first', array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => array('Status', 'User')));

		// If the collectible is a custom or an original and the user who created the collectible
		// is the one adding the attribute, then allow it
		if ($collectible['Collectible']['custom'] || $collectible['Collectible']['original']) {
			if ($collectible['Collectible']['user_id'] === $user['User']['id']) {
				$retVal = true;
			}
		} else {
			// if it is mass-produced and they are adding
			// it will always be status 2.  This is because
			// when the mass-produced collectible is approved
			// it will change them to status 4
		}

		return $retVal;
	}

	/**
	 * This method will determine if the user has permissions to
	 * update.
	 *
	 * TODO: WE might have to expand this eventually to say,
	 * 		 if the user does not have permsission, then an
	 * 		 edit it submitted and the ownwer of the collectible
	 * 		 approves the eidt
	 */
	public function isEditPermission($check, $user) {
		$retVal = false;

		// if they are an admin then they always get persmission
		if ($user['User']['admin']) {
			$retVal = true;
			return $retVal;
		}

		// setup to work for when we have the collectible object
		// already or just the id
		if (is_numeric($check) || is_string($check)) {
			$collectible = $this -> find('first', array('conditions' => array('Collectible.id' => $check), 'contain' => array('Status', 'User')));
			//lol
		} else {
			// assume object
			$collectible = $check;
		}

		// if it is a draft or submitted, just need to make sure the user ids match
		if ($collectible['Status']['id'] === '1' || $collectible['Status']['id'] === '2') {
			if ($collectible['Collectible']['user_id'] === $user['User']['id']) {
				$retVal = true;
			}
		} else {
			if ($collectible && !empty($collectible)) {
				// right now for originals if you have to be the one who submitted it
				if ($collectible['Collectible']['custom'] || $collectible['Collectible']['original']) {
					if ($collectible['Collectible']['user_id'] === $user['User']['id']) {
						$retVal = true;
					}
				} else {
					// otherwise if it is a mass produced collectible then just
					// return true cause anyone can edit it
					$retVal = true;
				}
			}
		}

		return $retVal;
	}

	/**
	 *  This method will determine if we should trigger an activity from anything being edited or updated on a collectible
	 */
	public function triggerActivity($check, $user) {
		$retVal = false;
		if (is_numeric($check) || is_string($check)) {
			$collectible = $this -> find('first', array('conditions' => array('Collectible.id' => $check), 'contain' => array('Status', 'User')));
			//lol
		} else {
			// assume object
			$collectible = $check;
		}

		/**
		 * Only trigger activity when status is 4, everything else is assumed to be working on while building
		 * the collectible
		 */
		if ($collectible['Status']['id'] === '4') {
			$retVal = true;
		}

		return $retVal;
	}

	/**
	 *
	 */
	public function isStashable($check, $user) {

		$retVal = false;
		if (is_numeric($check) || is_string($check)) {
			$collectible = $this -> find('first', array('conditions' => array('Collectible.id' => $check), 'contain' => array('Status', 'User')));
			//lol
		} else {
			// assume object
			$collectible = $check;
		}

		// if it is a draft or submitted, just need to make sure the user ids match
		if ($collectible['Status']['id'] === '1' || $collectible['Status']['id'] === '2') {
			// right now you can't
		} else {
			// if it is an active status
			// right now for originals or customs you cannot add
			if ($collectible['Collectible']['custom'] || $collectible['Collectible']['original']) {
				// if ($collectible['Collectible']['user_id'] === $user['User']['id']) {
				// $retVal = true;
				// }
			} else {
				// otherwise if it is a mass produced collectible then just
				// return true cause anyone can edit it
				$retVal = true;
			}

		}

		return $retVal;
	}

	/**
	 *
	 */
	public function validateCollectible() {

		// for some reason I cannot get the validator() -> get field stuff to work so doing it manually
		// If it is a print, then they do not have to enter a manufacturer or a brand
		if ($this -> data['Collectible']['collectibletype_id'] === Configure::read('Settings.CollectibleTypes.Print')) {

			// They don't have to select a manufacturer
			$this -> validate['manufacture_id']['allowEmpty'] = true;
			// They don't have to select a brand
			//using unset here so it will go the validate method
			unset($this -> validate['license_id']['allowEmpty']);

			// If they do end up having a brand, we need to validate it differently
			$this -> validate['license_id']['rule'] = array('validatePrintLicenseId');
			// $this -> validator() -> getField('manufacture_id') -> getRule('rule') -> message = 'This field cannot be left blank';

			// However, if it is a print, then we need to make sure they have at least one artist added
			if (empty($this -> data['ArtistsCollectible'])) {
				$this -> validationErrors['arrtist'] = __('At least one artist is required.');
			}
		} else if ($this -> data['Collectible']['custom']) {
			// They don't have to select a manufacturer
			$this -> validate['manufacture_id']['allowEmpty'] = true;
			$this -> validate['msrp']['allowEmpty'] = true;
			$this -> validate['url']['allowEmpty'] = true;
			// They don't have to select a brand
			//using unset here so it will go the validate method
			unset($this -> validate['license_id']['allowEmpty']);

			// If they do end up having a brand, we need to validate it differently
			$this -> validate['license_id']['rule'] = array('validatePrintLicenseId');
		} else if ($this -> data['Collectible']['original']) {
			// They don't have to select a manufacturer
			$this -> validate['manufacture_id']['allowEmpty'] = true;
			// They don't have to select a brand
			//using unset here so it will go the validate method
			unset($this -> validate['license_id']['allowEmpty']);

			// If they do end up having a brand, we need to validate it differently
			$this -> validate['license_id']['rule'] = array('validatePrintLicenseId');
			// $this -> validator() -> getField('manufacture_id') -> getRule('rule') -> message = 'This field cannot be left blank';

			// However, if it is a print, then we need to make sure they have at least one artist added
			if (empty($this -> data['ArtistsCollectible'])) {
				$this -> validationErrors['arrtist'] = __('At least one artist is required.');
			}
		}

		$retVal = $this -> validates();

		return $retVal;
	}

}
?>
