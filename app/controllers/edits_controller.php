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
				//save off the user id of the user who did the edit
				$userId = $edit['Edit']['user_id'];
				//Save off the id of the collectible that we are editing
				$collectibleId = $edit['Edit']['collectible_id'];
				if ($this -> data['Approval']['approve'] === 'true') {

					//Check what type of collectible data we are editing
					if (!empty($edit['Edit']['collectible_edit_id'])) {
						$updateFields = array();
						$collectibleEditId = $edit['Edit']['collectible_edit_id'];
						$updateFields = $this -> Edit -> CollectibleEdit -> getUpdateFields($collectibleEditId, true);
						//First check to make sure something is actually different.
						if (!empty($updateFields)) {
							$updateFields['action'] = 'E';
							debug($updateFields);
							//Unfortunately we have to use a save and not tie this to the update because the updateAll does
							//trigger callbacks so the rev does not work.  Once this gets updated we can tie this all into one
							//transaction.
							$this -> Edit -> Collectible -> id = $collectibleId;
							$this -> Edit -> Collectible -> set($updateFields);
							if ($this -> Edit -> Collectible -> save(null, array('validate' => false))) {
								$approvedChange = true;
							} else {
								$this -> Session -> setFlash(__('There was a problem saving the changes for the collectible.', true), null, null, 'error');
								$this -> redirect(array('admin' => true, 'controller' => 'collectible_edit', 'action' => 'admin_approval', $id, $collectibleEditId), null, true);
							}
						} else {
							$approvedChange = false;
						}

					} else if (!empty($edit['Edit']['upload_edit_id'])) {
						$uploadEditId = $edit['Edit']['upload_edit_id'];
						$uploadForSave = $this -> Edit -> UploadEdit -> getEditUpload($uploadEditId);
						$this -> loadModel('Upload');
						debug($uploadForSave);
						if ($this -> Upload -> saveEdit($uploadForSave)) {
							$approvedChange = true;
						} else {
							$this -> Session -> setFlash(__('There was a problem saving the changes for the collectible.', true), null, null, 'error');
							$this -> redirect(array('admin' => true, 'controller' => 'upload_edit', 'action' => 'admin_approval', $id, $uploadEditId), null, true);
						}
					}
				} else {
					$approvedChange = false;
				}

				$editUpdateFields = array();

				if ($approvedChange) {
					$editUpdateFields['Edit.status'] = 1;
					$editUpdateFields['User.edit_approve_count'] = 'User.edit_approve_count+1';
					//ALso if there is a successful save, I should update the User edit success count
					$this -> Session -> setFlash(__('The edit has been successfully approved.', true), null, null, 'success');
				} else {
					$editUpdateFields['Edit.status'] = 2;
					$editUpdateFields['User.edit_deny_count'] = 'User.edit_deny_count+1';
					//ALso if there is a successful save, I should update the User edit success count
					$this -> Session -> setFlash(__('The edit has been denied.', true), null, null, 'success');
				}
				//Update the edit status and anything else
				$this -> Edit -> updateAll($editUpdateFields, array('Edit.id' => $id));

				//Grab the user that made the changes
				$editUser = $this -> Edit -> User -> find("first", array('conditions' => array('User.id' => $userId), 'contain' => false));

				//Grab the updated collectible
				$updatedCollectible = $this -> Edit -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => false));

				$this -> __sendApprovalEmail($approvedChange, $editUser['User']['email'], $editUser['User']['username'], $updatedCollectible['Collectible']['name'], $edit['Edit']['collectible_id']);
				//Or do I just say fuck it and the user can just see the collectible they changed and a link to the collectible and then they can look at the collectible history?
				//Also need to decide where the description of the change should go...edit model or the collectible edit model? Do I want it to show up in the history or not?

				//Edit Description goes on collectible model/collectible edit/collectible rev
				//Updated Edit to say it is approved

				$this -> redirect(array('action' => 'index'), null, true);

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