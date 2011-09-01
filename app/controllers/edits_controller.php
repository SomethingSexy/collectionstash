<?php

class EditsController extends AppController {

	var $name = 'Edits';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler', 'Email');

	function admin_index() {
		$this -> checkLogIn();
		$this -> checkAdmin();

		//$this -> paginate = array('group'=> array('Edit.collectible_id'), 'contain' => array('UploadEdit' => array('fields' => array('id')), 'User', 'CollectibleEdit' => array('fields' => array('id'))), "limit" => 25);
		$this -> paginate = array('group' => array('Edit.collectible_id'), 'conditions' => array('Edit.status' => 0), 'contain' => array('Collectible' => array('fields' => array('Collectible.id, Collectible.name'))), "limit" => 25);

		$edits = $this -> paginate('Edit');
		//TODO might want to think about doing a behavior of some sort for this
		//TODO actually, for all edits, Upload and Attributes, I should always link up the collectible Id this is for
		//add this to the edit model
		foreach ($edits as &$edit) {
			if (!empty($edit['CollectibleEdit']['id'])) {
				$edit['type'] = __('Collectible', true);
				$edit['type_id'] = $edit['CollectibleEdit']['id'];
				unset($edit['UploadEdit']);
			} else if (!empty($edit['UploadEdit']['id'])) {
				$edit['type'] = __('Upload', true);
				$edit['type_id'] = $edit['UploadEdit']['id'];
				unset($edit['CollectibleEdit']);
			}
		}
		debug($edits);
		$this -> set('edits', $edits);
	}

	function admin_collectibleEditList($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		$this -> paginate = array('conditions' => array('Edit.collectible_id' => $id, 'Edit.status' => 0), 'order' => array('Edit.created' => 'ASC'), 'contain' => array('UploadEdit' => array('fields' => array('id')), 'User', 'CollectibleEdit' => array('fields' => array('id'))), "limit" => 25);

		$edits = $this -> paginate('Edit');
		//TODO might want to think about doing a behavior of some sort for this
		//TODO actually, for all edits, Upload and Attributes, I should always link up the collectible Id this is for
		//add this to the edit model
		foreach ($edits as &$edit) {
			if (!empty($edit['CollectibleEdit']['id'])) {
				$edit['type'] = __('Collectible', true);
				$edit['type_id'] = $edit['CollectibleEdit']['id'];
				unset($edit['UploadEdit']);
			} else if (!empty($edit['UploadEdit']['id'])) {
				$edit['type'] = __('Upload', true);
				$edit['type_id'] = $edit['UploadEdit']['id'];
				unset($edit['CollectibleEdit']);
			}
		}
		debug($edits);
		$this -> set('edits', $edits);
	}

