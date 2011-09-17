<?php
App::import('Sanitize');
/**
 * This is not a join table, this is the edit controllor for collectibles.  This is why it is named liked this.
 */
class CollectibleEditsController extends AppController {
	var $helpers = array('Html', 'FileUpload.FileUpload', 'CollectibleDetail');

	/*
	 * Ok, need to accept an admin mode parameter.  If it is admin mode and the user is an admin then we can do the edit basically
	 * like edit is auto approved.  If the user is not an admin and for some reason admin mode gets passed in, treat it like a normal edit.
	 * Might want to change it from adminMode? Does that make sense?
	 */
	function edit($id = null, $adminMode = false) {
		$this -> checkLogIn();

		debug($id);
		debug($this -> params['named']);

		if (!$id && !is_numeric($id) && empty($this -> data)) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			//TODO go somewhere
			$this -> redirect($this -> referer());
		}

		$this -> loadModel('Collectible');
		if (!empty($this -> data)) {
			if (!$this -> Session -> check('collectible.edit-id')) {
				//TODO figure out a better place to go, this is the case if some does a submit to this page without doing a GET first to setup all of the data
				$this -> redirect('/');
			}
			$this -> data = Sanitize::clean($this -> data);
			$this -> Collectible -> set($this -> data);
			$validCollectible = true;

			if ($this -> Collectible -> validates()) {

			} else {
				$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
				$validCollectible = false;
			}

			if ($validCollectible) {
				$this -> data['Collectible']['collectible_id'] = $this -> Session -> read('collectible.edit-id');
				$this -> Session -> write('preSaveCollectible', $this -> data);

				$this -> redirect(array('action' => 'review'));
			} else {
				$manufactureData = $this -> Collectible -> Manufacture -> getManufactureData($this -> data['Collectible']['manufacture_id']);

				$this -> set('manufactures', $manufactureData['manufactures']);
				$this -> set('licenses', $manufactureData['licenses']);

				if (isset($newCollectible['Collectible']['series_id'])) {
					$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($this -> data['Collectible']['manufacture_id'], $this -> data['Collectible']['license_id']);
					$this -> set('series', $series);
				}

				// $this -> set('series', $manufactureData['series']);
				$this -> set('collectibletypes', $manufactureData['collectibletypes']);
				$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
				$this -> set(compact('scales'));
				$retailers = $this -> Collectible -> Retailer -> getRetailerList();
				$this -> set('retailers', $retailers);
			}
		} else {
			if ($adminMode === 'true') {
				if (!$this -> isUserAdmin()) {
					$this -> Session -> write('collectible.edit.admin-mode', false);
				} else {
					$this -> Session -> write('collectible.edit.admin-mode', true);
				}
			} else {
				$this -> Session -> write('collectible.edit.admin-mode', false);
			}
			debug($this -> Session -> read('collectible.edit.admin-mode'));

			//At this point $id should always be set
			$collectible = $this -> Collectible -> read(null, $id);
			$this -> data = $collectible;
			$this -> Session -> write('collectible.edit-id', $id);
			if ($collectible['Collectible']['variant']) {
				$this -> Session -> write('edit.collectible.variant', true);
			} else {
				$this -> Session -> delete('edit.collectible.variant');
			}

			$manufactureData = $this -> Collectible -> Manufacture -> getManufactureData($collectible['Collectible']['manufacture_id'], $collectible['Collectible']['collectibletype_id']);
			$this -> set('manufactures', $manufactureData['manufactures']);
			$this -> set('licenses', $manufactureData['licenses']);
			$this -> set('collectibletypes', $manufactureData['collectibletypes']);
			//Hardcoded at level 2 right now but change when we support more
			if(isset($manufactureData['collectibletypes_L2'])){
				$this -> set('collectibletypes_L2', $manufactureData['collectibletypes_L2']);
			}
			$this -> set('selectedTypes', $manufactureData['selectedTypes']);
			
			if (isset($collectible['Collectible']['specialized_type_id']) && is_numeric($collectible['Collectible']['specialized_type_id'])) {
				$specializedTypes = $this -> Collectible -> SpecializedType -> CollectibletypesManufactureSpecializedType -> getSpecializedTypes($collectible['Collectible']['manufacture_id'], $collectible['Collectible']['collectibletype_id']);
				$this -> set('specializedTypes', $specializedTypes);
			}

			if (isset($collectible['Collectible']['series_id'])) {
				$series = $this -> Collectible -> Manufacture -> LicensesManufacture -> getSeries($collectible['Collectible']['manufacture_id'], $collectible['Collectible']['license_id']);
				$this -> set('series', $series);
			}

			$scales = $this -> Collectible -> Scale -> find("list", array('fields' => array('Scale.id', 'Scale.scale')));
			$this -> set(compact('scales'));
			$retailers = $this -> Collectible -> Retailer -> getRetailerList();
			$this -> set('retailers', $retailers);

		}

