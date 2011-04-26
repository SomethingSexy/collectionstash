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
					exit();

				} else if($this -> data['Collectible']['addType'] == 'V') {
					debug($this -> data);
					$this -> redirect( array('action' => 'addVariantSelectCollectible', 'initial' => 'yes'));
					exit();

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

	function addCollectible() {
		//check if user is logged in
		if($this -> isLoggedIn()) {
			$manufactureId = $this -> Session -> read('manufactureId');
			$licenseId = $this -> Session -> read('licenseId');
			//check to see if there is data submitted
			if(!empty($this -> data)) {
				//TODO having problems with this and uploading files
				//$this->data = Sanitize::clean($this->data, array('encode' => false));
				//create new collectible
				//$this->Collectible->Approval->create();
				$this -> Collectible -> create();
				//set pending to 2...this really needs to check if user is admin first TODO
				$this -> data['Approval']['state'] = 2;
				$userId = $this -> getUserId();
				//set the id of the user who is adding this collectible
				$this -> data['Approval']['user_id'] = $userId;
				$this -> data['Approval']['date_added'] = date("Y-m-d H:i:s", time());
				//set the man id of this collectible
				$this -> data['Collectible']['manufacture_id'] = $manufactureId;
				//set the license id of this collectible
				$this -> data['Collectible']['license_id'] = $licenseId;
				$this -> data['Collectible']['approval_id'] = '1';
				debug($this -> data);

				//Doing this for now because I think the way I am doing uploads is a bit different so error handling doesn't seem to be working
				//or I am not doing this correctly.
				if($this -> Collectible -> Upload -> isValidUpload($this -> data)) {
					//need save all to save all data to associated models
					if($this -> Collectible -> saveAll($this -> data)) {
						$id = $this -> Collectible -> id;
						$collectible = $this -> Collectible -> findById($id);
						$approvalId = $collectible['Collectible']['approval_id'];
						$this -> Session -> write('lastSaveApprovalId', $approvalId);
						$this -> Session -> write('collectible', $collectible);
						$this -> set($this -> data);
						//$this -> redirect( array('action' => 'review'));
					} else {
						debug($this -> Collectible -> validationErrors);
						$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					}
				} else {
					$this -> Collectible -> Upload -> validationErrors['0']['file'] = 'Image is required.';
					debug($this -> Collectible -> Upload -> invalidFields());
					$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				}
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

		//         if($this -> isLoggedIn()) {*/
		if($id) {
			$this -> set('collectible', $this -> Collectible -> read(null, $id));
			$this -> Session -> write('variant-add-id', $id);
		} else if(!empty($this -> data)) {
			debug($this -> data);
			$pendingState = '2';
			if($this -> isUserAdmin() || Configure::read('Settings.Approval.auto-approve') == 'true') {
				$pendingState = '0';
			}
			$this -> data['Approval']['state'] = $pendingState;
			$userId = $this -> getUserId();
			//set the id of the user who is adding this collectible
			$this -> data['Approval']['user_id'] = $userId;
			$this -> data['Approval']['date_added'] = date("Y-m-d H:i:s", time());

			$id = $this -> Session -> read('variant-add-id');
			$collectible = $this -> Collectible -> read(null, $id);
			$this -> set('collectible', $collectible);

			$saveData = array();

			$saveData['Approval'] = $this -> data['Approval'];
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

			debug($saveData);
			//Doing this for now because I think the way I am doing uploads is a bit different so error handling doesn't seem to be working
			//or I am not doing this correct.  For now it is forcing Upload to show errors first.  I could validate upload, then run
			// validation on collectible so I get both errors at once.
			if($this -> Collectible -> Upload -> isValidUpload($saveData)) {
				$this -> Collectible -> create();
				if($this -> Collectible -> saveAll($saveData)) {
					$collectible = $this -> Collectible -> findById($this -> Collectible -> id);
					$approvalId = $collectible['Collectible']['approval_id'];
					$this -> Session -> write('lastSaveApprovalId', $approvalId);
					$this -> Session -> write('collectible', $collectible);
					$this -> set($this -> data);
					$this -> redirect( array('action' => 'review'));
				}
			} else {
				$this -> Collectible -> Upload -> validationErrors['0']['file'] = 'Image is required.';
				debug($this -> Collectible -> Upload -> invalidFields());
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
			}
		}
		//         } else {*/
		//         	$this->Session->setFlash('Your session has timed out.');*/
		// 			$this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);*/
		//         }*/
	}

	function review() {
		$this -> checkLogIn();
		$addData = $this -> Session -> read('collectible');
		debug($addData);
		$this -> set('collectible', $addData);
	}

	function confirm() {
		$this -> checkLogIn();
		$addData = $this -> Session -> read('collectible');
		$approvalId = $this -> Session -> read('lastSaveApprovalId');
		debug($approvalId);
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

		debug($addData);

		$this -> set('collectible', $addData);
		$this -> Session -> setFlash(__('The collectible has been submitted!', true), null, null, 'success');
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

