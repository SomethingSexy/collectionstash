<?php
App::import('Sanitize');
class CollectibleEditController extends AppController {

	var $name = 'CollectibleEdit';
	var $helpers = array('Html', 'FileUpload.FileUpload');
	// var $components = array('Wizard.Wizard');
// 
	// function beforeFilter() {
		// parent::beforeFilter();
		// $this -> Wizard -> steps = array('manufacture', array('variant' => 'variantFeatures'), 'attributes','review');
		// $this -> Wizard -> completeUrl = '/collectibles/confirm';
		// $this -> Wizard -> loginRequired = true;
	// }
// 
	// function confirm() {
		// $id = $this -> Session -> read('addCollectibleId');
		// if(isset($id) && $id != null) {
			// $collectible = $this -> Collectible -> find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Manufacture', 'Collectibletype', 'License', 'Series', 'Approval', 'Scale', 'Retailer', 'Upload', 'AttributesCollectible' => array('Attribute'))));
			// $this -> set('collectible', $collectible);
			// $this -> Session -> delete('addCollectibleId');
		// } else {
			// $this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			// $this -> redirect( array('action' => 'addSelectType'));
		// }
	// }
// 
	// function wizard($step =null) {
		// $this -> Wizard -> process($step, $this -> isLoggedIn());
	// }
// 	
	// function _prepareManufacture() {
		// $this -> Session -> delete('collectible');
		// $this -> set('collectible_title', __('Add Collectible', true));
		// if(empty($this -> data)) {
			// if(isset($this -> params['pass'][1])) {
				// $id = $this -> params['pass'][1];
				// $collectible = $this -> Collectible -> read(null, $id);
				// //$this -> Session -> write('preSaveCollectible', $collectible);
				// $this -> data = $collectible;
				// $this -> Session -> write('collectible.edit-id', $id);				
			// }
// 
// 
			// $manufactureData = $this -> Collectible -> Manufacture -> getManufactureListData();
			// debug($manufactureData);
			// $this -> set('manufactures', $manufactureData['manufactures']);
			// $this -> set('licenses', $manufactureData['licenses']);
			// $this -> set('collectibletypes', $manufactureData['collectibletypes']);
// 
// 
			// $scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
			// $this -> set(compact('scales'));
		// } else {
			// if($this -> Session -> check('add.collectible.mode.variant')) {
				// $variantCollectible = $this -> Session -> read('add.collectible.variant.collectible');
				// debug($variantCollectible);
				// $this -> set('collectible', $variantCollectible);
			// }
			// $manufactureData = $this -> Collectible -> Manufacture -> getManufactureData($this -> data['Collectible']['manufacture_id']);
			// debug($manufactureData);
			// $this -> set('manufactures', $manufactureData['manufactures']);
			// $this -> set('licenses', $manufactureData['licenses']);
			// $this -> set('collectibletypes', $manufactureData['collectibletypes']);
// 
			// if(isset($wizardData['manufacture']['Collectible']['series_id'])) {
				// $series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($this -> data['Collectible']['manufacture_id'], $this -> data['Collectible']['license_id']);
				// $this -> set('series', $series);
			// }
// 
			// $scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
			// $this -> set(compact('scales'));
// 
		// }
	// }
// 
	// function _processManufacture() {
		// //check if user is logged in
		// if($this -> isLoggedIn()) {
			// debug($this -> data);
			// $newCollectible = array();
			// $this -> data = Sanitize::clean($this -> data);
			// //Since this is a post, take the data that was submitted and set it to our variable
			// $newCollectible = $this -> data;
			// //set default to true
			// $validCollectible = true;
// 
// 
			// //First try and validate the collectible.
			// $this -> Collectible -> set($newCollectible);
			// if($this -> Collectible -> validates()) {
// 
			// } else {
				// debug($this -> Collectible -> invalidFields());
				// $this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				// $validCollectible = false;
			// }
// 
			// if($validCollectible) {
// 
// 
				// return true;
			// } else {
				// return false;
			// }
		// } else {
			// $this -> redirect( array('controller' => 'users', 'action' => 'login'), null, true);
		// }
		// return false;
	// }
	
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
	 *
	 * Notes from 6/27/11
	 * 	- How to handle edits on hasmany relationship
	 * 	id attribute_collectible_id description variant mode (Edit/Delete/Add), if attribute_collectible_id = 0 then it is an Add
	 * 
	 * TODO need to add all of the variant shit back in here
	 * The special variant details will have their own edit section
	 *
	 */
	function manufacture($id =null) {
		$this -> checkLogIn();
		debug($id);
		debug($this -> params['named']);
		if(!$id && empty($this -> data) && empty($this -> params['named']['edit'])) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			//TODO go somewhere
			$this -> redirect($this -> referer());
		}