		$currentCollectibleId = $this -> Session -> read('collectible.edit-id');
		$this -> set(compact('currentCollectibleId'));
	}

	function review() {
		$this -> checkLogIn();
		//remove this cause we do not need it
		$adminMode = $this -> Session -> read('collectible.edit.admin-mode');
		$collectible = $this -> Session -> read('preSaveCollectible');

		debug($adminMode);
		debug($collectible);
		if (!is_null($collectible)) {
			$this -> loadModel('Collectible');
			//fuck you cake
			if (isset($collectible['Collectible']['release']['year'])) {
				$collectible['Collectible']['release'] = $collectible['Collectible']['release']['year'];
			}

			//Lets retrieve some data for display purposes
			//TODO this may be redundant...should we save off later?
			$manufacture = $this -> Collectible -> Manufacture -> find('first', array('conditions' => array('Manufacture.id' => $collectible['Collectible']['manufacture_id']), 'fields' => array('Manufacture.title', 'Manufacture.url'), 'contain' => false));
			$collectible['Manufacture'] = $manufacture['Manufacture'];

			$collectibleType = $this -> Collectible -> Collectibletype -> find('first', array('conditions' => array('Collectibletype.id' => $collectible['Collectible']['collectibletype_id']), 'fields' => array('Collectibletype.name'), 'contain' => false));
			$collectible['Collectibletype'] = $collectibleType['Collectibletype'];

			$license = $this -> Collectible -> License -> find('first', array('conditions' => array('License.id' => $collectible['Collectible']['license_id']), 'fields' => array('License.name'), 'contain' => false));
			$collectible['License'] = $license['License'];

			if (isset($collectible['Collectible']['specialized_type_id'])) {
				$specializedType = $this -> Collectible -> SpecializedType -> find('first', array('conditions' => array('SpecializedType.id' => $collectible['Collectible']['specialized_type_id']), 'fields' => array('SpecializedType.name'), 'contain' => false));
				$collectible['SpecializedType'] = $specializedType['SpecializedType'];
			}

			$scale = $this -> Collectible -> Scale -> find('first', array('conditions' => array('Scale.id' => $collectible['Collectible']['scale_id']), 'fields' => array('Scale.scale'), 'contain' => false));
			$collectible['Scale'] = $scale['Scale'];

			debug($collectible);
			$this -> set('collectibleReview', $collectible);
			return;
		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect('/');
		}
	}

	function confirm() {
		$this -> checkLogIn();
		$newCollectible = $this -> Session -> read('preSaveCollectible');
		debug($newCollectible);
		//TODO this code needs to be refactored
		if (!is_null($newCollectible)) {
			$adminMode = $this -> Session -> read('collectible.edit.admin-mode');
			/*
			 * If I am editing and I am in admin mode, which means I am editing a collectible that is being submitted for approval, and double check
			 * to make sure I am admin user, automatically update this collectible.  This will update the current collectible being added, create a new
			 * rev and then save off one the original user added.  This will keep the full circle of what was originally submitted plus whatever changes
			 * the admin makes.
			 * */
			if (Configure::read('Settings.Collectible.Edit.auto-approve') === true || (isset($adminMode) && $adminMode && $this -> isUserAdmin())) {
				$this -> loadModel('Collectible');
				$newCollectible['Revision']['user_id'] = $this -> getUserId();
				$newCollectible['Revision']['action'] = 'E';
				$newCollectible['Collectible']['id'] = $newCollectible['Collectible']['collectible_id'];
				if ($this -> Collectible -> saveAll($newCollectible, array('validate' => false))) {
					$id = $this -> Collectible -> id;
					$collectible = $this -> Collectible -> findById($id);
					debug($collectible);
					$this -> set('collectible', $collectible);
					$this -> Session -> delete('edit.collectible.variant');
					$this -> Session -> delete('preSaveCollectible');
					$this -> Session -> delete('collectible.edit-id');
					$this -> Session -> delete('collectible.edit.admin-mode');
				} else {
					debug($this -> Collectible -> validationErrors);
					$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					$this -> redirect(array('action' => 'review'));
				}
			} else {
				$saveCollectible['CollectibleEdit'] = $newCollectible['Collectible'];
				$saveCollectible['CollectibleEdit']['edit_user_id'] = $this -> getUserId();
				$saveCollectible['CollectibleEdit']['action'] = 'E';

				$this -> CollectibleEdit -> create();
				if ($this -> CollectibleEdit -> saveAll($saveCollectible, array('validate' => false))) {
					$id = $this -> CollectibleEdit -> id;
					/*
					 * Not sure how useful this will be in the end but I figured this would be a clean
					 * way to store all edits in one table no matter what we are editing of the collectible.
					 *
					 * This would also be a way in the future to link all edits to one edit at a time.
					 */
					$edit = array();
					$edit['user_id'] = $this -> getUserId();
					$edit['collectible_edit_id'] = $id;
					$edit['collectible_id'] = $newCollectible['Collectible']['collectible_id'];
					$this -> loadModel('Edit');
					if (!$this -> Edit -> save($edit)) {
						$this -> log('Failed to save the collectible edit into the edits table ' . $id . ' ' . date("Y-m-d H:i:s", time()), 'error');
					}

					$editCollectible = $this -> CollectibleEdit -> findById($id);
					$pendingState = '1';

					$collectible = $editCollectible;
					$collectible['Collectible'] = $editCollectible['CollectibleEdit'];
					unset($collectible['CollectibleEdit']);
					debug($editCollectible);
					$this -> set('collectible', $collectible);
					$this -> Session -> delete('preSaveCollectible');
					$this -> Session -> delete('collectible.edit-id');
					$this -> Session -> delete('collectible.edit.admin-mode');
				} else {
					debug($this -> CollectibleEdit -> validationErrors);
					$this -> Session -> setFlash(__('Oops! Something wasn\'t entered correctly, please try again.', true), null, null, 'error');
					$this -> redirect(array('action' => 'review'));
				}

			}

		} else {
			$this -> Session -> setFlash(__('Whoa! That was weird.', true), null, null, 'error');
			$this -> redirect('/');
		}

	}

	/**
	 * This method will display the collectible edit view of what is being approved.
	 *
	 * This will compare the current version of the collectible to the one that is in the edit to see what is different.
	 *
	 * I am not sure this is the best solution in the end but it will at least tell me what is different at the time of approval.  This will however,
	 * not tell me exactly what the user changed...only what is different at the time...since I have time stamps of when the edits are this should be fine, however
	 * I might want to update in the future that I only store what is being changed and not the whole collectible.
	 *
	 */
	function admin_approval($editId = null, $collectibleEditId = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($editId && is_numeric($editId) && $collectibleEditId && is_numeric($collectibleEditId)) {
			$this -> set('collectibleEditId', $collectibleEditId);
			$this -> set('editId', $editId);
			if (empty($this -> data)) {
				//TODO this should probably be moved to a model
				$collectibleEditVersion = $this -> CollectibleEdit -> find("first", array('conditions' => array('CollectibleEdit.id' => $collectibleEditId)));
				if (!empty($collectibleEditVersion)) {

					$this -> CollectibleEdit -> compareEdit($collectibleEditVersion['CollectibleEdit'], $collectibleEditVersion['Collectible']);
					debug($collectibleEditVersion);
					$collectible = array();
					$collectible = $collectibleEditVersion;
					unset($collectible['CollectibleEdit']);
					$collectible['Collectible'] = $collectibleEditVersion['CollectibleEdit'];
					debug($collectible);
					$this -> set('collectible', $collectible);
				} else {
					//uh fuck you
					$this -> redirect('/');
				}
			}

		} else {
			$this -> redirect('/');
		}
	}

}
?>