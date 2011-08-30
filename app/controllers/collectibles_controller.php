<?php
App::import('Sanitize');
class CollectiblesController extends AppController {

	var $name = 'Collectibles';

	var $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload', 'CollectibleDetail');

	var $components = array('RequestHandler', 'Wizard.Wizard');

	var $actsAs = array('Searchable.Searchable');

	function beforeFilter() {
		parent::beforeFilter();
		$this -> Wizard -> steps = array('manufacture', array('variant' => 'variantFeatures'), 'attributes', 'tags', 'image', 'review');
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
		//Always delete this stuff, this could go in a better spot in the future
		//Should probably update this so I set to show and not show different things, not put the biz logic in the view
		$this -> Session -> delete('preSaveCollectible');
		$this -> Session -> delete('uploadId');
		$this -> Session -> delete('add.collectible.manufacture');
		$this -> Session -> delete('add.collectible.collectibleType');
		$this -> Session -> delete('add.collectible.variant');

		//TODO I shouldn't need these
		$this -> Session -> delete('add.collectible.mode.collectible');
		$this -> Session -> delete('add.collectible.mode.variant');
		$this -> Session -> delete('edit.collectible.mode.collectible');
		$this -> Session -> delete('edit.collectible.mode.variant');
		$this -> Session -> delete('variant.add-id');
		$this -> Session -> delete('variant.base-collectible');
		$this -> Session -> delete('add.collectible.variant.collectible');
		$this -> Session -> delete('addCollectibleId');
		$this -> Wizard -> resetWizard();
		//check to see if there is data submitted
		if (!empty($this -> data)) {
			debug($this -> data);
			if ($this -> data['Collectible']['massProduced'] === 'true') {
				$this -> redirect(array('action' => 'massProduced'));

				//$this -> Wizard -> branch('variant', true);
				//$this -> redirect( array('action' => 'wizard/manufacture'));
			} else if ($this -> data['Collectible']['massProduced'] == 'false') {
				$this -> redirect(array('action' => 'nonMassProduced'));
				//$this -> Wizard -> branch('variant');
				//$this -> Session -> write('add.collectible.mode.variant', true);

				//$this -> redirect( array('action' => 'addVariantSelectCollectible', 'initial' => 'yes'));
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
			debug($this -> data);
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
			$validManufacture = $this -> Collectible -> Manufacture -> find('first', array('conditions' => array('Manufacture.id' => $id)));
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
		debug($manufactures);
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
				$this -> Wizard -> branch('variant', true);
				$this -> redirect(array('action' => 'wizard/manufacture'));
			} else {
				$this -> Session -> setFlash(__('Please select a valid Collectible Type', true), null, null, 'error');
			}
		}
		$manufactureId = $this -> Session -> read('add.collectible.manufacture');
		$this -> loadModel('CollectibletypesManufacture');
		$this -> paginate = array('conditions' => array('CollectibletypesManufacture.manufacture_id' => $manufactureId['Manufacture']['id']), 'contain' => array('Collectibletype' => array('order' => array('Collectibletype.name' => 'ASC'))));
		$collectibleTypes = $this -> paginate('CollectibletypesManufacture');
		debug($collectibleTypes);
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
			$variantCollectible = $this -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $id), 'contain' => array('Manufacture', 'Collectibletype', 'CollectiblesTag', 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'AttributesCollectible' => array('Attribute'))));

			if (!empty($variantCollectible)) {
				$manufacturer = $this -> Session -> read('add.collectible.manufacture');
				//make sure that the collectible they selected is the same as the manufacture they selected...hackerz
				if ($variantCollectible['Collectible']['manufacture_id'] === $manufacturer['Manufacture']['id']) {
					$this -> Session -> write('add.collectible.variant', $variantCollectible);
					$this -> Session -> write('add.collectible.collectibleType', array('Collectibletype' => $variantCollectible['Collectibletype']));
					$this -> Wizard -> branch('variant');
					$this -> redirect(array('action' => 'wizard/manufacture'));
				} else {
					$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
					$this -> redirect(array('action' => 'addSelectType'));
				}
			}
		}

		$this -> data = Sanitize::clean($this -> data, array('encode' => false));
		$manufactureId = $this -> Session -> read('add.collectible.manufacture');
		$this -> searchCollectible(array('Collectible.variant' => '0', 'Manufacture.id' => $manufactureId['Manufacture']['id']));
	}

	function nonManufactured() {
		$this -> checkLogIn();
	}

	function confirm() {
		$id = $this -> Session -> read('addCollectibleId');
		if (isset($id) && $id != null) {
			$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Manufacture', 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'AttributesCollectible' => array('Attribute'))));
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
		$this -> set('collectible_title', __('Add Collectible', true));
		if (!$this -> Session -> check('add.collectible.manufacture') && !$this -> Session -> check('add.collectible.collectibleType')) {
			//If it has not, start them over
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect(array('action' => 'addSelectType'));
		}
		$manufacturer = $this -> Session -> read('add.collectible.manufacture');
		$collectibleType = $this -> Session -> read('add.collectible.collectibleType');
		$this -> set(compact('manufacturer'));
		$this -> set(compact('collectibleType'));

		//If data is empty then this is the first time we are coming here or a refresh or something.
		if (empty($this -> data)) {
			debug($this -> Session -> check('add.collectible.variant'));
			debug($this -> Session -> read('add.collectible.variant'));
			if ($this -> Session -> check('add.collectible.variant')) {
				// if(isset($this -> params['pass'][1])) {
				//TODO store the collectible in the session for going back
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				$this -> set('collectible', $variantCollectible);
				$this -> data = $variantCollectible;
				// $variantCollectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $this -> params['pass'][1]), 'contain' => array('Manufacture', 'Collectibletype', 'License', 'Series', 'Approval', 'Scale', 'Retailer', 'Upload', 'AttributesCollectible' => array('Attribute'))));
				// $this -> Session -> write('add.collectible.variant.collectible', $variantCollectible);
				// $this -> set('collectible', $variantCollectible);
				// debug($variantCollectible);
				// $this -> data = $variantCollectible;
				// } else {
				// $variantCollectible = $this -> Session -> read('add.collectible.variant.collectible');
				// debug($variantCollectible);
				// $this -> set('collectible', $variantCollectible);
				// }
			}
			// $manufactureData = $this -> Collectible -> Manufacture -> getManufactureListData();
			// debug($manufactureData);
			// $this -> set('manufactures', $manufactureData['manufactures']);
			// $this -> set('licenses', $manufactureData['licenses']);
			// $this -> set('collectibletypes', $manufactureData['collectibletypes']);

			// if(isset($newCollectible['Collectible']['series_id'])) {
			// $series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($newCollectible['Collectible']['manufacture_id'], $newCollectible['Collectible']['license_id']);
			// $this -> set('series', $series);
			// }

			$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
			$this -> set(compact('scales'));
		} else {
			//If data is not empty, then we submitted and it errored or we are coming back to edit
			if ($this -> Session -> check('add.collectible.variant')) {
				$variantCollectible = $this -> Session -> read('add.collectible.variant');
				debug($variantCollectible);
				$this -> set('collectible', $variantCollectible);
			}
		}

		$manufactureData = $this -> Collectible -> Manufacture -> getManufactureData($manufacturer['Manufacture']['id']);
		debug($manufactureData);
		$this -> set('manufactures', $manufactureData['manufactures']);
		$this -> set('licenses', $manufactureData['licenses']);
		// $this -> set('collectibletypes', $manufactureData['collectibletypes']);

		if (isset($wizardData['manufacture']['Collectible']['series_id'])) {
			$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($this -> data['Collectible']['manufacture_id'], $this -> data['Collectible']['license_id']);
			$this -> set('series', $series);
		}

		$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
		$this -> set(compact('scales'));

	}

	function _processManufacture() {
		//check if user is logged in
		if ($this -> isLoggedIn()) {
			debug($this -> data);
			$newCollectible = array();
			//First check to see if this is a post
			// if(!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data);
			$manufacturer = $this -> Session -> read('add.collectible.manufacture');
			$collectibleType = $this -> Session -> read('add.collectible.collectibleType');
			$this -> data['Collectible']['manufacture_id'] = $manufacturer['Manufacture']['id'];
			$this -> data['Collectible']['collectibletype_id'] = $collectibleType['Collectibletype']['id'];
			//Since this is a post, take the data that was submitted and set it to our variable
			$newCollectible = $this -> data;
			//set default to true
			$validCollectible = true;

			//First try and validate the collectible.
			$this -> Collectible -> set($newCollectible);
			if ($this -> Collectible -> validates()) {

			} else {
				debug($this -> Collectible -> invalidFields());
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				$validCollectible = false;
			}

			if ($validCollectible) {

				//set pending to 2...this really needs to check if user is admin first TODO
				$newCollectible['Collectible']['state'] = 2;
				$userId = $this -> getUserId();
				//set the id of the user who is adding this collectible
				//$newCollectible['Approval']['user_id'] = $userId;
				//$newCollectible['Approval']['date_added'] = date("Y-m-d H:i:s", time());
				//set the man id of this collectible
				//$newCollectible['Collectible']['manufacture_id'] = $manufactureId;
				//set the license id of this collectible
				//$newCollectible['Collectible']['license_id'] = $licenseId;
				//$newCollectible['Collectible']['approval_id'] = '1';

				//Alright since it validates, lets next check to see if
				//any other collectibles out there exist that might be the
				//same so we are not adding duplicates
				// $conditions = array();
				// //Make sure they are approved already, might want to change this later
				// array_push($conditions, array('Approval.state' => '0'));
				// //Make sure it is not a variant
				// array_push($conditions, array('Collectible.variant' => '0'));
				// //Search on just the name for now
				// array_push($conditions, array('Collectible.name LIKE' => '%' . $newCollectible['Collectible']['name'] . '%'));
				// //array_push($conditions, array("LIKE(Collectible.name) AGAINST('{$this->data['Collectible']['name']}' IN BOOLEAN MODE)"));
				// $this -> paginate = array("conditions" => array($conditions), "contain" => array('Manufacture', 'License', 'Collectibletype', 'Upload', 'Approval'), 'limit' => 1);
				// $existingCollectibles = $this -> paginate('Collectible');
				//
				// $currentCollectible = $this -> Session -> read('preSaveCollectible');
				//
				// if(!is_null($currentCollectible)) {
				// if(isset($currentCollectible['Upload'])) {
				// $newCollectible['Upload'] = $currentCollectible['Upload'];
				// }
				// }
				//
				// $this -> Session -> write('preSaveCollectible', $newCollectible);
				//
				// //If the size is greater than zero when we have a potential
				// //collectible that is similar.
				// if(count($existingCollectibles) > 0) {
				// debug($existingCollectibles);
				// $this -> set('existingCollectibles', $existingCollectibles);
				// //TODO we need to figure out how to handle this appropriately...not sure we want add this step in the wizard
				// //I think I am going to do this differently, maybe an ajax on demand search that will post a link to a list or something
				// $this -> render('existingCollectibles');
				// return ;
				// } else {
				//
				// }

				return true;
			} else {
				return false;
			}
		} else {
			$this -> redirect(array('controller' => 'users', 'action' => 'login'), null, true);
		}
		return false;
	}

	function _processVariantFeatures() {
		$this -> data = Sanitize::clean($this -> data);
		return true;
	}

	function _prepareVariantFeatures() {
		if ($this -> Session -> check('add.collectible.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant');
			debug($variantCollectible);
			$this -> set('collectible', $variantCollectible);
		}
		$retailers = $this -> Collectible -> Retailer -> getRetailerList();
		$this -> set('retailers', $retailers);
	}

	function _prepareAttributes() {
		if ($this -> Session -> check('add.collectible.mode.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant.collectible');
			debug($variantCollectible);
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
			debug($this -> data);
			//Uh this doesn't make sense, do I need to loop through each of these to validate?
			if (isset($this -> data['AttributesCollectible'])) {
				foreach ($this -> data['AttributesCollectible'] as $key => $attribue) {
					$this -> AttributesCollectible -> set($attribue);
					//debug($this -> AttributesCollectible);
					if ($this -> AttributesCollectible -> validates()) {
						return true;
					} else {
						debug($this -> AttributesCollectible -> invalidFields());
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
		debug($wizardData);
		if (isset($wizardData['tags']['CollectiblesTag'])) {
			$this -> data['Tag'] = $wizardData['tags']['CollectiblesTag'];
		}

	}

	function _processTags() {
		//TODO should validate
		$this -> data = Sanitize::clean($this -> data);
		debug($this -> data);
		$this -> loadModel('Tag');
		$processedTags = $this -> Tag -> processAddTags($this -> data['CollectiblesTag']);
		$this -> data['CollectiblesTag'] = $processedTags;
		debug($this -> data);
		return true;

	}

	function _prepareImage() {
		$wizardData = $this -> Wizard -> read();
		if ($this -> Session -> check('add.collectible.mode.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant.collectible');
			debug($variantCollectible);
			$this -> set('collectible', $variantCollectible);
		}
		//debug($this -> Session -> read('Wizard.Collectibles.image'));
		debug($wizardData);
		$collectible = array();
		// if(isset($wizardData['image']['Upload'])) {
		// $collectible['Upload'] = $wizardData['image']['Upload'];
		// $this -> set('collectible', $collectible);
		// }
	}

	function _processImage() {
		if (!empty($this -> data)) {
			debug($this -> data['remove']);
			if (isset($this -> data['skip']) && $this -> data['skip'] === 'true') {
				return true;
			} else if (isset($this -> data['remove']) && $this -> data['remove'] === 'true') {
				debug($this -> data);
				$wizardData = $this -> Wizard -> read();
				$uploadId = $this -> Session -> read('uploadId');
				if (isset($wizardData['image']['Upload']) && !empty($uploadId)) {
					$imageId = $uploadId;
					if ($this -> Collectible -> Upload -> delete($imageId)) {
						//unset($collectible['Upload']);
						//$this -> Session -> write('preSaveCollectible', $collectible);
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
							debug($upload);
							$this -> Session -> write('uploadId', $uploadId);
							return true;
						} else {
							debug($this -> Collectible -> Upload -> validationErrors);
							$this -> Session -> setFlash(__('Oops! There was an issue uploading your photo.', true), null, null, 'error');
							return false;
						}
					} else {
						$this -> Collectible -> Upload -> validationErrors['0']['file'] = 'Image is required.';
						debug($this -> Collectible -> Upload -> invalidFields());
						$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
						return false;
					}
				} else {
					$collectible = $this -> Session -> read('preSaveCollectible');
					$this -> set(compact('collectible'));
				}

			}
		} else {
			$collectible = $this -> Session -> read('preSaveCollectible');
			$this -> set(compact('collectible'));
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
		debug($wizardData);
		$collectible = array();
		$collectible['Collectible'] = $wizardData['manufacture']['Collectible'];
		if (isset($wizardData['attributes']['AttributesCollectible'])) {
			$collectible['AttributesCollectible'] = $wizardData['attributes']['AttributesCollectible'];
		}

		if ($this -> Session -> check('add.collectible.mode.variant')) {
			if (isset($wizardData['variantFeatures']['AttributesCollectible']) && !empty($wizardData['variantFeatures']['AttributesCollectible'])) {
				if (isset($collectible['AttributesCollectible'])) {
					$result = array_merge($collectible['AttributesCollectible'], $wizardData['variantFeatures']['AttributesCollectible']);
				} else {
					$result = $wizardData['variantFeatures']['AttributesCollectible'];
				}
				debug($result);
				$collectible['AttributesCollectible'] = $result;
			}
		}

		if (isset($collectible['AttributesCollectible'])) {
			foreach ($collectible['AttributesCollectible'] as $key => &$value) {
				$value['Attribute']['name'] = $value['name'];
				debug($value);
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

		$manufacture = $this -> Collectible -> Manufacture -> find('first', array('conditions' => array('Manufacture.id' => $collectible['Collectible']['manufacture_id']), 'fields' => array('Manufacture.title', 'Manufacture.url'), 'contain' => false));
		$collectible['Manufacture'] = $manufacture['Manufacture'];

		$collectibleType = $this -> Collectible -> Collectibletype -> find('first', array('conditions' => array('Collectibletype.id' => $collectible['Collectible']['collectibletype_id']), 'fields' => array('Collectibletype.name'), 'contain' => false));
		$collectible['Collectibletype'] = $collectibleType['Collectibletype'];

		$license = $this -> Collectible -> License -> find('first', array('conditions' => array('License.id' => $collectible['Collectible']['license_id']), 'fields' => array('License.name'), 'contain' => false));
		$collectible['License'] = $license['License'];

		$scale = $this -> Collectible -> Scale -> find('first', array('conditions' => array('Scale.id' => $collectible['Collectible']['scale_id']), 'fields' => array('Scale.scale'), 'contain' => false));
		$collectible['Scale'] = $scale['Scale'];

		debug($collectible);
		debug($wizardData);
		if ($this -> Session -> check('add.collectible.variant')) {
			$variantCollectible = $this -> Session -> read('add.collectible.variant');
			debug($variantCollectible);
			$this -> set('collectible', $variantCollectible);
			$collectible['Collectible']['variant'] = 1;
		}

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

		if ($this -> Session -> check('add.collectible.mode.variant')) {
			if (!isset($collectible['AttributesCollectible'])) {
				$collectible['AttributesCollectible'] = array();
			}

			if (isset($wizardData['variantFeatures']['AttributesCollectible']) && !empty($wizardData['variantFeatures']['AttributesCollectible'])) {
				$result = array_merge($collectible['AttributesCollectible'], $wizardData['variantFeatures']['AttributesCollectible']);
				debug($result);
				$collectible['AttributesCollectible'] = $result;
			}
			$collectible['Collectible']['variant'] = 1;
		}
		/* Since they confirmed, now set to pending = 1.  I really don't like how
		 this is setup right now but it works because of the image thing.
		 A 1 means that this collectible needs to be approved by an admin first
		 *
		 * TODO: If we are not auto approving, then do we need to make sure that attributes_collectible is set to not active?
		 * */
		$pendingState = '1';
		if ($this -> isUserAdmin() || Configure::read('Settings.Approval.auto-approve') == 'true') {
			$pendingState = '0';
		}

		$collectible['Collectible']['state'] = $pendingState;

		$userId = $this -> getUserId();

		//user id of the person who added this collectible
		$collectible['Collectible']['user_id'] = $this -> getUserId();
		$collectible['Collectible']['action'] = 'A';
		debug($collectible);

		//If there are any newly created Tags, we need to remove them from the array or else cake won't add
		//In the future we might want to update this to that we active tags
		//if(Configure::read('Settings.Approval.auto-approve') == 'true') {
		foreach ($collectible['CollectiblesTag'] as &$tag) {
			//unset($tag);
			$tag['Tag'] = array();
		}
		//}

		debug($collectible);

		$this -> Collectible -> create();
		if ($this -> Collectible -> saveAll($collectible)) {
			$id = $this -> Collectible -> id;
			// $collectible = $this -> Collectible -> findById($id);
			if (isset($wizardData['image']['Upload'])) {
				$this -> Collectible -> Upload -> id = $wizardData['image']['Upload'];
				$this -> Collectible -> Upload -> saveField('collectible_id', $id);
			}

			$this -> Session -> write('addCollectibleId', $id);
			$this -> Session -> delete('preSaveCollectible');
			$this -> Session -> delete('uploadId');
			$this -> Session -> delete('add.collectible.manufacture');
			$this -> Session -> delete('add.collectible.collectibleType');
			$this -> Session -> delete('add.collectible.variant');
			return true;
		} else {
			debug($this -> Collectible -> validationErrors);
			$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			return false;
		}
	}

	function view($id = null) {
		if (!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect(array('action' => 'index'));
		}
		$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Attribute', 'conditions' => array('AttributesCollectible.active' => 1)))));

		if (!empty($collectible) && $collectible['Collectible']['state'] === '0') {
			debug($collectible);
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

	function cancel() {
		$uploadId = $this -> Session -> read('uploadId');
		debug($uploadId);

		$upload = $this -> Collectible -> Upload -> findById($uploadId);
		//if($this -> FileUpload -> removeFile($upload['Upload']['name'])) {
		$this -> Collectible -> Upload -> delete($uploadId);
		//} else {
		//	$this->log('Failed to remove file: ' . $upload['Upload']['name'] . ' linked to id '. $uploadId);
		//}
		$this -> Session -> delete('preSaveCollectible');
		$this -> Session -> delete('uploadId');
		$this -> redirect(array('action' => 'addSelectType'));

	}

	function search() {
		debug($this -> params['url']);
		//Ok so the core search checks post data, but this is being passed in via get data so hack for now
		//Should update this if we want to pass multiple paramaters in
		$this -> data['Search'] = array();
		$this -> data['Search']['search'] = '';
		if (isset($this -> params['url']['q'])) {
			$this -> data['Search']['search'] = $this -> params['url']['q'];
		}
		if (isset($this -> params['url']['m'])) {
			//find all of this license
			$this -> data['Search']['Manufacture'] = array();
			$this -> data['Search']['Manufacture']['Filter'] = array();
			$this -> data['Search']['Manufacture']['Filter'][$this -> params['url']['m']] = 1;
		}
		if (isset($this -> params['url']['l'])) {
			//find all of this license
			$this -> data['Search']['License'] = array();
			$this -> data['Search']['License']['Filter'] = array();
			$this -> data['Search']['License']['Filter'][$this -> params['url']['l']] = 1;
		}
		if (isset($this -> params['url']['ct'])) {
			//find all of this license
			$this -> data['Search']['CollectibleType'] = array();
			$this -> data['Search']['CollectibleType']['Filter'] = array();
			$this -> data['Search']['CollectibleType']['Filter'][$this -> params['url']['ct']] = 1;
		}
		if (isset($this -> params['url']['t'])) {
			//find all of this license
			$this -> data['Search']['Tag'] = array();
			$this -> data['Search']['Tag']['Filter'] = array();
			$this -> data['Search']['Tag']['Filter'][$this -> params['url']['t']] = 1;
		}
		$this -> searchCollectible();
	}

	function history($id = null) {
		$this -> checkLogIn();
		if ($id && is_numeric($id)) {
			$this -> Collectible -> id = $id;
			$history = $this -> Collectible -> revisions(null, true);
			$this -> loadModel('User');

			//This is like the worst thing ever and needs to get cleaned up
			//Making this by reference so we can modify it, is this proper in php?
			foreach ($history as $key => &$collectible) {
				if ($collectible['Collectible']['action'] !== 'A') {
					$editUserDetails = $this -> User -> findById($collectible['Collectible']['edit_user_id'], array('contain' => false));
					$collectible['Collectible']['user_name'] = $editUserDetails['User']['username'];
				} else {
					$userId = $collectible['Collectible']['user_id'];
					$userDetails = $this -> User -> findById($userId, array('contain' => false));
					$collectible['Collectible']['user_name'] = $userDetails['User']['username'];
				}
			}

			debug($history);
			$this -> set(compact('history'));
			//Grab a list of all attributes associated with this collectible, or were associated with this collectible.  We will display a list of all
			//of these attributes then we can go into further history detail if we need too
			$attributeHistory = $this -> Collectible -> AttributesCollectible -> find("all", array('conditions' => array('AttributesCollectible.collectible_id' => $id, 'AttributesCollectible.variant' => 0)));
			$this -> set(compact('attributeHistory'));
			debug($attributeHistory);
			//Update this in the future since we only allow one Upload for now
			$collectibleUpload = $this -> Collectible -> Upload -> find("first", array('contain' => false, 'conditions' => array('Upload.collectible_id' => $id)));

			$this ->  Collectible -> Upload -> id = $collectibleUpload['Upload']['id'];
			$uploadHistory = $this ->  Collectible -> Upload -> revisions(null, true);
			//This is like the worst thing ever and needs to get cleaned up
			//Making this by reference so we can modify it, is this proper in php?
			foreach ($uploadHistory as $key => &$upload) {
				if ($upload['Upload']['action'] !== 'A') {
					$editUserDetails = $this -> User -> findById($upload['Upload']['edit_user_id'], array('contain' => false));
					$upload['Upload']['user_name'] = $editUserDetails['User']['username'];
				} else {
					$userId = $upload['Upload']['edit_user_id'];
					$userDetails = $this -> User -> findById($userId, array('contain' => false));
					$upload['Upload']['user_name'] = $userDetails['User']['username'];
				}
			}
			debug($uploadHistory);
			$this -> set(compact('uploadHistory'));
		} else {
			$this -> redirect($this -> referer());
		}
	}

	function historyDetail($id = null, $version_id = null) {
		$this -> checkLogIn();
		debug($id);
		debug($version_id);
		if ($id && $version_id && is_numeric($id) && is_numeric($version_id)) {
			$this -> Collectible -> id = $id;
			$collectible = $this -> Collectible -> revisions(array('conditions' => array('version_id' => $version_id)), true);
			debug($collectible);
			$this -> set(compact('collectible'));

		} else {
			//$this -> redirect($this -> referer());
		}
	}

	function admin_index() {
		$this -> checkLogIn();
		$this -> checkAdmin();

		$this -> paginate = array("conditions" => array('state' => 1), "contain" => array('Manufacture', 'License', 'Collectibletype', 'Upload', 'CollectiblesTag' => array('Tag')));

		$collectilbes = $this -> paginate('Collectible');
		debug($collectilbes);
		$this -> set('collectibles', $collectilbes);

	}

	function admin_view($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();

		if (!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect(array('action' => 'index'));
		}
		$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Manufacture', 'User' => array('fields' => 'User.username'), 'Collectibletype', 'License', 'Series', 'Scale', 'Retailer', 'Upload', 'CollectiblesTag' => array('Tag'), 'AttributesCollectible' => array('Attribute', 'conditions' => array('AttributesCollectible.active' => 1)))));
		$this -> set('collectible', $collectible);
	}

	function admin_approve($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();

		$collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => false));
		debug($collectible);
		if (!empty($collectible) && $collectible['Collectible']['state'] === '1') {
			$this -> Collectible -> id = $collectible['Collectible']['id'];
			$data = array();
			$data['Collectible'] = array();
			$data['Collectible']['action'] = 'P';
			$data['Collectible']['edit_user_id'] = $this -> getUserId();
			$data['Collectible']['state'] = 0;
			if ($this -> Collectible -> save($data, false)) {

			}

		}
	}

}
?>

