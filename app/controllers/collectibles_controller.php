<?php
App::import('Sanitize');
class CollectiblesController extends AppController {

	var $name = 'Collectibles';

	//var $components = array('CollectibleDisplayCondition');

	var $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload');

	var $components = array('RequestHandler');

	var $actsAs = array('Searchable.Searchable');

	function index() {
		$this -> data = Sanitize::clean($this -> data, array('encode' => false));

		$options = array();
		if(!empty($this -> data['search'])) {
			// $options = array("MATCH(Collectible.name) AGAINST('{$this->data['search']}')");

			debug($this -> data);
			$this -> paginate = array("conditions" => array("MATCH(Collectible.name) AGAINST('{$this->data['search']}' IN BOOLEAN MODE)"), "contain" => array('Manufacture', 'Collectibletype', 'Upload', 'Approval'));
		} else {
			$this -> paginate = array("contain" => array('Manufacture', 'Collectibletype', 'Upload', 'Approval'));
			//  $params = array();
		}
		//$options['contains'] = array('Collectible' => array ( 'Manufacture','Collectibletype','Upload'));
		//this brings back variants
		//$this->Collectible->recursive = 1;
		//TODO update this so it limits what is returned
		$collectilbes = $this -> paginate('Collectible');
		debug($collectilbes);
		$this -> set('collectibles', $collectilbes);

		$username = $this -> getUsername();
		if($this -> Acl -> check($username, 'collectibles', 'update')) {
			$this -> set('showUpdate', TRUE);
		} else {
			$this -> set('showUpdate', FALSE);
		}

	}

