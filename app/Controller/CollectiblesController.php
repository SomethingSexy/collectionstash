<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesController extends AppController {

	public $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload', 'CollectibleDetail', 'Minify', 'Wizard', 'Tree');

	public $components = array('Wizard');

	var $actsAs = array('Searchable.Searchable');

	function beforeFilter() {
		parent::beforeFilter();
		// $this -> Wizard -> steps = array('manufacture', array('variant' => 'variantFeatures'), 'attributes', 'tags', 'image', 'review');
		$this -> Wizard -> steps = array('manufacture', array('similar' => 'similarCollectibles'), 'attributes', 'tags', 'image', 'review');
		$this -> Wizard -> completeUrl = '/collectibles/confirm';
		$this -> Wizard -> loginRequired = true;
	}

	/**
	 * This is the entry point into adding a collectible.  It will determine first if this collectible is mass produced or not.
	 *
	 * It will handle all add resets as well.
	 */
	function addSelectType() {
		$this -> checkLogIn();
		if (!Configure::read('Settings.Collectible.Contribute.allowed')) {
			$this -> redirect('/', null, true);
		}
		$this -> resetCollectibleAdd();
		//check to see if there is data submitted
		if (!empty($this -> request -> data)) {

			if ($this -> request -> data['Collectible']['massProduced'] === 'true') {
				$this -> redirect(array('action' => 'massProduced'));
			} else if ($this -> request -> data['Collectible']['massProduced'] == 'false') {
				$this -> redirect(array('action' => 'nonMassProduced'));
			} else {
				$this -> Session -> setFlash(__('Please select Yes or No.', true), null, null, 'error');
			}
		}
	}

	/**
	 * This is the first entry point if this collectible is not mass produced.
	 */
	function nonMassProduced() {
		$this -> checkLogIn();
	}

	/**
	 * This is the first entry point if this collectible is mass produced.  It will then
	 * ask the user if they are going to add this collectible by manufacture or not.
	 */
	function massProduced() {
		$this -> checkLogIn();
		if (!empty($this -> request -> data)) {

			if ($this -> request -> data['Collectible']['manufactured'] === 'true') {
				$this -> redirect(array('action' => 'selectManufacturer'));
			} else if ($this -> request -> data['Collectible']['manufactured'] == 'false') {
				$this -> redirect(array('action' => 'nonManufactured'));
			} else {
				$this -> Session -> setFlash(__('Please select an option.', true), null, null, 'error');
			}
		}
	}

	/**
	 * This is the next step in the flow, if it is a mass produced collectible and they are going
	 * to add by manufacturer.  Once they select a valid manufacturer then they will go to the variant
	 * page.
	 */
	function selectManufacturer($id = null) {
		$this -> checkLogIn();
		if ($id && is_numeric($id)) {
			$validManufacture = $this -> Collectible -> Manufacture -> find('first', array('conditions' => array('Manufacture.id' => $id), 'contain' => false));
			if (!empty($validManufacture)) {
				$this -> Session -> write('add.collectible.manufacture', $validManufacture);
				$this -> redirect(array('action' => 'variant'));
			} else {
				$this -> Session -> setFlash(__('Please select a valid Manufacture', true), null, null, 'error');
			}
		}

		$this -> paginate = array('order' => array('Manufacture.title' => 'ASC'), 'contain' => array('CollectibletypesManufacture' => 'Collectibletype'));
		// $existingCollectibles = $this -> paginate('Collectible');
		$manufactures = $this -> paginate('Manufacture');

		$this -> set('manufactures', $manufactures);
	}

	/**
	 * As this point, it is a mass produced collectible and they are adding by manufacturer and it is a variant.
	 */
	function variant() {
		$this -> checkLogIn();
		//At this point always check to make sure that the manufacture has been selected
		if (!$this -> Session -> check('add.collectible.manufacture')) {
			//If it has not, start them over
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect(array('action' => 'addSelectType'));
		}

		if (!empty($this -> request -> data)) {
			if ($this -> request -> data['Collectible']['variant'] === 'true') {
				$this -> redirect(array('action' => 'variantSelectCollectible'));
			} else if ($this -> request -> data['Collectible']['variant'] == 'false') {
				$this -> redirect(array('action' => 'selectCollectibleType'));
			} else {
				$this -> Session -> setFlash(__('Please select Yes or No.', true), null, null, 'error');
			}
		}
	}

	function selectCollectibleType($id = null) {
		$this -> checkLogIn();
		//At this point always check to make sure that the manufacture has been selected
		//They also should not be here if they selected variant
		if (!$this -> Session -> check('add.collectible.manufacture') && $this -> Session -> check('add.collectible.variant')) {
			//If it has not, start them over
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect(array('action' => 'addSelectType'));
		}
		if ($id && is_numeric($id)) {
			$manufactureId = $this -> Session -> read('add.collectible.manufacture');

			$validCollectibleType = $this -> Collectible -> Manufacture -> CollectibletypesManufacture -> find('first', array('conditions' => array('Manufacture.id' => $manufactureId['Manufacture']['id'], 'Collectibletype.id' => $id)));
			if (!empty($validCollectibleType)) {
				$this -> Session -> write('add.collectible.collectibleType', $validCollectibleType);
				/*
				 * Now we can go to the wizard, turn variant off and go to the beginning
				 */
				// $this -> Wizard -> branch('variant', true);
				$this -> redirect(array('action' => 'wizard/manufacture'));
			} else {
				$this -> Session -> setFlash(__('Please select a valid Collectible Type', true), null, null, 'error');
			}
		}
		$manufactureId = $this -> Session -> read('add.collectible.manufacture');

		/*
		 * For now, get threaded which will return all collectible types and children.  If this grows
		 * we could do a find all, where parent_id = null, paginate on that then once they select
		 * the main type, we could go into the sub types.
		 */
		$collectibleTypes = $this -> Collectible -> Collectibletype -> find('threaded', array('contain' => false));

		//Now grab all of the manufacture collectible types so we can filter
		$manufacturerCollectibletypes = $this -> Collectible -> Manufacture -> CollectibletypesManufacture -> find('all', array('conditions' => array('CollectibletypesManufacture.manufacture_id' => $manufactureId['Manufacture']['id']), 'contain' => false));
		$manColTypeList = array();
		//Create a list of just ids, I am sure there is a way to do this specifically with cake.
		//Also right now this is assuming that if you have a subtype added you also have the main type added
		foreach ($manufacturerCollectibletypes as $key => $value) {
			array_push($manColTypeList, $value['CollectibletypesManufacture']['collectibletype_id']);
		}

		$this -> set('manufacturerCollectibletypes', $manColTypeList);
		$this -> set(compact('collectibleTypes'));
		$this -> set('manufacturer', $manufactureId['Manufacture']['title']);
	}

	function variantSelectCollectible($id = null) {
		$this -> checkLogIn();

		if (!$this -> Session -> check('add.collectible.manufacture')) {
			//If it has not, start them over
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect(array('action' => 'addSelectType'));
		}
		if ($id && is_numeric($id)) {
			$variantCollectible = $this -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $id), 'contain' => array('Manufacture', 'SpecializedType', 'Collectibletype', 'CollectiblesTag' => array('Tag'), 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'AttributesCollectible' => array('Attribute' => array('AttributeCategory', 'Manufacture', 'Scale', 'Revision'), 'conditions' => array('AttributesCollectible.active' => 1)))));

			if (!empty($variantCollectible)) {
				$manufacturer = $this -> Session -> read('add.collectible.manufacture');
				//make sure that the collectible they selected is the same as the manufacture they selected...hackerz
				if ($variantCollectible['Collectible']['manufacture_id'] === $manufacturer['Manufacture']['id']) {
					$this -> Session -> write('add.collectible.variant', $variantCollectible);
					$this -> Session -> write('add.collectible.collectibleType', array('Collectibletype' => $variantCollectible['Collectibletype']));
					// $this -> Wizard -> branch('variant');
					$this -> redirect(array('action' => 'wizard/manufacture'));
				} else {
					$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
					$this -> redirect(array('action' => 'addSelectType'));
				}
			}
		}

		$this -> request -> data = Sanitize::clean($this -> request -> data, array('encode' => false));
		$manufactureId = $this -> Session -> read('add.collectible.manufacture');
		debug($manufactureId);
		$this -> request -> query['m'] = $manufactureId['Manufacture']['id'];
		//Allow variants of variants
		$this -> searchCollectible();
	}

	function nonManufactured() {
		$this -> checkLogIn();
	}

	/**
	 * This method will allow us to quick add a collectible from a selected collectible.
	 * This method will base the new collectible off the manufacture and type that this collectible is.
	 *
	 * We will probably want to check against the type of collectible first in the future to know what
	 * we are doing first.
	 *
	 * Use Cases:
	 * 	- Add a similar collectible (collectible Id = xx, variant = false)
	 *  - Add a variant collectible (collectible Id = xx, variant = true)
	 *  - Add a similar collectible that is a variant of a base collectible (collectible Id = xx, variant = false)
	 * 		- For this case, we will determine here IF the collectible we are copying from IS a variant, then we will use its base collectible as the base collectible for the new collectible
	 *
	 * $collectibleId - this is the id that we are "Copying"
	 * $variant - this is a variant add?
	 */
	function quickAdd($collectibleId = null, $variant = false) {
		$this -> checkLogIn();
		if (!is_null($collectibleId) && is_numeric($collectibleId)) {
			//reset anything so we are fresh
			$this -> resetCollectibleAdd();
			//Grab my collectible
			$collectible = $this -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => array('Currency', 'Manufacture', 'SpecializedType', 'Collectibletype', 'CollectiblesTag' => array('Tag'), 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'AttributesCollectible' => array('Attribute', 'conditions' => array('AttributesCollectible.active' => 1)))));
			//make sure this is a valid collectible

			if (!empty($collectible)) {

				//$this -> Session -> write('add.collectible.variant', $variantCollectible);
				$manufacturer = array();
				$manufacturer['Manufacture'] = $collectible['Manufacture'];
				$this -> Session -> write('add.collectible.manufacture', $manufacturer);

				$collectibleType = array();
				$collectibleType['Collectibletype'] = $collectible['Collectibletype'];
				$this -> Session -> write('add.collectible.collectibleType', $collectibleType);

				if ($variant === 'true') {
					//Variant add
					$this -> Session -> write('add.collectible.variant', $collectible);
				} else {
					if ($collectible['Collectible']['variant']) {
						//If the collectible we are copying is a variant itself, then grab its parent
						//and set that as a parent and then this will be a variant of that collectible
						$variantId = $collectible['Collectible']['variant_collectible_id'];
						$collectible = $this -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $variantId), 'contain' => array('Currency', 'Manufacture', 'SpecializedType', 'Collectibletype', 'CollectiblesTag' => array('Tag'), 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'AttributesCollectible' => array('Attribute' => array('Manufacture', 'Scale', 'AttributeCategory', 'Revision'), 'conditions' => array('AttributesCollectible.active' => 1)))));
						$this -> Session -> write('add.collectible.variant', $collectible);
					}
				}
				$this -> redirect(array('action' => 'wizard/manufacture'));
			} else {
				$this -> redirect($this -> referer());
			}
		} else {
			$this -> redirect($this -> referer());
		}
	}

	function confirm() {
		$id = $this -> Session -> read('addCollectibleId');
		if (isset($id) && $id != null) {
			$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Currency', 'SpecializedType', 'Manufacture', 'Collectibletype', 'CollectiblesTag' => array('Tag'), 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'AttributesCollectible' => array('Attribute' => array('Manufacture', 'Scale', 'AttributeCategory', 'Revision'), 'conditions' => array('AttributesCollectible.active' => 1)))));
			$this -> set('collectible', $collectible);
			$this -> Session -> delete('addCollectibleId');
		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect(array('action' => 'addSelectType'));
		}
	}

	function wizard($step = null) {
		$this -> Wizard -> process($step, $this -> isLoggedIn());
	}

	function _prepareManufacture() {
		$this -> Session -> delete('collectible');

		//We need to make sure we have the two static pieces of data that cannot change right now.
		if (!$this -> Session -> check('add.collectible.manufacture') && !$this -> Session -> check('add.collectible.collectibleType')) {
			//If it has not, start them over
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect(array('action' => 'addSelectType'));
		}
		$manufacturer = $this -> Session -> read('add.collectible.manufacture');
		$collectibleType = $this -> Session -> read('add.collectible.collectibleType');

		$this -> set(compact('manufacturer'));
		$this -> set(compact('collectibleType'));

		//Below is the stuff that needs to happen for every request

		//Grab all licenses for this manufacturer.
		$licenses = $this -> Collectible -> License -> LicensesManufacture -> getLicensesByManufactureId($manufacturer['Manufacture']['id']);
		$this -> set('licenses', $licenses);

		//Grab all specicialized types
		$specializedTypes = $this -> Collectible -> SpecializedType -> CollectibletypesManufactureSpecializedType -> getSpecializedTypes($manufacturer['Manufacture']['id'], $collectibleType['Collectibletype']['id']);
		$this -> set('specializedTypes', $specializedTypes);

		//Grab all scales
		$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
		$this -> set(compact('scales'));

		//Grab all retailers.
		$retailers = $this -> Collectible -> Retailer -> find('all', array('contain' => false));
		$this -> set('retailers', $retailers);

		//Grab all currencies
		$currencies = $this -> Collectible -> Currency -> find("list", array('fields' => array('Currency.id', 'Currency.iso_code')));
		$this -> set('currencies', $currencies);

		//Check to see if this is a post, if it is not a post then do some initial stuff

		//Check to see if this is a variant we are adding
		if ($this -> Session -> check('add.collectible.variant')) {
			$wizardData = $this -> Wizard -> read();

			// Do some initial stuff if what we are adding is a variant and the wizard
			// data has not been set yet.
			// TODO: As of 7/22/12 There is still a defect if you come in for
			// the first time, submit a change that has an error, it will overrwrite
			// what you entered because the wizard data has not been set yet
			if (is_null($wizardData) || !isset($wizardData['manufacture'])) {
				$variantCollectible = $this -> Session -> read('add.collectible.variant');

				if (isset($variantCollectible['Collectible']['retailer_id']) && !is_null($variantCollectible['Collectible']['retailer_id'])) {
					$variantCollectible['Collectible']['retailer'] = $variantCollectible['Retailer']['name'];
					unset($variantCollectible['Collectible']['retailer_id']);
				}
				//If it is then lets set the request attribute for default data and then also
				//prefill the input fields
				$this -> set('collectible', $variantCollectible);
				$this -> request -> data = $variantCollectible;
			}

		}
		$hasSeries = false;
		//First see if this manufacturer even has a series
		if (!empty($manufacturer['Manufacture']['series_id'])) {
			//If it does check to see if it has any children.
			$seriesCount = $this -> Collectible -> Series -> childCount($manufacturer['Manufacture']['series_id']);
			//If it does have a series set to true so the user will be forced to add it
			if (count($seriesCount) > 0) {
				$hasSeries = true;
			} else {
				//set the default behind the scenes
				$this -> request -> data('Collectible.series_id', $manufacturer['Manufacture']['series_id']);
			}
		}

		$this -> set('hasSeries', $hasSeries);

	}

	function _processManufacture() {
		//check if user is logged in
		if ($this -> isLoggedIn()) {

			$newCollectible = array();
			//First check to see if this is a post
			// if(!empty($this->request->data)) {
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			$manufacturer = $this -> Session -> read('add.collectible.manufacture');
			$collectibleType = $this -> Session -> read('add.collectible.collectibleType');
			$this -> request -> data['Collectible']['manufacture_id'] = $manufacturer['Manufacture']['id'];
			$this -> request -> data['Collectible']['collectibletype_id'] = $collectibleType['Collectibletype']['id'];
			if ($this -> Session -> check('add.collectible.variant')) {
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				$this -> request -> data['Collectible']['variant'] = 1;
				$this -> request -> data['Collectible']['variant_collectible_id'] = $variantCollectible['Collectible']['id'];
				$this -> set('collectible', $variantCollectible);
			}
			//Since this is a post, take the data that was submitted and set it to our variable
			$newCollectible = $this -> request -> data;
			//set default to true
			$validCollectible = true;
			//First try and validate the collectible.
			$this -> Collectible -> set($newCollectible);
			if ($this -> Collectible -> validates()) {
				/*
				 * OK at this point we need to test to see if this collectible has been added already
				 * We could reuse the collectible search logic but I think this should be a model call
				 * so that that business logic stays in the model
				 *
				 * Check
				 * 		if (UPC) OR (Manufacturer AND Product Code) OR (Manufacturer AND License AND CollectibleType AND LIKE Name)
				 *
				 * 		Notes: For the last one, I will not check a series or a manufacturer specialized type, incase they left it off
				 * 		Should only be required fields for that one.
				 *
				 *
				 */
				$similarCollectibles = $this -> Collectible -> doesCollectibleExist($newCollectible);
				debug($similarCollectibles);
				if (!empty($similarCollectibles)) {
					$this -> Session -> write('add.collectible.similar', $similarCollectibles);
					$this -> Wizard -> branch('similar');
				} else {
					$this -> Session -> delete('add.collectible.similar');
					$this -> Wizard -> branch('similar', true);
				}
			} else {
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				$validCollectible = false;
			}

			if ($validCollectible) {
				$userId = $this -> getUserId();
				return true;
			} else {
				return false;
			}
		} else {
			$this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
		}
		return false;
	}

	function _prepareSimilarCollectibles() {
		$similarCollectibles = $this -> Session -> read('add.collectible.similar');
		$this -> set(compact('similarCollectibles'));
	}

	function _processSimilarCollectibles() {
		return true;
	}

	function _prepareAttributes() {

		$attributeCategories = $this -> Collectible -> AttributesCollectible -> Attribute -> AttributeCategory -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
		$this -> set(compact('attributeCategories'));

		$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
		$this -> set(compact('scales'));

		$manufactures = $this -> Collectible -> Manufacture -> getManufactureList();
		$this -> set(compact('manufactures'));

		if (empty($this -> request -> data)) {

			// If this is a variant then we need to take the existing
			// attributes and link the attribute id itsefl to them, not just
			// the name anymore
			if ($this -> Session -> check('add.collectible.variant')) {
				//TODO store the collectible in the session for going back
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				$this -> set('collectible', $variantCollectible);
				if (isset($variantCollectible['AttributesCollectible'])) {
					foreach ($variantCollectible['AttributesCollectible'] as $attributeKey => $attributesCollectible) {
						if (isset($attributesCollectible['attribute_id']) && !empty($attributesCollectible['attribute_id'])) {
							$attribute = $this -> Collectible -> AttributesCollectible -> Attribute -> find("first", array('contain' => array('Manufacture', 'Scale', 'AttributeCategory'), 'conditions' => array('Attribute.id' => $attributesCollectible['attribute_id'])));
							$variantCollectible['AttributesCollectible'][$attributeKey]['Attribute'] = $attribute['Attribute'];
							$variantCollectible['AttributesCollectible'][$attributeKey]['Attribute']['Manufacture'] = $attribute['Manufacture'];
							$variantCollectible['AttributesCollectible'][$attributeKey]['Attribute']['Scale'] = $attribute['Scale'];
							$variantCollectible['AttributesCollectible'][$attributeKey]['Attribute']['AttributeCategory'] = $attribute['AttributeCategory'];
						}
					}
				}
				$this -> request -> data['AttributesCollectible'] = $variantCollectible['AttributesCollectible'];
			}
		} else {
			// If it is set, that means we already went through this
			// we need to do some work to make sure that our stuff is set
			if ($this -> request -> data['AttributesCollectible']) {
				foreach ($this -> request -> data['AttributesCollectible'] as $attributeKey => $attributesCollectible) {
					if (isset($attributesCollectible['attribute_id']) && !empty($attributesCollectible['attribute_id'])) {
						$attribute = $this -> Collectible -> AttributesCollectible -> Attribute -> find("first", array('contain' => array('Manufacture', 'Scale', 'AttributeCategory'), 'conditions' => array('Attribute.id' => $attributesCollectible['attribute_id'])));
						$this -> request -> data['AttributesCollectible'][$attributeKey]['Attribute'] = $attribute['Attribute'];
						$this -> request -> data['AttributesCollectible'][$attributeKey]['Attribute']['Manufacture'] = $attribute['Manufacture'];
						$this -> request -> data['AttributesCollectible'][$attributeKey]['Attribute']['Scale'] = $attribute['Scale'];
						$this -> request -> data['AttributesCollectible'][$attributeKey]['Attribute']['AttributeCategory'] = $attribute['AttributeCategory'];
					} else {
						if (isset($attributesCollectible['Attribute']['manufacture_id'])) {
							foreach ($manufactures as $key => $value) {
								if ($key == $attributesCollectible['Attribute']['manufacture_id']) {
									$this -> request -> data['AttributesCollectible'][$attributeKey]['Attribute']['Manufacture'] = array();
									$this -> request -> data['AttributesCollectible'][$attributeKey]['Attribute']['Manufacture']['title'] = $value;
								}
							}
						}
						//scale_id
						if (isset($attributesCollectible['Attribute']['scale_id'])) {
							foreach ($scales as $key => $value) {
								if ($key == $attributesCollectible['Attribute']['scale_id']) {
									$this -> request -> data['AttributesCollectible'][$attributeKey]['Attribute']['Scale'] = array();
									$this -> request -> data['AttributesCollectible'][$attributeKey]['Attribute']['Scale']['scale'] = $value;
								}
							}
						}

						//attribute_category_id
						if (isset($attributesCollectible['Attribute']['attribute_category_id'])) {
							foreach ($attributeCategories as $key => $value) {
								if ($value['AttributeCategory']['id'] === $attributesCollectible['Attribute']['attribute_category_id']) {
									$this -> request -> data['AttributesCollectible'][$attributeKey]['Attribute']['AttributeCategory'] = $value['AttributeCategory'];
								}
							}
						}
					}
				}
			}
		}
		debug($this -> request -> data);

		if ($this -> Session -> check('add.collectible.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant');

			$this -> set('collectible', $variantCollectible);
		}

	}

	function _processAttributes() {
		//TODO should validate
		if (isset($this -> request -> data['skip']) && $this -> request -> data['skip'] === 'true') {
			return true;
		} else {
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			debug($this -> request -> data);
			//Uh this doesn't make sense, do I need to loop through each of these to validate?
			if (isset($this -> request -> data['AttributesCollectible'])) {
				foreach ($this -> request -> data['AttributesCollectible'] as $key => $attribue) {
					$this -> Collectible -> AttributesCollectible -> Attribute -> set($attribue['Attribute']);
					//if ($this -> Collectible -> AttributesCollectible -> Attribute -> validates()) {
					return true;
					//} else {
					//	debug($this -> Collectible -> AttributesCollectible -> Attribute -> validationErrors);
					//	$this -> set('errors', $this -> Collectible -> AttributesCollectible -> Attribute -> validationErrors);
					//	return false;
					//}
				}
			}

			return true;
		}
	}

	function _prepareTags() {
		$wizardData = $this -> Wizard -> read();

		if (isset($wizardData['tags']['CollectiblesTag'])) {
			$this -> request -> data['Tag'] = $wizardData['tags']['CollectiblesTag'];
		} else {
			if ($this -> Session -> check('add.collectible.variant')) {
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				$this -> request -> data['Tag'] = $variantCollectible['CollectiblesTag'];
			}
		}
		if ($this -> Session -> check('add.collectible.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant');

			$this -> set('collectible', $variantCollectible);
		}

	}

	function _processTags() {
		$this -> request -> data = Sanitize::clean($this -> request -> data);
		//TODO clean up the validation
		if (count($this -> request -> data['CollectiblesTag']) <= 5) {
			$this -> loadModel('Tag');
			$processedTags = $this -> Tag -> processAddTags($this -> request -> data['CollectiblesTag']);
			$this -> request -> data['CollectiblesTag'] = $processedTags;
			//If there are any errors returned from the processing, set them here and return false
			//so the user knows they fucked something
			if (!empty($this -> Tag -> validationErrors)) {
				debug($this -> Tag -> validationErrors);
				$this -> set('errors', $this -> Tag -> validationErrors['tag']);
				return false;
			}
		} else {
			$this -> Session -> setFlash(__('Only 5 tags allowed.', true), null, null, 'error');
			return false;
		}

		return true;
	}

	function _prepareImage() {
		$wizardData = $this -> Wizard -> read();
		if ($this -> Session -> check('add.collectible.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant');

			$this -> set('collectible', $variantCollectible);
		}

		debug($this -> request -> data);
	}

	function _processImage() {
		if (!empty($this -> request -> data)) {

			if (isset($this -> request -> data['skip']) && $this -> request -> data['skip'] === 'true') {
				return true;
			} else if (isset($this -> request -> data['remove']) && $this -> request -> data['remove'] === 'true') {

				$wizardData = $this -> Wizard -> read();
				$uploadId = $this -> Session -> read('uploadId');
				if (isset($wizardData['image']['Upload']) && !empty($uploadId)) {
					$imageId = $uploadId;
					if ($this -> Collectible -> CollectiblesUpload -> Upload -> delete($imageId)) {
						$this -> Session -> delete('Wizard.Collectibles.image');
						return false;
					} else {

					}
				} else {

				}
			} else {
				//If they submit and we already added a collectible, think back button, then just redisplay the
				//page and show the image.  They can then choose to edit the image if they want
				if (!isset($wizardData['image']['Upload']) && empty($uploadId)) {
					if ($this -> Collectible -> CollectiblesUpload -> Upload -> isValidUpload($this -> request -> data)) {
						// We are adding this to the upload table first and then when we actually submit this will get added
						// to the collectibles upload table
						$response = $this -> Collectible -> CollectiblesUpload -> Upload -> add($this -> request -> data, $this -> getUserId(), true);
						if ($response['response']['isSuccess']) {
							//We have to save the upload right away because of how these work.  So lets save it
							//Then lets grab the id of the upload and return the data of the uploaded one and store
							//it in our saving object.  This will allow us to display the details to the user in the
							//review and confirm process.
							$this -> Session -> write('uploadId', $response['response']['data']['Upload']['id']);
							return true;
						} else {
							debug($this -> Collectible -> Upload -> validationErrors);
							unset($this -> request -> data['Upload']);
							$this -> Session -> setFlash(__('Oops! There was an issue uploading your photo.', true), null, null, 'error');
							return false;
						}
					} else {
						//TODO:
						$this -> Collectible -> CollectiblesUpload -> Upload -> validationErrors['0']['file'] = 'Image is required.';

						$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
						return false;
					}
				} else {

				}

			}
		} else {

		}

		return false;
	}

	function _saveImage() {
		$uploadId = $this -> Session -> read('uploadId');
		$upload = $this -> Collectible -> CollectiblesUpload -> Upload -> find('first', array('conditions' => array('Upload.id' => $uploadId), 'contain' => false));
		$this -> Wizard -> save('image', $upload);
	}

	function _prepareReview() {

		$wizardData = $this -> Wizard -> read();

		$collectible = array();
		$collectible['Collectible'] = $wizardData['manufacture']['Collectible'];

		if (isset($wizardData['tags']['CollectiblesTag'])) {
			$collectible['CollectiblesTag'] = $wizardData['tags']['CollectiblesTag'];
		}

		// Need to do a little magic because the review page is reusing stuff
		if (isset($wizardData['image']['Upload'])) {
			$collectible['CollectiblesUpload'][0]['Upload'] = $wizardData['image']['Upload'];
			$collectible['CollectiblesUpload'][0]['primary'] = true;
		}
		//fuck you cake
		if (isset($collectible['Collectible']['release']['year'])) {
			$collectible['Collectible']['release'] = $collectible['Collectible']['release']['year'];
		}

		//TODO: All of this code between this and the collectible_edit controller is redundant
		$manufacture = $this -> Collectible -> Manufacture -> find('first', array('conditions' => array('Manufacture.id' => $collectible['Collectible']['manufacture_id']), 'fields' => array('Manufacture.title', 'Manufacture.url', 'Manufacture.id'), 'contain' => false));
		$collectible['Manufacture'] = $manufacture['Manufacture'];

		$collectibleType = $this -> Collectible -> Collectibletype -> find('first', array('conditions' => array('Collectibletype.id' => $collectible['Collectible']['collectibletype_id']), 'fields' => array('Collectibletype.name'), 'contain' => false));
		$collectible['Collectibletype'] = $collectibleType['Collectibletype'];

		if (isset($collectible['Collectible']['specialized_type_id'])) {
			$specializedType = $this -> Collectible -> SpecializedType -> find('first', array('conditions' => array('SpecializedType.id' => $collectible['Collectible']['specialized_type_id']), 'fields' => array('SpecializedType.name'), 'contain' => false));
			$collectible['SpecializedType'] = $specializedType['SpecializedType'];
		}

		$license = $this -> Collectible -> License -> find('first', array('conditions' => array('License.id' => $collectible['Collectible']['license_id']), 'fields' => array('License.name'), 'contain' => false));
		$collectible['License'] = $license['License'];

		//This method will check and see if a series has been added and if so it will generate and add the path for us.
		$this -> Collectible -> addSeriesPath($collectible);

		$scale = $this -> Collectible -> Scale -> find('first', array('conditions' => array('Scale.id' => $collectible['Collectible']['scale_id']), 'fields' => array('Scale.scale'), 'contain' => false));
		$collectible['Scale'] = $scale['Scale'];

		$currency = $this -> Collectible -> Currency -> find('first', array('conditions' => array('Currency.id' => $collectible['Collectible']['currency_id']), 'contain' => false));
		$collectible['Currency'] = $currency['Currency'];

		if (isset($wizardData['attributes']['AttributesCollectible'])) {
			$attributeCategories = $this -> Collectible -> AttributesCollectible -> Attribute -> AttributeCategory -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
			$this -> set(compact('attributeCategories'));

			$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
			$this -> set(compact('scales'));

			$manufactures = $this -> Collectible -> Manufacture -> getManufactureList();
			$this -> set(compact('manufactures'));

			$collectible['AttributesCollectible'] = $wizardData['attributes']['AttributesCollectible'];

			foreach ($collectible['AttributesCollectible'] as $attributeKey => $attributesCollectible) {
				if (isset($attributesCollectible['attribute_id']) && !empty($attributesCollectible['attribute_id'])) {
					$attribute = $this -> Collectible -> AttributesCollectible -> Attribute -> find("first", array('contain' => array('Manufacture', 'Scale', 'AttributeCategory'), 'conditions' => array('Attribute.id' => $attributesCollectible['attribute_id'])));
					$collectible['AttributesCollectible'][$attributeKey]['Attribute'] = $attribute['Attribute'];
					$collectible['AttributesCollectible'][$attributeKey]['Attribute']['Manufacture'] = $attribute['Manufacture'];
					$collectible['AttributesCollectible'][$attributeKey]['Attribute']['Scale'] = $attribute['Scale'];
					$collectible['AttributesCollectible'][$attributeKey]['Attribute']['AttributeCategory'] = $attribute['AttributeCategory'];
				} else {
					if (isset($attributesCollectible['Attribute']['manufacture_id'])) {
						foreach ($manufactures as $key => $value) {
							if ($key == $attributesCollectible['Attribute']['manufacture_id']) {
								$collectible['AttributesCollectible'][$attributeKey]['Attribute']['Manufacture'] = array();
								$collectible['AttributesCollectible'][$attributeKey]['Attribute']['Manufacture']['title'] = $value;
							}
						}
					}
					//scale_id
					if (isset($attributesCollectible['Attribute']['scale_id'])) {
						foreach ($scales as $key => $value) {
							if ($key == $attributesCollectible['Attribute']['scale_id']) {
								$collectible['AttributesCollectible'][$attributeKey]['Attribute']['Scale'] = array();
								$collectible['AttributesCollectible'][$attributeKey]['Attribute']['Scale']['scale'] = $value;
							}
						}
					}

					//attribute_category_id
					if (isset($attributesCollectible['Attribute']['attribute_category_id'])) {
						foreach ($attributeCategories as $key => $value) {
							if ($value['AttributeCategory']['id'] === $attributesCollectible['Attribute']['attribute_category_id']) {
								$collectible['AttributesCollectible'][$attributeKey]['Attribute']['AttributeCategory'] = $value['AttributeCategory'];
							}
						}
					}
				}
			}

		}

		if ($this -> Session -> check('add.collectible.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant');
			// debug($variantCollectible);
			$this -> set('collectible', $variantCollectible);
			$collectible['Collectible']['variant'] = 1;
		}
		debug($collectible);
		$this -> set('collectibleReview', $collectible);

	}

	function _processReview() {
		return true;
	}

	function _afterComplete() {
		$wizardData = $this -> Wizard -> read();
		$collectible = array();
		$collectible['Collectible'] = $wizardData['manufacture']['Collectible'];
		if (isset($wizardData['attributes']['AttributesCollectible'])) {
			$collectible['AttributesCollectible'] = $wizardData['attributes']['AttributesCollectible'];
		}
		$collectible['CollectiblesTag'] = $wizardData['tags']['CollectiblesTag'];

		if (isset($wizardData['image']['Upload'])) {
			$collectible['CollectiblesUpload'][0]['upload_id'] = $wizardData['image']['Upload']['id'];
			$collectible['CollectiblesUpload'][0]['primary'] = true;
		}

		if ($this -> Session -> check('add.collectible.variant')) {
			$collectible['Collectible']['variant'] = 1;
		}

		$userId = $this -> getUserId();

		//If there are any newly created Tags, we need to remove them from the array or else cake won't add
		//In the future we might want to update this to that we active tags
		//if(Configure::read('Settings.Approval.auto-approve') == 'true') {
		foreach ($collectible['CollectiblesTag'] as &$tag) {
			//unset($tag);
			$tag['Tag'] = array();
		}
		//}

		if ($this -> Collectible -> add($collectible, $userId)) {
			$id = $this -> Collectible -> id;
			$this -> Session -> write('addCollectibleId', $id);
			$this -> Session -> delete('uploadId');
			$this -> Session -> delete('add.collectible.manufacture');
			$this -> Session -> delete('add.collectible.collectibleType');
			$this -> Session -> delete('add.collectible.variant');
			$this -> Session -> delete('add.collectible.similar');
			return true;
		} else {
			$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			return false;
		}
	}

	/**
	 * This is called when cancelling during the contribute process
	 */
	function cancel() {
		//If there is an upload, delete it
		$uploadId = $this -> Session -> read('uploadId');
		$upload = $this -> Collectible -> Upload -> findById($uploadId);
		$this -> Collectible -> Upload -> delete($uploadId);
		//reset and then redirect
		$this -> resetCollectibleAdd();
		$this -> redirect(array('action' => 'addSelectType'));
	}

	function view($id = null) {
		if (!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect(array('action' => 'index'));
		}
		// TODO: We really need to start caching collectibles I think...we are fetching A LOT of data
		// 12/17/12 - Welp I was right, this is WAY too many joins for my little server to handle
		//$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Currency', 'SpecializedType', 'Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Revision' => array('User'), 'Attribute' => array('AttributeCategory', 'Manufacture', 'Scale', 'AttributesCollectible' => array('Collectible' => array('fields' => array('id', 'name')))), 'conditions' => array('AttributesCollectible.active' => 1)))));
		$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Currency', 'SpecializedType', 'Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Revision' => array('User'), 'Attribute' => array('AttributeCategory', 'Manufacture', 'Scale'), 'conditions' => array('AttributesCollectible.active' => 1)))));

		// so let's do this manually and try that out
		if (!empty($collectible['AttributesCollectible'])) {
			// ok if we have some of these
			// loop through each one
			foreach ($collectible['AttributesCollectible'] as $key => $attributesCollectible) {
				//'AttributesCollectible' => array('Collectible' )
				if (!empty($attributesCollectible['Attribute'])) {
					$existingAttributeCollectibles = $this -> Collectible -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.attribute_id' => $attributesCollectible['Attribute']['id']), 'contain' => array('Collectible' => array('fields' => array('id', 'name')))));
					$collectible['AttributesCollectible'][$key]['Attribute']['AttributesCollectible'] = $existingAttributeCollectibles;
				}
			}
		}

		if (!empty($collectible) && $collectible['Collectible']['state'] === '0') {
			$this -> set('collectible', $collectible);
			$count = $this -> Collectible -> getNumberofCollectiblesInStash($id);
			$this -> set('collectibleCount', $count);

			$variants = $this -> Collectible -> getCollectibleVariants($id);
			$this -> set('variants', $variants);

			//TODO: this should be a helper or something to get all of the data necessary to render the add attribute window
			$attributeCategories = $this -> Collectible -> AttributesCollectible -> Attribute -> AttributeCategory -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
			$this -> set(compact('attributeCategories'));

			$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
			$this -> set(compact('scales'));

			$manufactures = $this -> Collectible -> Manufacture -> getManufactureList();
			$this -> set(compact('manufactures'));

		} else {
			$this -> render('viewMissing');
		}
	}

	function search() {
		/*
		 * Call the parent method now, that method handles pretty much everything now
		 */
		$this -> searchCollectible();
		// I can use this to pull the pagination data off the request and pass it to the view
		// although in the JSON view, I should be able to pull all of the data off the request
		// and build out the JSON object and send that down, with access to the pagination
		// information.  I can pass it as meta data that the client side script can then use
		// to know how to make the next set of requests
		debug($this -> request -> params['paging']['Collectible']);
		if ($this -> request -> isAjax()) {
			$this -> render('searchJson');
		} else {
			$this -> set('viewType', 'list');
			$this -> render('searchList');
		}

	}

	/**
	 * We need to two methods because the tile stuff using the infinite scroll
	 * which uses the standard HTML response to parse out the contents
	 */
	function searchTiles($type = 'list') {
		/*
		 * Call the parent method now, that method handles pretty much everything now
		 */
		$this -> searchCollectible();
		$this -> set('viewType', 'tiles');
		$this -> render('searchTiles');

	}

	function catalog() {
		//Updated to use modified desc, instead of created so I will get the latest ones added.
		$recentlyAddedCollectibles = $this -> Collectible -> find('all', array('limit' => 20, 'conditions' => array('Collectible.state' => '0'), 'contain' => array('CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype', 'License'), 'order' => array('Collectible.modified' => 'desc')));
		$this -> set(compact('recentlyAddedCollectibles'));

		$manufactures = $this -> Collectible -> Manufacture -> find('all', array('limit' => 10, 'contain' => false, 'order' => array('Manufacture.collectible_count' => 'desc')));
		$this -> set(compact('manufactures'));

		$licenses = $this -> Collectible -> License -> find('all', array('limit' => 10, 'contain' => false, 'order' => array('License.collectible_count' => 'desc')));
		$this -> set(compact('licenses'));

		$collectibletypes = $this -> Collectible -> Collectibletype -> find('all', array('limit' => 10, 'contain' => false, 'order' => array('Collectibletype.collectible_count' => 'desc')));
		$this -> set(compact('collectibletypes'));

	}

	function history($id = null) {
		$this -> checkLogIn();
		if ($id && is_numeric($id)) {
			$this -> Collectible -> id = $id;
			$history = $this -> Collectible -> revisions(null, true);
			$this -> loadModel('User');

			//TODO the revision behavior needs to get updated so that we can return associated data with it
			//Maybe the revision behavior should also interact with the Revision model
			//Making this by reference so we can modify it, is this proper in php?
			foreach ($history as $key => &$collectible) {
				$collectibleRevision = $this -> Collectible -> Revision -> findById($collectible['Collectible']['revision_id'], array('contain' => false));
				$collectible['Collectible']['action'] = $collectibleRevision['Revision']['action'];
				if ($collectibleRevision['Revision']['action'] !== 'A') {
					$editUserDetails = $this -> User -> findById($collectibleRevision['Revision']['user_id'], array('contain' => false));
					$collectible['Collectible']['user_name'] = $editUserDetails['User']['username'];
				} else {
					$userId = $collectible['Collectible']['user_id'];
					$userDetails = $this -> User -> findById($userId, array('contain' => false));
					$collectible['Collectible']['user_name'] = $userDetails['User']['username'];
				}
			}

			$this -> set(compact('history'));
			debug($history);
			//Grab a list of all attributes associated with this collectible, or were associated with this collectible.  We will display a list of all
			//of these attributes then we can go into further history detail if we need too
			$attributeHistory = $this -> Collectible -> AttributesCollectible -> find("all", array('conditions' => array('AttributesCollectible.collectible_id' => $id)));
			$this -> set(compact('attributeHistory'));
			//Update this in the future since we only allow one Upload for now
			$collectibleUpload = $this -> Collectible -> Upload -> find("first", array('contain' => false, 'conditions' => array('Upload.collectible_id' => $id)));
			$uploadHistory = array();
			if (!empty($collectibleUpload)) {
				$this -> Collectible -> Upload -> id = $collectibleUpload['Upload']['id'];
				$uploadHistory = $this -> Collectible -> Upload -> revisions(null, true);
				//This is like the worst thing ever and needs to get cleaned up
				//Making this by reference so we can modify it, is this proper in php?
				foreach ($uploadHistory as $key => &$upload) {
					$uploadRevision = $this -> Collectible -> Revision -> findById($upload['Upload']['revision_id'], array('contain' => false));

					$upload['Upload']['action'] = $uploadRevision['Revision']['action'];

					$editUserDetails = $this -> User -> findById($uploadRevision['Revision']['user_id'], array('contain' => false));
					$upload['Upload']['user_name'] = $editUserDetails['User']['username'];

				}
				//As of 9/7/11, because of the way we have to add an upload, the first revision is going to be bogus.
				//Pop it off here until we can update the revision behavior so that we can specific a save to not add a revision.
				$lastUpload = end($uploadHistory);
				if ($lastUpload['Upload']['revision_id'] === '0') {
					array_pop($uploadHistory);
				}
				reset($uploadHistory);

			}

			$this -> set(compact('uploadHistory'));
		} else {
			$this -> redirect($this -> referer());
		}
	}

	function historyDetail($id = null, $version_id = null) {
		$this -> checkLogIn();

		if ($id && $version_id && is_numeric($id) && is_numeric($version_id)) {
			$this -> Collectible -> id = $id;
			$collectible = $this -> Collectible -> revisions(array('conditions' => array('version_id' => $version_id)), true);

			$this -> set(compact('collectible'));

		} else {
			//$this -> redirect($this -> referer());
		}
	}

	/**
	 * This function right now will return the history of the collectibles the user has submitted.
	 */
	function userHistory() {
		//Make sure the user is logged in
		$this -> checkLogIn();
		//Grab the user id of the person logged in
		$userId = $this -> getUserId();
		$this -> paginate = array('conditions' => array('Collectible.user_id' => $userId), 'contain' => array('Collectibletype', 'Manufacture'), 'limit' => 25);
		$collectibles = $this -> paginate('Collectible');
		$this -> set(compact('collectibles'));
	}

	function admin_index() {
		$this -> checkLogIn();
		$this -> checkAdmin();

		$this -> paginate = array("conditions" => array('state' => 1), "contain" => array('Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')));

		$collectilbes = $this -> paginate('Collectible');

		$this -> set('collectibles', $collectilbes);

	}

	function admin_view($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();

		if (!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect(array('action' => 'index'));
		}
		$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Currency', 'SpecializedType', 'Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Revision' => array('User'), 'Attribute' => array('AttributeCategory', 'Manufacture', 'Scale', 'Revision'), 'conditions' => array('AttributesCollectible.active' => 1)))));
		$this -> set('collectible', $collectible);
		debug($collectible);

		//TODO: this should be a helper or something to get all of the data necessary to render the add attribute window
		$attributeCategories = $this -> Collectible -> AttributesCollectible -> Attribute -> AttributeCategory -> find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
		$this -> set(compact('attributeCategories'));

		$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
		$this -> set(compact('scales'));

		$manufactures = $this -> Collectible -> Manufacture -> getManufactureList();
		$this -> set(compact('manufactures'));

	}

	function admin_approve($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($id && is_numeric($id) && isset($this -> request -> data['Approval']['approve'])) {
			$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('User', 'Upload', 'AttributesCollectible')));
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			$notes = $this -> request -> data['Approval']['notes'];
			//Approve
			if ($this -> request -> data['Approval']['approve'] === 'true') {
				if (!empty($collectible) && $collectible['Collectible']['state'] === '1') {
					$data = array();
					$data['Collectible'] = array();
					$data['Collectible']['id'] = $collectible['Collectible']['id'];
					$data['Collectible']['state'] = 0;
					$data['Revision']['action'] = 'P';
					$data['Revision']['user_id'] = $this -> getUserId();
					$data['Revision']['notes'] = $this -> request -> data['Approval']['notes'];
					if ($this -> Collectible -> saveAll($data, array('validate' => false))) {
						//Ugh need to get this again so I can get the Revision id
						$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('User', 'CollectiblesUpload' => array('Upload'), 'AttributesCollectible' => array('Attribute'))));
						//update with the new revision id
						if (isset($collectible['CollectiblesUpload']) && !empty($collectible['CollectiblesUpload'])) {

							$this -> Collectible -> CollectiblesUpload -> id = $collectible['CollectiblesUpload'][0]['id'];
							if (!$this -> Collectible -> CollectiblesUpload -> saveField('revision_id', $collectible['Collectible']['revision_id'])) {
								//If it fails, let it pass but log the problem.
								$this -> log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
							}

							$this -> Collectible -> CollectiblesUpload -> Upload -> id = $collectible['CollectiblesUpload'][0]['Upload']['id'];
							if (!$this -> Collectible -> CollectiblesUpload -> Upload -> saveField('revision_id', $collectible['Collectible']['revision_id'])) {
								//If it fails, let it pass but log the problem.
								$this -> log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
							}
							$this -> Collectible -> CollectiblesUpload -> Upload -> id = $collectible['CollectiblesUpload'][0]['Upload']['id'];
							if (!$this -> Collectible -> CollectiblesUpload -> Upload -> saveField('status_id', 4)) {
								//If it fails, let it pass but log the problem.
								$this -> log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
							}
						}

						if (isset($collectible['AttributesCollectible']) && !empty($collectible['AttributesCollectible'])) {
							foreach ($collectible['AttributesCollectible'] as $key => $value) {
								$this -> Collectible -> AttributesCollectible -> id = $value['id'];
								if (!$this -> Collectible -> AttributesCollectible -> saveField('revision_id', $collectible['Collectible']['revision_id'])) {
									//If it fails, let it pass but log the problem.
									$this -> log('Failed to update the AttributesCollectible with the revision id (with approval) for collectible ' . $collectible['Collectible']['id'], 'error');
								}
								$this -> Collectible -> AttributesCollectible -> Attribute -> id = $value['Attribute']['id'];
								if (!$this -> Collectible -> AttributesCollectible -> Attribute -> saveField('status_id', 4)) {
									//If it fails, let it pass but log the problem.
									$this -> log('Failed to update the attribute with the status id of 4 (with approval) for collectible ' . $collectible['Collectible']['id'], 'error');
								}
								$this -> Collectible -> AttributesCollectible -> Attribute -> id = $value['Attribute']['id'];
								if (!$this -> Collectible -> AttributesCollectible -> Attribute -> saveField('revision_id', $collectible['Collectible']['revision_id'])) {
									//If it fails, let it pass but log the problem.
									$this -> log('Failed to update the attribute with the revision id (with approval) for collectible ' . $collectible['Collectible']['id'], 'error');
								}

							}

						}

						$this -> getEventManager() -> dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$ADMIN_APPROVE_NEW, 'user' => $this -> getUser(), 'object' => $collectible, 'target' => $collectible, 'type' => 'Collectible')));

						$this -> __sendApprovalEmail(true, $collectible['User']['email'], $collectible['User']['username'], $collectible['Collectible']['name'], $collectible['Collectible']['id']);

						$this -> Session -> setFlash(__('The collectible was successfully approved.', true), null, null, 'success');
						$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
					} else {
						$this -> Session -> setFlash(__('There was a problem approving the collectible.', true), null, null, 'error');
						$this -> redirect(array('admin' => true, 'action' => 'view', $id), null, true);
					}
				} else {
					$this -> Session -> setFlash(__('The collectible has been approved already.', true), null, null, 'error');
					$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
				}
			} else {
				//fuck it, I am deleting it
				if ($this -> Collectible -> delete($collectible['Collectible']['id'], true)) {

					//If this fails oh well
					//TODO: This should be in some callback
					//Have to do this because we have a belongsTo relationship on Collectible, probably should be a hasOne, fix at some point
					$this -> Collectible -> Revision -> delete($collectible['Collectible']['revision_id']);
					//Have to do the same thing with Entity
					$this -> Collectible -> EntityType -> delete($collectible['Collectible']['entity_type_id']);
					$this -> __sendApprovalEmail(false, $collectible['User']['email'], $collectible['User']['username'], $collectible['Collectible']['name'], null, $notes);
					$this -> Session -> setFlash(__('The collectible was successfully denied.', true), null, null, 'success');
					$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
				} else {
					$this -> Session -> setFlash(__('There was a problem denying the collectible.', true), null, null, 'error');
					$this -> redirect(array('admin' => true, 'action' => 'view', $id), null, true);
				}

			}

		} else {
			$this -> Session -> setFlash(__('Invalid collectible.', true), null, null, 'error');
			$this -> redirect(array('admin' => true, 'action' => 'index'), null, true);
		}

	}

	function __sendApprovalEmail($approvedChange = true, $email = null, $username = null, $collectibleName = null, $collectileId = null, $notes = '') {
		$return = true;
		if ($email) {
			// Set data for the "view" of the Email
			$this -> set(compact('collectibleName'));
			$this -> set(compact('username'));
			$this -> set(compact('notes'));

			$cakeEmail = new CakeEmail('smtp');
			$cakeEmail -> emailFormat('both');
			$cakeEmail -> to($email);
			if ($approvedChange) {
				$cakeEmail -> template('add_approval', 'simple');
				$cakeEmail -> subject('Your submission has been successfully approved!');
			} else {
				$cakeEmail -> template('add_deny', 'simple');
				$cakeEmail -> subject('Oh no! Your submission has been denied.');
			}
			$cakeEmail -> viewVars(array('collectible_url' => 'http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectileId, 'collectibleName' => $collectibleName, 'notes' => $notes, 'username' => $username));
			$cakeEmail -> send();
		} else {
			$return = false;
		}

		return $return;
	}

	private function resetCollectibleAdd() {
		$this -> Session -> delete('uploadId');
		$this -> Session -> delete('add.collectible.manufacture');
		$this -> Session -> delete('add.collectible.collectibleType');
		$this -> Session -> delete('add.collectible.variant');
		$this -> Session -> delete('add.collectible.similar');
		$this -> Session -> delete('addCollectibleId');
		$this -> Session -> delete('add.collectible.similar');

		$this -> Wizard -> resetWizard();
	}

}
?>