	function admin_approval($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($id && is_numeric($id)) {
			if (isset($this -> data['Approval']['approve'])) {
				$approvedChange = false;
				//Grab the Edit array we are going to approve
				$edit = $this -> Edit -> find("first", array('conditions' => array('Edit.id' => $id)));
				debug($edit);
				//save off the user id of the user who did the edit
				$userId = $edit['Edit']['user_id'];
				//Save off the id of the collectible that we are editing
				$collectibleId = $edit['Edit']['collectible_id'];
				//Use this to store the fields we are updating
				$updateFields = array();
				//This tells us what type of edit we are doing
				$updateType = '';
				//This tells us what mode we are doing, (E)dit, (A)dd...add is really going to be for the has many relationships
				$mode = 'E';
				if ($this -> data['Approval']['approve'] === 'true') {
					$failRedirect = array();
					//Check what type of collectible data we are editing
					if (!empty($edit['Edit']['collectible_edit_id'])) {
						$updateType = 'collectible';
						$collectibleEditId = $edit['Edit']['collectible_edit_id'];
						$updateFields = $this -> Edit -> CollectibleEdit -> getUpdateFields($collectibleEditId, true);
						//First check to make sure something is actually different.
						if (!empty($updateFields)) {
							$updateFields['Collectible.action'] = '\'E\'';
							$approvedChange = true;
							$failRedirect = array('admin' => true, 'controller' => 'collectible_edits', 'action' => 'admin_approval', $id, $collectibleEditId);
						} else {
							$approvedChange = false;
						}
					} else if (!empty($edit['Edit']['upload_edit_id'])) {
						$updateType = 'upload';
					
						if (empty($edit['Edit']['upload_id'])) {
							$mode = 'A';
						} 
						$uploadEditId = $edit['Edit']['upload_edit_id'];
						if ($mode === 'A') {
							$failRedirect = array('admin' => true, 'controller' => 'upload_edits', 'action' => 'admin_approval', $id, $uploadEditId);
							$uploadToAdd = $this -> Edit -> UploadEdit -> getAddUpload($uploadEditId);
							//TODO at this point, we should double check to see if a collectible was not added already, if there happened to be two submits.
							//If it was added then at some point we should probably flag on there, but for now we can change the action from an Add to an Edit
							if ($this -> Edit -> Upload -> saveAll($uploadToAdd, array('validate' => false))) {
								//This is the id of the new Upload
								$uploadId = $this -> Edit -> Upload -> id;
								$approvedChange = true;
							} else {
								$this -> Session -> setFlash(__('There was a problem saving the new photo.', true), null, null, 'error');
								$this -> redirect(array('admin' => true, 'controller' => 'upload_edits', 'action' => 'admin_approval', $id, $uploadEditId), null, true);
							}
						} else {
							//save the id of the upload that this is for, for the rev later
							$uploadId = $edit['Edit']['upload_id'];
							$updateFields = $this -> Edit -> UploadEdit -> getUpdateFields($uploadEditId);
							if (!empty($updateFields)) {
								$updateFields['Upload.action'] = '\'E\'';
								$approvedChange = true;
							} else {
								$approvedChange = false;
							}
						}
					}
				} else {
					$approvedChange = false;
				}
				if ($updateType !== '') {
					$successMessage = '';
					if ($approvedChange) {
						$updateFields['Edit.status'] = 1;
						$updateFields['User.edit_approve_count'] = 'User.edit_approve_count+1';
						//ALso if there is a successful save, I should update the User edit success count
						$successMessage = __('The edit has been successfully approved.', true);
					} else {
						$updateFields['Edit.status'] = 2;
						$updateFields['User.edit_deny_count'] = 'User.edit_deny_count+1';
						//ALso if there is a successful save, I should update the User edit success count
						$successMessage = __('The edit has been denied.', true);
					}
					debug($updateFields);
					//Update the edit status and anything else
					if ($this -> Edit -> updateAll($updateFields, array('Edit.id' => $id))) {
						//Ok now, lets update history
						//We need to see what type of change we are doing so we know what to update
						//This could have a potential race condition if there are two edits that are approved
						//at the sametime but that seems pretty rare, at least in the early stages

						//We have to do the revision separately because updateAll does not trigger a revision without
						// hack.  This is because updateAll does not trigger afterSave callback.  Once that happens (if ever)
						// we can update this could, or we could add our own hack.
						if ($updateType === 'collectible') {
							$this -> Edit -> Collectible -> id = $collectibleId;
							//If this fails, oh well, just log it for now
							if (!$this -> Edit -> Collectible -> createRevision()) {
								$this -> log('There was an issue saving the revision for collectible id ' . $collectibleId, 'error');
							}
						} else if ($updateType === 'upload' && isset($uploadId) && isset($mode) && $mode === 'E') {
							$this -> Edit -> Upload -> id = $uploadId;
							//If this fails, oh well, just log it for now
							if (!$this -> Edit -> Upload -> createRevision()) {
								$this -> log('There was an issue saving the revision for collectible id ' . $edit['Edit']['upload_edit_id'], 'error');
							}
						}

						//Grab the user that made the changes
						$editUser = $this -> Edit -> User -> find("first", array('conditions' => array('User.id' => $userId), 'contain' => false));

						//Grab the updated collectible
						$updatedCollectible = $this -> Edit -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => false));

						$this -> __sendApprovalEmail($approvedChange, $editUser['User']['email'], $editUser['User']['username'], $updatedCollectible['Collectible']['name'], $edit['Edit']['collectible_id']);
						//Or do I just say fuck it and the user can just see the collectible they changed and a link to the collectible and then they can look at the collectible history?
						//Also need to decide where the description of the change should go...edit model or the collectible edit model? Do I want it to show up in the history or not?

						//Edit Description goes on collectible model/collectible edit/collectible rev
						//Updated Edit to say it is approved
						$this -> Session -> setFlash($successMessage, null, null, 'success');
					} else {
						$this -> Session -> setFlash(__('There was a problem submitting the edit.', true), null, null, 'error');
						//$this -> redirect($failRedirect, null, true);
					}

				} else {
					$this -> Session -> setFlash(__('The type of edit is currently unsupported.', true), null, null, 'error');
				}

				//$this -> redirect(array('action' => 'index'), null, true);

			} else {
				$this -> redirect('/');
			}
		}
	}

	function __sendApprovalEmail($approvedChange = true, $email = null, $username = null, $collectibleName = null, $collectileId = null) {
		$return = true;
		if ($email) {
			// Set data for the "view" of the Email
			$this -> set('collectible_url', 'http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectileId);
			$this -> set(compact('collectibleName'));
			$this -> set(compact('username'));
			$this -> Email -> smtpOptions = array('port' => Configure::read('Settings.Email.port'), 'timeout' => Configure::read('Settings.Email.timeout'), 'host' => Configure::read('Settings.Email.host'), 'username' => Configure::read('Settings.Email.username'), 'password' => Configure::read('Settings.Email.password'));
			$this -> Email -> delivery = 'smtp';
			$this -> Email -> to = $email;

			$this -> Email -> from = Configure::read('Settings.Email.username');
			if ($approvedChange) {
				$this -> Email -> template = 'edit_approval';
				$this -> Email -> subject = 'Your change has been successfully approved!';
			} else {
				$this -> Email -> template = 'edit_deny';
				$this -> Email -> subject = 'Oh no! Your change has been denied.';
			}
			$this -> Email -> sendAs = 'text';
			// you probably want to use both :)
			$return = $this -> Email -> send();
			$this -> set('smtp_errors', $this -> Email -> smtpError);
			if (!empty($this -> Email -> smtpError)) {
				$return = false;
				$this -> log('There was an issue sending the email ' . $this -> Email -> smtpError . ' for user ' . $user_id, 'error');
			}
		} else {
			$return = false;
		}

		return $return;
	}

}
?>