	function view($id =null) {
		if(!$id) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect( array('action' => 'index'));
		}
		$this -> set('collectible', $this -> Collectible -> findById($id));
		$count = $this -> Collectible -> getNumberofCollectiblesInStash($id);
		$this -> set('collectibleCount', $count);
	}

	/**
	 * I think there is an issue with the file upload code and saving the image
	 * if I redirect to another page. So for now I am doing an inital save
	 * and setting the pending to 2.  Then on the actual confirm I am changing
	 * it to 1.  2s will be filtered and should eventually be deleted because
	 * they are not committed changes.  Really lame but works for now.
	 */
	function addSelectType() {
		if($this -> isLoggedIn()) {
			//check to see if there is data submitted
			if(!empty($this -> data)) {
				if($this -> data['Collectible']['addType'] == 'C') {
					debug($this -> data);
					$this -> redirect( array('action' => 'addCollectibleManufacture'));
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

	function addCollectibleManufacture() {
		if($this -> isLoggedIn()) {
			//check to see if there is data submitted
			if(!empty($this -> data)) {
				debug($this -> data);
				$manufactureId = $this -> data['Collectible']['manufacture_id'];
				$this -> Session -> write('manufactureId', $manufactureId);
				$licenses = $this -> Collectible -> Manufacture -> LicensesManufacture -> getLicensesByManufactureId($manufactureId);

				//If this manufacture has no licenses well then just carry on to adding the collectible.
				if(empty($licenses)) {
					$this -> redirect( array('action' => 'addCollectible'));
					exit();
				} else {
					//no sense in retrieving this twice.
					$this -> Session -> write('licenses', $licenses);
					$this -> redirect( array('action' => 'addCollectibleLicense'));
					exit();
				}
			} else {
				$manufactures = $this -> Collectible -> Manufacture -> find('list');
				$this -> set(compact('manufactures'));
			}
		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	function addCollectibleLicense() {
		if($this -> isLoggedIn()) {
			if($this -> Session -> read('manufactureId') != null) {
				//check to see if there is data submitted
				if(!empty($this -> data)) {
					debug($this -> data);
					$licenseId = $this -> data['Collectible']['license_id'];
					$this -> Session -> write('licenseId', $licenseId);
					$this -> redirect( array('action' => 'addCollectible'));
					exit();
				} else {
					$licenses = $this -> Session -> read('licenses');
					$this -> set(compact('licenses'));
				}
			} else {
				$this -> redirect( array('action' => 'addCollectibleManufacture'));
				$this -> Session -> setFlash(__('You need to select a manufacture.', true), null, null, 'error');
				exit();
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
			$bypass = false;
			$manufactureId = $this -> Session -> read('manufactureId');
			$licenseId = $this -> Session -> read('licenseId');
			//check to see if there is data submitted
			$this -> Session -> delete('lastSaveApprovalId');
			$this -> Session -> delete('collectible');
			//Check if we are bypass an already existing add.
			// if(!empty($this -> params['named']['bypass'])) {
			// if(strcasecmp($this -> params['named']['bypass'], "yes") == 0) {
			// //ok so we are bypassing, pull $this->data out of session and store to $this-data and let it pass through
			// $newCollectible = $this -> Session -> read('preSaveCollectible');
			// $bypass = true;
			// }
			//
			// } else if(!empty($this -> data)) {
			// //If we are not bypassing but something was submitted, set that data
			// $newCollectible = $this -> data;
			// }

			if(!empty($this -> data)) {
				$newCollectible = $this -> data;
				//TODO having problems with this and uploading files
				//$this->data = Sanitize::clean($this->data, array('encode' => false));
				//create new collectible
				//$this->Collectible->Approval->create();
				debug($this -> data);
				//Now check if we are bypassing, if we are not bypassing then we need to
				//validate the data that was passed in
				$save = true;
				debug($bypass);
				debug($newCollectible);
				// if(!$bypass) {
				//Doing this for now because I think the way I am doing uploads is a bit different so error handling doesn't seem to be working
				//or I am not doing this correctly.
				if($this -> Collectible -> Upload -> isValidUpload($newCollectible)) {
					//need save all to save all data to associated models

					//set pending to 2...this really needs to check if user is admin first TODO
					$newCollectible['Approval']['state'] = 2;
					$userId = $this -> getUserId();
					//set the id of the user who is adding this collectible
					$newCollectible['Approval']['user_id'] = $userId;
					//$newCollectible['Approval']['date_added'] = date("Y-m-d H:i:s", time());
					//set the man id of this collectible
					$newCollectible['Collectible']['manufacture_id'] = $manufactureId;
					//set the license id of this collectible
					$newCollectible['Collectible']['license_id'] = $licenseId;
					$newCollectible['Collectible']['exclusive'] = 0;
					$newCollectible['Collectible']['variant'] = 0;
					//$newCollectible['Collectible']['approval_id'] = '1';

					$this -> Collectible -> set($newCollectible);

					if($this -> Collectible -> validates()) {

						if($this -> Collectible -> Upload -> saveAll($newCollectible['Upload'], array('validate' => false))) {
							//We have to save the upload right away because of how these work.  So lets save it
							//Then lets grab the id of the upload and return the data of the uploaded one and store
							//it in our saving object.  This will allow us to display the details to the user in the
							//review and confirm process.
							$uploadId = $this -> Collectible -> Upload -> id;
							$upload = $this -> Collectible -> Upload -> find('first',  array('conditions' => array('Upload.id' => $uploadId), 'contain' => false));
							$newCollectible['Upload'] = $upload['Upload'];
							$this -> Session -> write('preSaveCollectible', $newCollectible);
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
							$this -> paginate = array("conditions" => array($conditions), "contain" => array('Manufacture', 'Collectibletype', 'Upload', 'Approval'), 'limit' => 1);
							$existingCollectibles = $this -> paginate('Collectible');

							//If the size is greater than zero when we have a potential
							//collectible that is similar.
							if(count($existingCollectibles) > 0) {
								//not sure this matters because of the exit, but just in case
								$save = false;
								debug($existingCollectibles);
								$this -> set('existingCollectibles', $existingCollectibles);

								$this -> render('existingCollectibles');
								//does render exit?
								//exit();
							} else {
								$this -> redirect( array('action' => 'review'));
							}
						} else {
							$this -> Session -> setFlash(__('Oops! There was an issue uploading your photo.', true), null, null, 'error');
						}
					} else {
						//$save = false;
						debug($this -> Collectible -> validationErrors);
						$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					}
				} else {
					//$save = false;
					$this -> Collectible -> Upload -> validationErrors['0']['file'] = 'Image is required.';
					debug($this -> Collectible -> Upload -> invalidFields());
					$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				}
				// }
			}

			$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> LicensesManufacturesSeries -> getSeriesByLicenseManufactureId(5);
			$this -> set(compact('licenses'));
			$manufactureName = $this -> Collectible -> Manufacture -> getManufactureNameById($manufactureId);
			$this -> set(compact('manufactureName'));
			$collectibletypes = $this -> Collectible -> Manufacture -> CollectibletypesManufacture -> getCollectibleTypeByManufactureId($manufactureId);
			$this -> set(compact('collectibletypes'));

		} else {
			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		}
	}

	function addVariantSelectCollectible() {
		if($this -> isLoggedIn()) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			$this -> searchCollectible( array('Collectible.variant' => '0'));
			/*             if(!empty($this -> data['search'])) {
			 $this -> paginate = array("conditions" => array("MATCH(Collectible.name) AGAINST('{$this->data['search']}')", 'Approval.state' => '0', 'Collectible.variant' => '0'), "contain" => array('Manufacture', 'Collectibletype', 'Upload', 'Approval'));
			 } else {
			 $this -> paginate = array("conditions" => array('Approval.state' => '0', 'Collectible.variant' => '0'), "contain" => array('Manufacture', 'Collectibletype', 'Upload', 'Approval'));
			 }
			 $this -> set('collectibles', $this -> paginate('Collectible'));*/
		}
	}

	function addVariant($id =null) {
		$this -> checkLogIn();

		if($id) {
			$this -> set('collectible', $this -> Collectible -> read(null, $id));
			$this -> Session -> write('variant-add-id', $id);
		} else if(!empty($this -> data)) {
			debug($this -> data);
			// $pendingState = '2';
			// if($this -> isUserAdmin() || Configure::read('Settings.Approval.auto-approve') == 'true') {
				// $pendingState = '0';
			// }
			
			if($this -> Collectible -> Upload -> isValidUpload($this -> data)) {
				$this -> data['Approval']['state'] = 2;
				$userId = $this -> getUserId();
				//set the id of the user who is adding this collectible
				$this -> data['Approval']['user_id'] = $userId;
				// -> data['Approval']['date_added'] = date("Y-m-d H:i:s", time());
	
				//Grab the id of the collectible this is a variant for
				$id = $this -> Session -> read('variant-add-id');
				//read that collecrible
				$collectible = $this -> Collectible -> read(null, $id);
				//why?
				$this -> set('collectible', $collectible);
	
				$saveData = array();
	
				$saveData['Approval'] = $this -> data['Approval'];
				//Set the base of that collectible, we will override with what the user
				//entered.
				$saveData['Collectible'] = $collectible['Collectible'];
				$saveData['Upload'] = $this -> data['Upload'];
				$saveData['Collectible']['id'] = '';
				$saveData['Collectible']['approval_id'] = '';
	
				if(isset($this -> data['AttributesCollectible'])) {
					$saveData['AttributesCollectible'] = $this -> data['AttributesCollectible'];
				}
	
				$saveData['Collectible']['exclusive'] = $this -> data['Collectible']['exclusive'];
				$saveData['Collectible']['edition_size'] = $this -> data['Collectible']['edition_size'];
				$saveData['Collectible']['url'] = $this -> data['Collectible']['url'];
				$saveData['Collectible']['variant'] = '1';
				//we are saving what collectible this variant came from.
				$saveData['Collectible']['variant_collectible_id'] = $id;
				//Null thee out so they will update, probably should go in the model
				unset($saveData['Collectible']['created']);
				unset($saveData['Collectible']['modified']);
	
				debug($saveData);		
				
				$this -> Collectible -> set($saveData);

				if($this -> Collectible -> validates()) {
					if($this -> Collectible -> Upload -> saveAll($saveData['Upload'], array('validate' => false))) {
						$uploadId = $this -> Collectible -> Upload -> id;
						$upload = $this -> Collectible -> Upload -> find('first',  array('conditions' => array('Upload.id' => $uploadId), 'contain' => false));
						$saveData['Upload'] = $upload['Upload'];
						$this -> Session -> write('preSaveCollectible', $saveData);	
						
						
						$this -> redirect( array('action' => 'review'));
						
					}	
				}		
			
				
			} else {
				$this -> Collectible -> Upload -> validationErrors['0']['file'] = 'Image is required.';
				debug($this -> Collectible -> Upload -> invalidFields());
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			}
			
			
			

			// //Doing this for now because I think the way I am doing uploads is a bit different so error handling doesn't seem to be working
			// //or I am not doing this correct.  For now it is forcing Upload to show errors first.  I could validate upload, then run
			// // validation on collectible so I get both errors at once.
			// if($this -> Collectible -> Upload -> isValidUpload($saveData)) {
				// $this -> Collectible -> create();
				// if($this -> Collectible -> saveAll($saveData)) {
					// $collectible = $this -> Collectible -> findById($this -> Collectible -> id);
					// $approvalId = $collectible['Collectible']['approval_id'];
					// $this -> Session -> write('lastSaveApprovalId', $approvalId);
					// $this -> Session -> write('collectible', $collectible);
					// $this -> set($this -> data);
					// $this -> redirect( array('action' => 'review'));
				// }
			// } else {
				// $this -> Collectible -> Upload -> validationErrors['0']['file'] = 'Image is required.';
				// debug($this -> Collectible -> Upload -> invalidFields());
				// $this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			// }
		}
	}

	function review() {
		$this -> checkLogIn();
		//remove this cause we do not need it
		$collectible = $this -> Session -> read('preSaveCollectible');

		if(!is_null($collectible)) {
			//Lets retrieve some data for display purposes
			$manufacture = $this -> Collectible -> Manufacture -> find('first', array('conditions' => array('Manufacture.id' => $collectible['Collectible']['manufacture_id']), 'fields' => array('Manufacture.title', 'Manufacture.url'), 'contain' => false));
			$collectible['Manufacture'] = $manufacture['Manufacture'];

			$collectibleType = $this -> Collectible -> Collectibletype -> find('first', array('conditions' => array('Collectibletype.id' => $collectible['Collectible']['collectibletype_id']), 'fields' => array('Collectibletype.name'), 'contain' => false));
			$collectible['Collectibletype'] = $collectibleType['Collectibletype'];

			$license = $this -> Collectible -> License -> find('first', array('conditions' => array('License.id' => $collectible['Collectible']['license_id']), 'fields' => array('License.name'), 'contain' => false));
			$collectible['License'] = $license['License'];

			debug($collectible);
			$this -> set('collectible', $collectible);
		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect( array('action' => 'addSelectType'));
		}

	}

	function confirm() {
		$this -> checkLogIn();
		$newCollectible = $this -> Session -> read('preSaveCollectible');
		$saveCollectible['Approval'] = $newCollectible['Approval'];
		$saveCollectible['Collectible'] = $newCollectible['Collectible'];
		if(isset($newCollectible['AttributesCollectible'])) {
			$saveCollectible['AttributesCollectible'] = $newCollectible['AttributesCollectible'];
		}
		$this -> Collectible -> create();
		if($this -> Collectible -> saveAll($saveCollectible, array('validate' => false))) {
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
			$this -> Collectible -> Upload -> id = $newCollectible['Upload']['id'];
			$this -> Collectible -> Upload -> saveField('collectible_id', $id);

			$collectible = $this -> Collectible -> findById($id);
			debug($collectible);
			$this -> set('collectible', $collectible);
			$this -> Session -> delete('preSaveCollectible');
		} else {
			debug($this -> Collectible -> validationErrors);
			$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			$this -> redirect( array('action' => 'review'));
		}
	}

	function edit($id =null) {
		$this -> checkLogIn();
		if(!$id && empty($this -> data)) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			$this -> redirect( array('action' => 'index'));
		}
		$username = $this -> getUsername();

		if($this -> Acl -> check($username, 'collectibles', 'update')) {
			if(!empty($this -> data)) {
				if($this -> Collectible -> saveAll($this -> data)) {
					$this -> Session -> setFlash(__('The collectible has been saved', true), null, null, 'success');
					$this -> redirect( array('action' => 'index'));
				} else {
					$this -> Session -> setFlash(__('The collectible could not be saved. Please, try again.', true), null, null, 'error');
				}
			}

			if(empty($this -> data)) {
				$this -> data = $this -> Collectible -> read(null, $id);
			}
		} else {
			$this -> Session -> setFlash(__('The collectible could not be saved. Please, try again.', true), null, null, 'error');
			$this -> redirect( array('action' => 'index'));
		}

		$manufactures = $this -> Collectible -> Manufacture -> find('list');
		$this -> set(compact('manufactures'));
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

}
?>

