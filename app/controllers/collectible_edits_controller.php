<?php
App::import('Sanitize');
/**
 * This is not a join table, this is the edit controllor for collectibles.  This is why it is named liked this.
 */
class CollectibleEditsController extends AppController {
	var $helpers = array('Html', 'FileUpload.FileUpload', 'CollectibleDetail');

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
	function edit($id = null) {
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
			//At this point $id should always be set
			$collectible = $this -> Collectible -> read(null, $id);
			$this -> data = $collectible;
			$this -> Session -> write('collectible.edit-id', $id);

			$manufactureData = $this -> Collectible -> Manufacture -> getManufactureData($collectible['Collectible']['manufacture_id']);
			$this -> set('manufactures', $manufactureData['manufactures']);
			$this -> set('licenses', $manufactureData['licenses']);
			$this -> set('collectibletypes', $manufactureData['collectibletypes']);

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
		$collectible = $this -> Session -> read('preSaveCollectible');
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
			//if ($this -> isUserAdmin() || Configure::read('Settings.Collectible.Edit.auto-approve') === true) {
			if (Configure::read('Settings.Collectible.Edit.auto-approve') === true) {
				$this -> loadModel('Collectible');
				$newCollectible['Revision']['user_id'] = $this -> getUserId();
				$newCollectible['Revision']['action'] = 'E';
				$newCollectible['Collectible']['id'] = $newCollectible['Collectible']['collectible_id'];
				if ($this -> Collectible -> saveAll($newCollectible, array('validate' => false))) {
					$id = $this -> Collectible -> id;
					$collectible = $this -> Collectible -> findById($id);
					debug($collectible);
					$this -> set('collectible', $collectible);
					$this -> Session -> delete('preSaveCollectible');
					$this -> Session -> delete('uploadId');
					$this -> Session -> delete('collectible.edit-id');
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
					$this -> Session -> delete('uploadId');
					$this -> Session -> delete('collectible.edit-id');
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