		$username = $this -> getUsername();
		//Check if it is a variant or not
		$this -> Session -> write('edit.collectible.mode.collectible', true);
		$this -> Session -> delete('edit.collectible.mode.variant');
		$this -> Session -> delete('add.collectible.mode.collectible');
		$this -> Session -> delete('add.collectible.mode.variant');
		// $this -> set('collectible_title', __('Edit Collectible', true));
		// $this -> set('collectible_action', '/collectibleEdit/edit');
		$editMode = false;

		//First check if we are in edit mode
		if(isset($this -> data['Edit']) && $this -> data['Edit'] === 'true') {
			$editMode = true;
		}
		$this -> loadModel('Collectible');
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
				//$collectible = $this -> Session -> read('preSaveCollectible');
				//$this->data['Collectible']['approval_id'] = $collectible['Collectible']['approval_id'];
				$this -> data['Collectible']['collectible_id'] = $this -> Session -> read('collectible.edit-id');
				$this -> Session -> write('preSaveCollectible', $this -> data);
				$this -> redirect( array('action' => 'review'));
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
				return ;
			}
		} else if(empty($this -> data)) {
			//TODO 6/19/11 - finish all of this logic

			if(!empty($this -> params['named']['edit'])) {
				$collectible = $this -> Session -> read('preSaveCollectible');
				$this -> data = $collectible;
				$this -> set('edit', true);
			} else {
				$collectible = $this -> Collectible -> read(null, $id);
				//$this -> Session -> write('preSaveCollectible', $collectible);
				$this -> data = $collectible;
				$this -> Session -> write('collectible.edit-id', $id);
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
			return ;
		} else {
			//do somethinf
		}

	}

	function review() {
		$this -> checkLogIn();
		//remove this cause we do not need it
		$collectible = $this -> Session -> read('preSaveCollectible');
		debug($collectible);
		if(!is_null($collectible)) {
			$this -> loadModel('Collectible');
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
			$this -> set('collectibleReview', $collectible);
			return ;
		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect('/');
		}
	}

	function confirm() {
		$this -> checkLogIn();
		$newCollectible = $this -> Session -> read('preSaveCollectible');
		debug($newCollectible);
		if(!is_null($newCollectible)) {
			//$saveCollectible['Approval'] = $newCollectible['Approval'];
			if($this -> isUserAdmin() || Configure::read('Settings.Collectible.Edit.auto-approve') === true) {
				$this -> loadModel('Collectible');
				$newCollectible['Collectible']['user_id'] = $this -> getUserId();
				$this -> Collectible -> id = $newCollectible['Collectible']['collectible_id'];
				if($this -> Collectible -> save($newCollectible, array('validate' => false))) {
					$id = $this -> Collectible -> id;
					$collectible = $this -> Collectible -> findById($id);
					debug($collectible);
					$this -> set('collectible', $collectible);
					$this -> Session -> delete('preSaveCollectible');
					$this -> Session -> delete('uploadId');
					$this -> Session -> delete('collectible.edit-id');
				} else {
					debug($this -> CollectiblesEdit -> validationErrors);
					$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					$this -> redirect( array('action' => 'review'));
				}
			} else {
				$saveCollectible['CollectibleEdit'] = $newCollectible['Collectible'];
				$saveCollectible['CollectibleEdit']['user_id'] = $this -> getUserId();
				if(isset($newCollectible['AttributesCollectible'])) {
					$saveCollectible['AttributesCollectible'] = $newCollectible['AttributesCollectible'];
				}
				$this -> loadModel('CollectibleEdit');
				$this -> CollectibleEdit -> create();
				if($this -> CollectibleEdit -> saveAll($saveCollectible, array('validate' => false))) {
					$id = $this -> CollectibleEdit -> id;
					$editCollectible = $this -> CollectibleEdit -> findById($id);
					$pendingState = '1';

					$collectible = $editCollectible;
					$collectible['Collectible'] = $editCollectible['CollectibleEdit'];
					unset($collectible['CollectibleEdit']);
					debug($editCollectible);
					$this -> set('collectible', $collectible);
					$this -> Session -> delete('preSaveCollectible');
					$this -> Session -> delete('uploadId');
					$this -> Session -> delete('collectible.edit-id');
				} else {
					debug($this -> CollectiblesEdit -> validationErrors);
					$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					$this -> redirect( array('action' => 'review'));
				}

			}

		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect('/');
		}

	}

}
?>