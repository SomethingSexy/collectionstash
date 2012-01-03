<?php
App::uses('Sanitize', 'Utility');
class CollectiblesController extends AppController {

	public $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload', 'CollectibleDetail', 'Minify');

	var $components = array('Wizard', 'Email');

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
		if (!empty($this -> data)) {

			if ($this -> data['Collectible']['massProduced'] === 'true') {
				$this -> redirect(array('action' => 'massProduced'));
			} else if ($this -> data['Collectible']['massProduced'] == 'false') {
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
		if (!empty($this -> data)) {

			if ($this -> data['Collectible']['manufactured'] === 'true') {
				$this -> redirect(array('action' => 'selectManufacturer'));
			} else if ($this -> data['Collectible']['manufactured'] == 'false') {
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

		if (!empty($this -> data)) {
			if ($this -> data['Collectible']['variant'] === 'true') {
				$this -> redirect(array('action' => 'variantSelectCollectible'));
			} else if ($this -> data['Collectible']['variant'] == 'false') {
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
			$variantCollectible = $this -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $id), 'contain' => array('Manufacture', 'SpecializedType', 'Collectibletype', 'CollectiblesTag' => array('Tag'), 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'AttributesCollectible' => array('Attribute', 'conditions' => array('AttributesCollectible.active' => 1)))));

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

		$this -> data = Sanitize::clean($this -> data, array('encode' => false));
		$manufactureId = $this -> Session -> read('add.collectible.manufacture');
		debug($manufactureId);
		$this -> params['url']['m'] = $manufactureId['Manufacture']['id'];
		$this -> searchCollectible(array('Collectible.variant' => '0'));
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
			$collectible = $this -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => array('Manufacture', 'SpecializedType', 'Collectibletype', 'CollectiblesTag' => array('Tag'), 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'AttributesCollectible' => array('Attribute', 'conditions' => array('AttributesCollectible.active' => 1)))));
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
						$collectible = $this -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $variantId), 'contain' => array('Manufacture', 'SpecializedType', 'Collectibletype', 'CollectiblesTag' => array('Tag'), 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'AttributesCollectible' => array('Attribute', 'conditions' => array('AttributesCollectible.active' => 1)))));
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
			$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('SpecializedType', 'Manufacture', 'Collectibletype', 'CollectiblesTag' => array('Tag'), 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'AttributesCollectible' => array('Attribute', 'conditions' => array('AttributesCollectible.active' => 1)))));
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
		if (!$this -> Session -> check('add.collectible.manufacture') && !$this -> Session -> check('add.collectible.collectibleType')) {
			//If it has not, start them over
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect(array('action' => 'addSelectType'));
		}
		$manufacturer = $this -> Session -> read('add.collectible.manufacture');
		$collectibleType = $this -> Session -> read('add.collectible.collectibleType');
		$this -> set(compact('manufacturer'));
		$this -> set(compact('collectibleType'));

		$licenses = $this -> Collectible -> License -> LicensesManufacture -> getLicensesByManufactureId($manufacturer['Manufacture']['id']);
		debug($licenses);
		$this -> set('licenses', $licenses);

		//If data is empty then this is the first time we are coming here or a refresh or something.
		if (empty($this -> data)) {

			if ($this -> Session -> check('add.collectible.variant')) {
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				debug($variantCollectible);
				$this -> set('collectible', $variantCollectible);
				$this -> data = $variantCollectible;
			}
			$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
			$this -> set(compact('scales'));

			//Now get any series for this license
			/*
			 * We will get the list, to determine if there is something for this license.  We will not display the list initially,
			 * just the series name if they want to change it.  This way we know if a series exists for this collectible.
			 *
			 * TODO: At this point, we will also need to know how to draw this if we return
			 */
			reset($licenses);
			$licensesId = key($licenses);
			$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($manufacturer['Manufacture']['id'], $licensesId);
			debug($series);
			$hasSeries = false;
			if (count($series) > 0) {
				$hasSeries = true;
			} else {

			}
			$this -> set('hasSeries', $series);
		} else {
			//If data is not empty, then we submitted and it errored or we are coming back to edit
			if ($this -> Session -> check('add.collectible.variant')) {
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				$this -> set('collectible', $variantCollectible);
			}

			if (isset($this -> data['Collectible']['series_id']) && !empty($this -> data['Collectible']['series_id'])) {
				$seriesPathName = $this -> Collectible -> Series -> buildSeriesPathName($this -> data['Collectible']['series_id']);
				$this -> data['Collectible']['series_name'] = $seriesPathName;
				$this -> set('hasSeries', true);
			} else {
				$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($manufacturer['Manufacture']['id'], $this -> data['Collectible']['license_id']);
				debug($series);
				$hasSeries = false;
				if (count($series) > 0) {
					$hasSeries = true;
				} else {

				}
				$this -> set('hasSeries', $series);
			}
		}

		$specializedTypes = $this -> Collectible -> SpecializedType -> CollectibletypesManufactureSpecializedType -> getSpecializedTypes($manufacturer['Manufacture']['id'], $collectibleType['Collectibletype']['id']);
		$this -> set('specializedTypes', $specializedTypes);

		$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale'),'order'=> array('Scale.scale' => 'ASC')));

		$this -> set(compact('scales'));

		$retailers = $this -> Collectible -> Retailer -> getRetailerList();
		$this -> set('retailers', $retailers);

	}

	function _processManufacture() {
		//check if user is logged in
		if ($this -> isLoggedIn()) {

			$newCollectible = array();
			//First check to see if this is a post
			// if(!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data);
			$manufacturer = $this -> Session -> read('add.collectible.manufacture');
			$collectibleType = $this -> Session -> read('add.collectible.collectibleType');
			$this -> data['Collectible']['manufacture_id'] = $manufacturer['Manufacture']['id'];
			$this -> data['Collectible']['collectibletype_id'] = $collectibleType['Collectibletype']['id'];
			if ($this -> Session -> check('add.collectible.variant')) {
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				$this -> data['Collectible']['variant'] = 1;
				$this -> data['Collectible']['variant_collectible_id'] = $variantCollectible['Collectible']['id'];
				$this -> set('collectible', $variantCollectible);
			}
			//Since this is a post, take the data that was submitted and set it to our variable
			$newCollectible = $this -> data;
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
		if (empty($this -> data)) {
			if ($this -> Session -> check('add.collectible.variant')) {
				//TODO store the collectible in the session for going back
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				$this -> set('collectible', $variantCollectible);
				if (isset($variantCollectible['AttributesCollectible'])) {
					foreach ($variantCollectible['AttributesCollectible'] as $key => &$value) {
						$value['name'] = $value['Attribute']['name'];

					}
				}
				$this -> data['AttributesCollectible'] = $variantCollectible['AttributesCollectible'];
			}

		}
		if ($this -> Session -> check('add.collectible.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant');

			$this -> set('collectible', $variantCollectible);
		}

	}

	function _processAttributes() {
		//TODO should validate
		if (isset($this -> data['skip']) && $this -> data['skip'] === 'true') {
			return true;
		} else {
			$this -> data = Sanitize::clean($this -> data);
			$this -> loadModel('AttributesCollectible');

			//Uh this doesn't make sense, do I need to loop through each of these to validate?
			if (isset($this -> data['AttributesCollectible'])) {
				foreach ($this -> data['AttributesCollectible'] as $key => $attribue) {
					$this -> AttributesCollectible -> set($attribue);
					//debug($this -> AttributesCollectible);
					if ($this -> AttributesCollectible -> validates()) {
						return true;
					} else {

						$this -> set('errors', $this -> AttributesCollectible -> validationErrors);
						return false;
					}
				}
			}

			return true;
		}
	}

	function _prepareTags() {
		$wizardData = $this -> Wizard -> read();

		if (isset($wizardData['tags']['CollectiblesTag'])) {
			$this -> data['Tag'] = $wizardData['tags']['CollectiblesTag'];
		} else {
			if ($this -> Session -> check('add.collectible.variant')) {
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				$this -> data['Tag'] = $variantCollectible['CollectiblesTag'];
			}
		}
		if ($this -> Session -> check('add.collectible.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant');

			$this -> set('collectible', $variantCollectible);
		}

	}

	function _processTags() {
		$this -> data = Sanitize::clean($this -> data);
		//TODO clean up the validation
		if (count($this -> data['CollectiblesTag']) <= 5) {
			$this -> loadModel('Tag');
			$processedTags = $this -> Tag -> processAddTags($this -> data['CollectiblesTag']);
			$this -> data['CollectiblesTag'] = $processedTags;
			//If there are any errors returned from the processing, set them here and return false
			//so the user knows they fucked something
			if (!empty($this -> Tag -> validationErrors)) {
				$this -> set('errors', $this -> Tag -> validationErrors);
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
		//debug($this -> Session -> read('Wizard.Collectibles.image'));

		$collectible = array();
		// if(isset($wizardData['image']['Upload'])) {
		// $collectible['Upload'] = $wizardData['image']['Upload'];
		// $this -> set('collectible', $collectible);
		// }
	}

	function _processImage() {
		if (!empty($this -> data)) {

			if (isset($this -> data['skip']) && $this -> data['skip'] === 'true') {
				return true;
			} else if (isset($this -> data['remove']) && $this -> data['remove'] === 'true') {

				$wizardData = $this -> Wizard -> read();
				$uploadId = $this -> Session -> read('uploadId');
				if (isset($wizardData['image']['Upload']) && !empty($uploadId)) {
					$imageId = $uploadId;
					if ($this -> Collectible -> Upload -> delete($imageId)) {
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
					if ($this -> Collectible -> Upload -> isValidUpload($this -> data)) {
						if ($this -> Collectible -> Upload -> saveAll($this -> data['Upload'])) {
							//We have to save the upload right away because of how these work.  So lets save it
							//Then lets grab the id of the upload and return the data of the uploaded one and store
							//it in our saving object.  This will allow us to display the details to the user in the
							//review and confirm process.
							$uploadId = $this -> Collectible -> Upload -> id;
							$this -> Session -> write('uploadId', $uploadId);
							return true;
						} else {

							$this -> Session -> setFlash(__('Oops! There was an issue uploading your photo.', true), null, null, 'error');
							return false;
						}
					} else {
						$this -> Collectible -> Upload -> validationErrors['0']['file'] = 'Image is required.';

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
		$upload = $this -> Collectible -> Upload -> find('first', array('conditions' => array('Upload.id' => $uploadId), 'contain' => false));
		$this -> Wizard -> save('image', $upload);
	}

	function _prepareReview() {

		$wizardData = $this -> Wizard -> read();

		$collectible = array();
		$collectible['Collectible'] = $wizardData['manufacture']['Collectible'];
		if (isset($wizardData['attributes']['AttributesCollectible'])) {
			$collectible['AttributesCollectible'] = $wizardData['attributes']['AttributesCollectible'];
		}

		if (isset($collectible['AttributesCollectible'])) {
			foreach ($collectible['AttributesCollectible'] as $key => &$value) {
				$value['Attribute']['name'] = $value['name'];

			}
		}

		if (isset($wizardData['tags']['CollectiblesTag'])) {
			$collectible['CollectiblesTag'] = $wizardData['tags']['CollectiblesTag'];
		}

		if (isset($wizardData['image']['Upload'])) {
			$collectible['Upload'][0] = $wizardData['image']['Upload'];
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

		$series = $this -> Collectible -> Series -> find('first', array('conditions' => array('Series.id' => $collectible['Collectible']['series_id']), 'fields' => array('Series.name'), 'contain' => false));
		$collectible['Series'] = $series['Series'];

		$scale = $this -> Collectible -> Scale -> find('first', array('conditions' => array('Scale.id' => $collectible['Collectible']['scale_id']), 'fields' => array('Scale.scale'), 'contain' => false));
		$collectible['Scale'] = $scale['Scale'];

		if (isset($collectible['Collectible']['retailer_id'])) {
			$retailer = $this -> Collectible -> Retailer -> find('first', array('conditions' => array('Retailer.id' => $collectible['Collectible']['retailer_id']), 'fields' => array('Retailer.name'), 'contain' => false));
			$collectible['Retailer'] = $retailer['Retailer'];
		}

		// debug($collectible);
		// debug($wizardData);
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

		if ($this -> Session -> check('add.collectible.variant')) {
			// if (!isset($collectible['AttributesCollectible'])) {
			// $collectible['AttributesCollectible'] = array();
			// }

			// if (isset($wizardData['variantFeatures']['AttributesCollectible']) && !empty($wizardData['variantFeatures']['AttributesCollectible'])) {
			// $result = array_merge($collectible['AttributesCollectible'], $wizardData['variantFeatures']['AttributesCollectible']);
			// debug($result);
			// $collectible['AttributesCollectible'] = $result;
			// }
			$collectible['Collectible']['variant'] = 1;
		}
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

		$userId = $this -> getUserId();

		//user id of the person who added this collectible
		$collectible['Collectible']['user_id'] = $this -> getUserId();
		$collectible['Revision']['action'] = 'A';
		$collectible['Revision']['user_id'] = $this -> getUserId();

		//If there are any newly created Tags, we need to remove them from the array or else cake won't add
		//In the future we might want to update this to that we active tags
		//if(Configure::read('Settings.Approval.auto-approve') == 'true') {
		foreach ($collectible['CollectiblesTag'] as &$tag) {
			//unset($tag);
			$tag['Tag'] = array();
		}
		//}

		$this -> Collectible -> create();
		if ($this -> Collectible -> saveAll($collectible)) {
			$id = $this -> Collectible -> id;
			$addCollectible = $this -> Collectible -> findById($id);
			if (isset($wizardData['image']['Upload'])) {
				//Update the current one
				//Doing this so that we will trigger the revision
				$updateUpload = array();
				$updateUpload['Upload']['id'] = $wizardData['image']['Upload']['id'];
				$updateUpload['Upload']['collectible_id'] = $id;
				$updateUpload['Upload']['revision_id'] = $addCollectible['Collectible']['revision_id'];
				if (!$this -> Collectible -> Upload -> saveAll($updateUpload, array('validate' => false))) {
					//If it fails, let it pass but log the problem.
					$this -> log('Failed to update the upload with the collectible id and revision id for collectible ' . $addCollectible['Collectible']['id'] . ' and upload id ' . $addCollectible['Upload']['id'], 'error');
				}
			}
			//If I did set some attributes, lets update the revision ids for them as well.
			if (isset($addCollectible['AttributesCollectible']) && !empty($addCollectible['AttributesCollectible'])) {
				foreach ($addCollectible['AttributesCollectible'] as $key => &$value) {
					$value['revision_id'] = $addCollectible['Collectible']['revision_id'];
					unset($value['attribute_id']);
					unset($value['collectible_id']);
					unset($value['description']);
					unset($value['active']);
					unset($value['created']);
					unset($value['modified']);
				}
				//SINCE this is a new collectible and I am approving, this should be the newest of the collectible data out there so I should be fine with doing it on all attributes collectibles whose collectible io is the one I just approved.
				if (!$this -> Collectible -> AttributesCollectible -> saveAll($addCollectible['AttributesCollectible'], array('validate' => false))) {
					//If it fails, let it pass but log the problem.
					$this -> log('Failed to update the AttributesCollectible with the collectible id and revision id for collectible ' . $addCollectible['Collectible']['id'], 'error');
				}
			}

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
		$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('SpecializedType', 'Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Attribute', 'conditions' => array('AttributesCollectible.active' => 1)))));
		if (!empty($collectible) && $collectible['Collectible']['state'] === '0') {
			$this -> set('collectible', $collectible);
			$count = $this -> Collectible -> getNumberofCollectiblesInStash($id);
			$this -> set('collectibleCount', $count);

			if (!$collectible['Collectible']['variant']) {
				$variants = $this -> Collectible -> getCollectibleVariants($id);
				$this -> set('variants', $variants);
			}
		} else {
			$this -> render('viewMissing');
		}
	}

	function search() {
		/*
		 * Call the parent method now, that method handles pretty much everything now
		 */
		$this -> searchCollectible();
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

		$this -> paginate = array("conditions" => array('state' => 1), "contain" => array('Manufacture', 'License', 'Collectibletype', 'Upload', 'CollectiblesTag' => array('Tag')));

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
		$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('SpecializedType', 'Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Attribute', 'conditions' => array('AttributesCollectible.active' => 1)))));
		$this -> set('collectible', $collectible);
	}

	function admin_approve($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($id && is_numeric($id) && isset($this -> data['Approval']['approve'])) {
			$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('User', 'Upload', 'AttributesCollectible')));
			$this -> data = Sanitize::clean($this -> data);
			$notes = $this -> data['Approval']['notes'];
			//Approve
			if ($this -> data['Approval']['approve'] === 'true') {
				if (!empty($collectible) && $collectible['Collectible']['state'] === '1') {
					$data = array();
					$data['Collectible'] = array();
					$data['Collectible']['id'] = $collectible['Collectible']['id'];
					$data['Collectible']['state'] = 0;
					$data['Revision']['action'] = 'P';
					$data['Revision']['user_id'] = $this -> getUserId();
					$data['Revision']['notes'] = $this -> data['Approval']['notes'];
					if ($this -> Collectible -> saveAll($data, array('validate' => false))) {
						//Ugh need to get this again so I can get the Revision id
						$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('User', 'Upload', 'AttributesCollectible')));
						//update with the new revision id
						if (isset($collectible['Upload']) && !empty($collectible['Upload'])) {

							$this -> Collectible -> Upload -> id = $collectible['Upload'][0]['id'];
							if (!$this -> Collectible -> Upload -> saveField('revision_id', $collectible['Collectible']['revision_id'])) {
								//If it fails, let it pass but log the problem.
								$this -> log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
							}
						}
						//If I did set some attributes, lets update the revision ids for them as well.
						if (isset($collectible['AttributesCollectible']) && !empty($collectible['AttributesCollectible'])) {
							foreach ($collectible['AttributesCollectible'] as $key => &$value) {
								$value['revision_id'] = $collectible['Collectible']['revision_id'];
								unset($value['attribute_id']);
								unset($value['collectible_id']);
								unset($value['description']);
								unset($value['active']);
								unset($value['created']);
								unset($value['modified']);
							}

							//SINCE this is a new collectible and I am approving, this should be the newest of the collectible data out there so I should be fine with doing it on all attributes collectibles whose collectible io is the one I just approved.
							if (!$this -> Collectible -> AttributesCollectible -> saveAll($collectible['AttributesCollectible'], array('validate' => false))) {
								//If it fails, let it pass but log the problem.
								$this -> log('Failed to update the AttributesCollectible with the collectible id and revision id for collectible ' . $collectible['Collectible']['id'], 'error');
							}
						}

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
				if ($this -> Collectible -> delete($collectible['Collectible']['id'])) {

					//If this fails oh well
					//Have to do this because we have a belongsTo relationship on Collectible, probably should be a hasOne, fix at some point
					$this -> Collectible -> Revision -> delete($collectible['Collectible']['revision_id']);
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
			$this -> Email -> smtpOptions = array('port' => Configure::read('Settings.Email.port'), 'timeout' => Configure::read('Settings.Email.timeout'), 'host' => Configure::read('Settings.Email.host'), 'username' => Configure::read('Settings.Email.username'), 'password' => Configure::read('Settings.Email.password'));
			$this -> Email -> delivery = 'smtp';
			$this -> Email -> to = $email;

			$this -> Email -> from = Configure::read('Settings.Email.from');
			if ($approvedChange) {
				$this -> Email -> template = 'add_approval';
				$this -> Email -> subject = 'Your submission has been successfully approved!';
				$this -> set('collectible_url', 'http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectileId);
			} else {
				$this -> Email -> template = 'add_deny';
				$this -> Email -> subject = 'Oh no! Your submission has been denied.';
			}
			$this -> Email -> sendAs = 'both';
			$return = $this -> Email -> send();
			$this -> set('smtp_errors', $this -> Email -> smtpError);
			if (!empty($this -> Email -> smtpError)) {
				$return = false;
				$this -> log('There was an issue sending the email ' . $this -> Email -> smtpError . ' for user ' . $email, 'error');
			}
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

