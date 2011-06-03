<?php
App::import('Sanitize');
class CollectiblesController extends AppController {

	var $name = 'Collectibles';

	//var $components = array('CollectibleDisplayCondition');

	var $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload');

	var $components = array('RequestHandler');

	var $actsAs = array('Searchable.Searchable');

	/**
	 * TODO as of 5/9/11 this method is currently not being used and I do not think works right now.
	 */
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
			$this -> Session -> delete('preSaveCollectible');
			$this -> Session -> delete('uploadId');
			$this -> Session -> delete('add.collectible.mode.collectible');
			$this -> Session -> delete('add.collectible.mode.variant');
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
			$this -> Session -> write ('add.collectible.mode.collectible', true);
			$this -> Session -> delete('add.collectible.mode.variant');
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
					$this -> paginate = array("conditions" => array($conditions), "contain" => array('Manufacture', 'Collectibletype', 'Upload', 'Approval'), 'limit' => 1);
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
						//not sure this matters because of the exit, but just in case
						debug($existingCollectibles);
						$this -> set('existingCollectibles', $existingCollectibles);

						$this -> render('existingCollectibles');
						return ;
						//does render exit?
						//exit();
					} else {
						$this -> redirect( array('action' => 'addImage'));

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
		$this -> Session -> write ('add.collectible.mode.variant', true);
		$this -> Session -> delete('add.collectible.mode.collectible');
		//If edit do not do this again
		if($id && empty($this -> params['named']['edit'])) {

			$this -> set('collectible', $this -> Collectible -> read(null, $id));
			$this -> Session -> write('variant-add-id', $id);
		}

		if(!empty($this -> data)) {
			debug($this -> data);
			$this -> data['Collectible'] = Sanitize::clean($this -> data['Collectible'], array('encode' => false));

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
			$id = $this -> Session -> read('variant-add-id');
			//read that collecrible
			$collectible = $this -> Collectible -> read(null, $id);
			//do this for display purposes
			$this -> set('collectible', $collectible);

			$newCollectible = array();

			$newCollectible['Approval'] = $this -> data['Approval'];
			//Set the base of that collectible, we will override with what the user
			//entered.
			$newCollectible['Collectible'] = $collectible['Collectible'];
			//$newCollectible['Upload'] = $this -> data['Upload'];
			$newCollectible['Collectible']['id'] = '';
			$newCollectible['Collectible']['approval_id'] = '';

			if(isset($this -> data['AttributesCollectible'])) {
				$newCollectible['AttributesCollectible'] = $this -> data['AttributesCollectible'];
			}

			//These are the things that variant can override
			$newCollectible['Collectible']['name'] = $this -> data['Collectible']['name'];
			$newCollectible['Collectible']['exclusive'] = $this -> data['Collectible']['exclusive'];
			$newCollectible['Collectible']['edition_size'] = $this -> data['Collectible']['edition_size'];
			$newCollectible['Collectible']['url'] = $this -> data['Collectible']['url'];
			$newCollectible['Collectible']['variant'] = '1';
			//we are saving what collectible this variant came from.
			$newCollectible['Collectible']['variant_collectible_id'] = $id;
			//Null thee out so they will update, probably should go in the model
			unset($newCollectible['Collectible']['created']);
			unset($newCollectible['Collectible']['modified']);

			debug($newCollectible);

			$this -> Collectible -> set($newCollectible);
			if($this -> Collectible -> validates()) {


			} else {
				$this -> log('User ' . $userId . ' failed at adding collectible variant ', 'error');
				$validCollectible = false;
				debug($this -> Collectible -> invalidFields());
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');

			}

			if($validCollectible) {
				$this -> Session -> write('preSaveCollectible', $newCollectible);
				$this -> redirect( array('action' => 'addImage'));
				//$this -> redirect( array('action' => 'review'));
			} else {

			}

		} else if(!empty($this -> params['named']['edit'])) {
			$collectible = $this -> Session -> read('preSaveCollectible');
			$this -> data = $collectible;
			//TODO this is way redundant and should not need to be done again...instead pull from session
			$id = $this -> Session -> read('variant-add-id');
			//read that collecrible
			$collectible = $this -> Collectible -> read(null, $id);
			//do this for display purposes
			$this -> set('collectible', $collectible);
			$this -> set('edit', true);
		}
	}

	function addImage() {
		$this -> checkLogIn();
		
		if(!empty($this -> data)) {
			debug($this->data['remove']);
			if($this->data['remove'] === 'true') {
				debug($this->data);
				$collectible = $this -> Session -> read('preSaveCollectible');
				if(isset($collectible['Upload']) && !empty($collectible['Upload']['id'])){
					$imageId = $collectible['Upload']['id'];
					if ($this -> Collectible -> Upload -> delete($imageId)) {
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

