<?php
App::import('Sanitize');
class CollectiblesController extends AppController {

	var $name = 'Collectibles';

	var $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload');

	var $components = array('RequestHandler');

	var $actsAs = array('Searchable.Searchable');

	function view($id =null) {
		if(!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect( array('action' => 'index'));
		}
		$collectible = $this -> Collectible -> findById($id);
		$this -> set('collectible', $collectible);
		$count = $this -> Collectible -> getNumberofCollectiblesInStash($id);
		$this -> set('collectibleCount', $count);

		if(!$collectible['Collectible']['variant']) {
			$variants = $this -> Collectible -> getCollectibleVariants($id);
			$this -> set('variants', $variants);
		}
	}

	function addSelectType() {
		if($this -> isLoggedIn()) {
			//Always delete this stuff, this could go in a better spot in the future
			//Should probably update this so I set to show and not show different things, not put the biz logic in the view
			$this -> Session -> delete('preSaveCollectible');
			$this -> Session -> delete('uploadId');
			$this -> Session -> delete('add.collectible.mode.collectible');
			$this -> Session -> delete('add.collectible.mode.variant');
			$this -> Session -> delete('edit.collectible.mode.collectible');
			$this -> Session -> delete('edit.collectible.mode.variant');
			$this -> Session -> delete('variant.add-id');
			$this -> Session -> delete('variant.base-collectible');
			//check to see if there is data submitted
			if(!empty($this -> data)) {
				if($this -> data['Collectible']['addType'] == 'C') {
					debug($this -> data);
					$this -> redirect( array('action' => 'addCollectible'));
				} else if($this -> data['Collectible']['addType'] == 'V') {
					debug($this -> data);
					$this -> redirect( array('action' => 'addVariantSelectCollectible', 'initial' => 'yes'));
				} else {
					$this -> Session -> setFlash(__('Please select an option to add.', true), null, null, 'error');
				}
			}

		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	/**
	 *
	 */
	function addCollectible() {
		//check if user is logged in
		if($this -> isLoggedIn()) {
			$newCollectible = array();
			$this -> Session -> delete('collectible');
			$this -> Session -> write('add.collectible.mode.collectible', true);
			$this -> Session -> delete('add.collectible.mode.variant');
			$this -> set('collectible_title', __('Add Collectible', true));
			//First check to see if this is a post
			if(!empty($this -> data)) {
				$this -> data['Collectible'] = Sanitize::clean($this -> data['Collectible']);
				//Since this is a post, take the data that was submitted and set it to our variable
				$newCollectible = $this -> data;
				//set default to true
				$validCollectible = true;
				//set default to false
				$editMode = false;

				//First check if we are in edit mode
				if($this -> data['Edit'] === true) {
					$editMode = true;
				}

				//First try and validate the collectible.
				$this -> Collectible -> set($newCollectible);
				if($this -> Collectible -> validates()) {

				} else {
					$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					$validCollectible = false;
				}

				if($validCollectible) {

					//set pending to 2...this really needs to check if user is admin first TODO
					$newCollectible['Approval']['state'] = 2;
					$userId = $this -> getUserId();
					//set the id of the user who is adding this collectible
					$newCollectible['Approval']['user_id'] = $userId;
					//$newCollectible['Approval']['date_added'] = date("Y-m-d H:i:s", time());
					//set the man id of this collectible
					//$newCollectible['Collectible']['manufacture_id'] = $manufactureId;
					//set the license id of this collectible
					//$newCollectible['Collectible']['license_id'] = $licenseId;
					//$newCollectible['Collectible']['approval_id'] = '1';

					//Alright since it validates, lets next check to see if
					//any other collectibles out there exist that might be the
					//same so we are not adding duplicates
					$conditions = array();
					//Make sure they are approved already, might want to change this later
					array_push($conditions, array('Approval.state' => '0'));
					//Make sure it is not a variant
					array_push($conditions, array('Collectible.variant' => '0'));
					//Search on just the name for now
					array_push($conditions, array('Collectible.name LIKE' => '%' . $newCollectible['Collectible']['name'] . '%'));
					//array_push($conditions, array("LIKE(Collectible.name) AGAINST('{$this->data['Collectible']['name']}' IN BOOLEAN MODE)"));
					$this -> paginate = array("conditions" => array($conditions), "contain" => array('Manufacture', 'License', 'Collectibletype', 'Upload', 'Approval'), 'limit' => 1);
					$existingCollectibles = $this -> paginate('Collectible');

					$currentCollectible = $this -> Session -> read('preSaveCollectible');

					if(!is_null($currentCollectible)) {
						if(isset($currentCollectible['Upload'])) {
							$newCollectible['Upload'] = $currentCollectible['Upload'];
						}
					}

					$this -> Session -> write('preSaveCollectible', $newCollectible);

					//If the size is greater than zero when we have a potential
					//collectible that is similar.
					if(count($existingCollectibles) > 0) {
						debug($existingCollectibles);
						$this -> set('existingCollectibles', $existingCollectibles);
						$this -> render('existingCollectibles');
						return ;
					} else {
						//$this -> redirect( array('action' => 'addImage'));

						//$this -> redirect( array('action' => 'review'));
					}
				} else {
					$manufactureData = $this -> Collectible -> Manufacture -> getManufactureData($newCollectible['Collectible']['manufacture_id']);
					$this -> set('manufactures', $manufactureData['manufactures']);
					$this -> set('licenses', $manufactureData['licenses']);

					if(isset($newCollectible['Collectible']['series_id'])) {
						$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($newCollectible['Collectible']['manufacture_id'], $newCollectible['Collectible']['license_id']);
						$this -> set('series', $series);
					}

					// $this -> set('series', $manufactureData['series']);
					$this -> set('collectibletypes', $manufactureData['collectibletypes']);
					$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
					$this -> set(compact('scales'));
				}

			} else if(!empty($this -> params['named']['edit'])) {
				$collectible = $this -> Session -> read('preSaveCollectible');
				$this -> data = $collectible;
				$this -> set('edit', true);
				$manufactureData = $this -> Collectible -> Manufacture -> getManufactureData($collectible['Collectible']['manufacture_id']);
				$this -> set('manufactures', $manufactureData['manufactures']);
				$this -> set('licenses', $manufactureData['licenses']);
				$this -> set('collectibletypes', $manufactureData['collectibletypes']);

				if(isset($newCollectible['Collectible']['series_id'])) {
					$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($newCollectible['Collectible']['manufacture_id'], $newCollectible['Collectible']['license_id']);
					$this -> set('series', $series);
				}

				$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
				$this -> set(compact('scales'));

			} else {
				/*
				 * Grab a list of all manufactures.
				 *
				 * Then using the first manufacture as the base, grab its licenses and types.
				 *
				 * These will be dynamic on the page but we want to prime the page with data.
				 */
				$manufactureData = $this -> Collectible -> Manufacture -> getManufactureListData();
				$this -> set('manufactures', $manufactureData['manufactures']);
				$this -> set('licenses', $manufactureData['licenses']);
				$this -> set('collectibletypes', $manufactureData['collectibletypes']);

				if(isset($newCollectible['Collectible']['series_id'])) {
					$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($newCollectible['Collectible']['manufacture_id'], $newCollectible['Collectible']['license_id']);
					$this -> set('series', $series);
				}

				$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
				$this -> set(compact('scales'));
			}

		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	function addVariantSelectCollectible() {
		if($this -> isLoggedIn()) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			$this -> searchCollectible( array('Collectible.variant' => '0'));
		}
	}

	function addVariant($id =null) {
		$this -> checkLogIn();
		$this -> Session -> write('add.collectible.mode.variant', true);
		$this -> Session -> delete('add.collectible.mode.collectible');
		$this -> set('collectible_title', __('Add Collectible', true));

		//If edit do not do this again
		//If it is not an edit and the id is passed, store and grab the collectible
		if($id && empty($this -> params['named']['edit'])) {
			$variantCollectible = $this -> Collectible -> findById($id);
			$this -> set('collectible', $variantCollectible);
			//$this -> data = $variantCollectible;
			$this -> Session -> write('variant.add-id', $id);
			$this -> Session -> write('variant.base-collectible', $variantCollectible);
		}

		if(!empty($this -> data)) {
			debug($this -> data);
			$this -> data['Collectible'] = Sanitize::clean($this -> data['Collectible']);

			$validCollectible = true;
			//set default to false
			$editMode = false;

			//First check if we are in edit mode
			if($this -> data['Edit'] === true) {
				$editMode = true;
			}

			$this -> data['Approval']['state'] = 2;
			$userId = $this -> getUserId();
			//set the id of the user who is adding this collectible
			$this -> data['Approval']['user_id'] = $userId;
			// -> data['Approval']['date_added'] = date("Y-m-d H:i:s", time());

			//Grab the id of the collectible this is a variant for
			$id = $this -> Session -> read('variant.add-id');
			//read that collecrible
			$baseCollectible = $this -> Session -> read('variant.base-collectible');
			//do this for display purposes
			$this -> set('collectible', $baseCollectible);

			$variantCollectible = array();

			$variantCollectible['Approval'] = $this -> data['Approval'];
			//Set the base of that collectible, we will override with what the user
			//entered.
			$variantCollectible['Collectible'] = $this -> data['Collectible'];
			//$newCollectible['Upload'] = $this -> data['Upload'];
			$variantCollectible['Collectible']['id'] = '';
			$variantCollectible['Collectible']['approval_id'] = '';

			if(isset($this -> data['AttributesCollectible'])) {
				$variantCollectible['AttributesCollectible'] = $this -> data['AttributesCollectible'];
			}

			$variantCollectible['Collectible']['manufacture_id'] = $baseCollectible['Collectible']['manufacture_id'];
			$variantCollectible['Collectible']['license_id'] = $baseCollectible['Collectible']['license_id'];
			$variantCollectible['Collectible']['series_id'] = $baseCollectible['Collectible']['series_id'];
			$variantCollectible['Collectible']['collectibletype_id'] = $baseCollectible['Collectible']['collectibletype_id'];
			$variantCollectible['Collectible']['scale_id'] = $baseCollectible['Collectible']['scale_id'];

			//These are the things that variant can override
			//$newCollectible['Collectible']['name'] = $this -> data['Collectible']['name'];
			//$newCollectible['Collectible']['exclusive'] = $this -> data['Collectible']['exclusive'];
			//$newCollectible['Collectible']['edition_size'] = $this -> data['Collectible']['edition_size'];
			//$newCollectible['Collectible']['url'] = $this -> data['Collectible']['url'];
			$variantCollectible['Collectible']['variant'] = '1';
			//we are saving what collectible this variant came from.
			$variantCollectible['Collectible']['variant_collectible_id'] = $id;
			//Null thee out so they will update, probably should go in the model
			unset($variantCollectible['Collectible']['created']);
			unset($variantCollectible['Collectible']['modified']);

			debug($variantCollectible);

			$this -> Collectible -> set($variantCollectible);
			if($this -> Collectible -> validates()) {

			} else {
				$this -> log('User ' . $userId . ' failed at adding collectible variant ', 'error');
				$validCollectible = false;
				debug($this -> Collectible -> invalidFields());
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');

			}

			if($validCollectible) {
				$this -> Session -> write('preSaveCollectible', $variantCollectible);
				$this -> redirect( array('action' => 'addImage'));
				return ;
				//$this -> redirect( array('action' => 'review'));
			} else {
				$manufactureData = $this -> Collectible -> Manufacture -> getManufactureList();
				$this -> set('exclusive_manufactures', $manufactureData);
				$this -> render('addCollectible');
				return ;
			}

		} else if(!empty($this -> params['named']['edit'])) {
			$variantCollectible = $this -> Session -> read('preSaveCollectible');
			//Set the current collectible we are editing, so it will show up on the page inputs
			$this -> data = $variantCollectible;
			//Grab the collectible from the session and store it on the request for display
			$baseCollectible = $this -> Session -> read('variant.base-collectible');
			//do this for display purposes
			$this -> set('collectible', $baseCollectible);
			$this -> set('edit', true);
			$this -> render('addCollectible');
			$manufactureData = $this -> Collectible -> Manufacture -> getManufactureList();
			$this -> set('exclusive_manufactures', $manufactureData);
			return ;
		} else {
			$baseCollectible = $this -> Session -> read('variant.base-collectible');
			$this -> data = $baseCollectible;
			$manufactureData = $this -> Collectible -> Manufacture -> getManufactureList();
			$this -> set('exclusive_manufactures', $manufactureData);
		}

		$this -> render('addCollectible');
	}

	function addImage() {
		$this -> checkLogIn();

		if(!empty($this -> data)) {
			debug($this -> data['remove']);
			if($this -> data['remove'] === 'true') {
				debug($this -> data);
				$collectible = $this -> Session -> read('preSaveCollectible');
				if(isset($collectible['Upload']) && !empty($collectible['Upload']['id'])) {
					$imageId = $collectible['Upload']['id'];
					if($this -> Collectible -> Upload -> delete($imageId)) {
						unset($collectible['Upload']);
						$this -> Session -> write('preSaveCollectible', $collectible);
					} else {

					}
				} else {

				}
			} else {
				//If they submit and we already added a collectible, think back button, then just redisplay the
				//page and show the image.  They can then choose to edit the image if they want
				$collectible = $this -> Session -> read('preSaveCollectible');
				if(!isset($collectible['Upload'])) {
					if($this -> Collectible -> Upload -> isValidUpload($this -> data)) {
						if($this -> Collectible -> Upload -> saveAll($this -> data['Upload'])) {
							//We have to save the upload right away because of how these work.  So lets save it
							//Then lets grab the id of the upload and return the data of the uploaded one and store
							//it in our saving object.  This will allow us to display the details to the user in the
							//review and confirm process.
							$uploadId = $this -> Collectible -> Upload -> id;
							$upload = $this -> Collectible -> Upload -> find('first', array('conditions' => array('Upload.id' => $uploadId), 'contain' => false));
							debug($upload);
							$collectible = $this -> Session -> read('preSaveCollectible');
							$collectible['Upload'] = $upload['Upload'];
							$this -> Session -> write('preSaveCollectible', $collectible);
							//$this -> Session -> write('uploadId', $uploadId);

							$this -> redirect( array('action' => 'review'));
						} else {
							debug($this -> Collectible -> Upload -> validationErrors);
							$this -> Session -> setFlash(__('Oops! There was an issue uploading your photo.', true), null, null, 'error');
							$validCollectible = false;
						}
					} else {
						$this -> Collectible -> Upload -> validationErrors['0']['file'] = 'Image is required.';
						debug($this -> Collectible -> Upload -> invalidFields());
						$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
						$validCollectible = false;
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
	}

	function review() {
		$this -> checkLogIn();
		//remove this cause we do not need it
		$collectible = $this -> Session -> read('preSaveCollectible');
		if(!is_null($collectible)) {
			//Lets retrieve some data for display purposes
			//TODO this may be redundant...should we save off later?
			$manufacture = $this -> Collectible -> Manufacture -> find('first', array('conditions' => array('Manufacture.id' => $collectible['Collectible']['manufacture_id']), 'fields' => array('Manufacture.title', 'Manufacture.url'), 'contain' => false));
			$collectible['Manufacture'] = $manufacture['Manufacture'];

			$collectibleType = $this -> Collectible -> Collectibletype -> find('first', array('conditions' => array('Collectibletype.id' => $collectible['Collectible']['collectibletype_id']), 'fields' => array('Collectibletype.name'), 'contain' => false));
			$collectible['Collectibletype'] = $collectibleType['Collectibletype'];

			$license = $this -> Collectible -> License -> find('first', array('conditions' => array('License.id' => $collectible['Collectible']['license_id']), 'fields' => array('License.name'), 'contain' => false));
			$collectible['License'] = $license['License'];

			$scale = $this -> Collectible -> Scale -> find('first', array('conditions' => array('Scale.id' => $collectible['Collectible']['scale_id']), 'fields' => array('Scale.scale'), 'contain' => false));
			$collectible['Scale'] = $scale['Scale'];

			debug($collectible);
			$this -> set('collectible', $collectible);
			$this -> set('confirmUrl', '/collectibles/confirm');
		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect( array('action' => 'addSelectType'));
		}

	}

	function confirm() {
		$this -> checkLogIn();
		$newCollectible = $this -> Session -> read('preSaveCollectible');
		if(!is_null($newCollectible)) {
			$saveCollectible['Approval'] = $newCollectible['Approval'];
			$saveCollectible['Collectible'] = $newCollectible['Collectible'];
			if(isset($newCollectible['AttributesCollectible'])) {
				$saveCollectible['AttributesCollectible'] = $newCollectible['AttributesCollectible'];
			}
			$this -> Collectible -> create();
			if($this -> Collectible -> saveAll($saveCollectible, array('validate' => true))) {
				$id = $this -> Collectible -> id;
				$collectible = $this -> Collectible -> findById($id);
				$approvalId = $collectible['Collectible']['approval_id'];
				$this -> loadModel('Approval');
				$this -> Approval -> id = $approvalId;
				/* Since they confirmed, now set to pending = 1.  I really don't like how
				 this is setup right now but it works because of the image thing.
				 A 1 means that this collectible needs to be approved by an admin first */
				$pendingState = '1';
				if($this -> isUserAdmin() || Configure::read('Settings.Approval.auto-approve') == 'true') {
					$pendingState = '0';
				}
				$this -> Approval -> saveField('state', $pendingState);
				if(isset($newCollectible['Upload'])) {
					$this -> Collectible -> Upload -> id = $newCollectible['Upload']['id'];
					$this -> Collectible -> Upload -> saveField('collectible_id', $id);
				}

				$collectible = $this -> Collectible -> findById($id);
				debug($collectible);
				$this -> set('collectible', $collectible);
				$this -> Session -> delete('preSaveCollectible');
				$this -> Session -> delete('uploadId');
			} else {
				debug($this -> Collectible -> validationErrors);
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				$this -> redirect( array('action' => 'review'));
			}
		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect( array('action' => 'addSelectType'));
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
		$this -> redirect( array('action' => 'addSelectType'));

	}

	/*
	 * I think I will have a "collectibles_edit" table and potentially going to have to have edit tables for the other related tables.
	 * All edits will be put to the collectibles_edit table as to not interfer with the main tables, once an edit has been approved then it
	 * will push it out to the main tables.  We will reuse the approval table but the approval id on the edit collectible will be for the edit...
	 *
	 * Once the edit is approved we will update the fields of the collectible that were being changed, this will keep the history on the rev table...however
	 * how do we maintain who did the edits? Does each entry in the collectible_rev get its own approval id?
	 * 
	 * Notes from 6/21/11
	 * 	- I need either to have the user id on the collectible that created it, and then the approval id will be the most current version that will keep the 
	 * edit trail.  That way I always know who created it and I also know who did the lastest edits.  If I want to go back and look at history, I would do compares against
	 * the rev table and each approval id would be the id of the person who did the edit, although do it make more sense to have an edit table or edit user id?  Since that isn't
	 * the purpose of approval?
	 */
	function edit($id =null) {
		$this -> checkLogIn();
		if(!$id && empty($this -> data) && !empty($this -> params['named']['edit'])) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			//TODO go somewhere
			//$this -> redirect( array('action' => 'index'));
		}
		$username = $this -> getUsername();
		//Check if it is a variant or not
		$this -> Session -> write('edit.collectible.mode.collectible', true);
		$this -> Session -> delete('edit.collectible.mode.variant');
		$this -> Session -> delete('add.collectible.mode.collectible');
		$this -> Session -> delete('add.collectible.mode.variant');
		$this -> set('collectible_title', __('Edit Collectible', true));

		if(!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data);
			$this -> Collectible -> set($this -> data);
			$validCollectible = true;
			if($this -> Collectible -> validates()) {

			} else {
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				$validCollectible = false;
			}

			if($validCollectible) {
				$this -> Session -> write('preSaveCollectible', $this -> data);
				$this -> redirect( array('action' => 'editReview'));
			} else {
				$manufactureData = $this -> Collectible -> Manufacture -> getManufactureData($this -> data['Collectible']['manufacture_id']);
				$this -> set('manufactures', $manufactureData['manufactures']);
				$this -> set('licenses', $manufactureData['licenses']);

				if(isset($newCollectible['Collectible']['series_id'])) {
					$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($this -> data['Collectible']['manufacture_id'], $this -> data['Collectible']['license_id']);
					$this -> set('series', $series);
				}

				// $this -> set('series', $manufactureData['series']);
				$this -> set('collectibletypes', $manufactureData['collectibletypes']);
				$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
				$this -> set(compact('scales'));
			}
		} else if(empty($this -> data)) {
			//TODO 6/19/11 - finish all of this logic

			if(!empty($this -> params['named']['edit'])) {
				$collectible = $this -> Session -> read('preSaveCollectible');
				$this -> data = $collectible;
				$this -> set('edit', true);
			} else {
				$collectible = $this -> Collectible -> read(null, $id);
				$this -> data = $collectible;
			}

			$manufactureData = $this -> Collectible -> Manufacture -> getManufactureData($collectible['Collectible']['manufacture_id']);
			$this -> set('manufactures', $manufactureData['manufactures']);
			$this -> set('licenses', $manufactureData['licenses']);
			$this -> set('collectibletypes', $manufactureData['collectibletypes']);

			if(isset($collectible['Collectible']['series_id'])) {
				$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($collectible['Collectible']['manufacture_id'], $collectible['Collectible']['license_id']);
				$this -> set('series', $series);
			}

			$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
			$this -> set(compact('scales'));

			$this -> render('addCollectible');
			return ;
		} else {
			//do somethinf
		}

	}

	function editReview() {
		$this -> checkLogIn();
		//remove this cause we do not need it
		$collectible = $this -> Session -> read('preSaveCollectible');
		if(!is_null($collectible)) {
			//Lets retrieve some data for display purposes
			//TODO this may be redundant...should we save off later?
			$manufacture = $this -> Collectible -> Manufacture -> find('first', array('conditions' => array('Manufacture.id' => $collectible['Collectible']['manufacture_id']), 'fields' => array('Manufacture.title', 'Manufacture.url'), 'contain' => false));
			$collectible['Manufacture'] = $manufacture['Manufacture'];

			$collectibleType = $this -> Collectible -> Collectibletype -> find('first', array('conditions' => array('Collectibletype.id' => $collectible['Collectible']['collectibletype_id']), 'fields' => array('Collectibletype.name'), 'contain' => false));
			$collectible['Collectibletype'] = $collectibleType['Collectibletype'];

			$license = $this -> Collectible -> License -> find('first', array('conditions' => array('License.id' => $collectible['Collectible']['license_id']), 'fields' => array('License.name'), 'contain' => false));
			$collectible['License'] = $license['License'];

			$scale = $this -> Collectible -> Scale -> find('first', array('conditions' => array('Scale.id' => $collectible['Collectible']['scale_id']), 'fields' => array('Scale.scale'), 'contain' => false));
			$collectible['Scale'] = $scale['Scale'];

			debug($collectible);
			$this -> set('collectible', $collectible);
			
			$this -> set('confirmUrl', '/collectibles/editConfirm');
			$this -> render('review');
			return ;
		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect( array('action' => 'addSelectType'));
		}
	}

	function editConfirm() {
		$this -> checkLogIn();
		$newCollectible = $this -> Session -> read('preSaveCollectible');
		if(!is_null($newCollectible)) {
			$saveCollectible['Approval'] = $newCollectible['Approval'];
			$saveCollectible['Collectible'] = $newCollectible['Collectible'];
			if(isset($newCollectible['AttributesCollectible'])) {
				$saveCollectible['AttributesCollectible'] = $newCollectible['AttributesCollectible'];
			}
			$this -> CollectiblesEdit -> create();
			if($this -> CollectiblesEdit -> saveAll($saveCollectible, array('validate' => false))) {
				$id = $this -> Collectible -> id;
				$collectible = $this -> Collectible -> findById($id);
				$approvalId = $collectible['Collectible']['approval_id'];
				$this -> loadModel('Approval');
				$this -> Approval -> id = $approvalId;
				/* Since they confirmed, now set to pending = 1.  I really don't like how
				 this is setup right now but it works because of the image thing.
				 A 1 means that this collectible needs to be approved by an admin first */
				$pendingState = '1';
				if($this -> isUserAdmin() || Configure::read('Settings.Approval.auto-approve') == 'true') {
					$pendingState = '0';
				}
				$this -> Approval -> saveField('state', $pendingState);
				if(isset($newCollectible['Upload'])) {
					$this -> Collectible -> Upload -> id = $newCollectible['Upload']['id'];
					$this -> Collectible -> Upload -> saveField('collectible_id', $id);
				}

				$collectible = $this -> Collectible -> findById($id);
				debug($collectible);
				$this -> set('collectible', $collectible);
				$this -> Session -> delete('preSaveCollectible');
				$this -> Session -> delete('uploadId');
			} else {
				debug($this -> Collectible -> validationErrors);
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				$this -> redirect( array('action' => 'review'));
			}
		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect( array('action' => 'addSelectType'));
		}

	}

	function delete($id =null) {
		$this -> checkLogIn();
		$username = $this -> getUsername();
		$aco = $this -> params['controller'];
		if($this -> Acl -> check($username, 'collectibles', 'delete')) {
			if(!$id) {
				$this -> Session -> setFlash(__('Invalid id for collectible', true), null, null, 'error');
				$this -> redirect( array('action' => 'index'));
			}

			if($this -> Collectible -> delete($id)) {
				$this -> Session -> setFlash(__('Collectible deleted', true), null, null, 'success');
				$this -> redirect( array('action' => 'index'));
			}
		}
		$this -> Session -> setFlash(__('Collectible was not deleted', true), null, null, 'error');
		$this -> redirect( array('action' => 'index'));
	}

	function search() {
		//Ok so the core search checks post data, but this is being passed in via get data so hack for now
		//Should update this if we want to pass multiple paramaters in
		if(isset($this -> params['url']['q'])) {
			$this -> data['Search'] = array();
			$this -> data['Search']['search'] = $this -> params['url']['q'];
		} else if(isset($this -> params['url']['m'])) {
			$this -> data['Search'] = array();
			//find all of this license
			$this -> data['Search']['search'] = '';
			$this -> data['Search']['Manufacture'] = array();
			$this -> data['Search']['Manufacture']['Filter'] = array();
			$this -> data['Search']['Manufacture']['Filter'][$this -> params['url']['m']] = 1;
		} else if(isset($this -> params['url']['l'])) {
			$this -> data['Search'] = array();
			//find all of this license
			$this -> data['Search']['search'] = '';
			$this -> data['Search']['License'] = array();
			$this -> data['Search']['License']['Filter'] = array();
			$this -> data['Search']['License']['Filter'][$this -> params['url']['l']] = 1;
		}
		$this -> searchCollectible();
	}

}
?>

