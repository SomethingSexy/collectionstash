<?php
App::import('Sanitize');
/**
 * TODO definitely think about making this edit stuff a behavior for a model
 *
 * 9/1/11 - TC: I decided that edit notes are going to go on the actually edit model, so I can keep a history of those notes for each approval.
 * 				I might consider in the future calling those approval_notes and having just a notes that is used for when a person is making an
 * 				edit and they want to write something.
 * 9/13/11 - TC: The above note doesn't make sense anymore.  I am putting the notes on the revision model for sure because then when history is
 * 				 shown we can also show the notes of each revision.  I am trying to decide what to do with collectible edits that are denied.  Do
 * 				 I need to keep those around or do I not care?  Is a history of that necessary?
 */
class EditsController extends AppController {

	var $name = 'Edits';
	var $helpers = array('Html', 'Ajax', 'Minify.Minify');
	var $components = array('RequestHandler', 'Email');

	function admin_index() {
		$this -> checkLogIn();
		$this -> checkAdmin();
		$this -> paginate = array('group' => array('Edit.collectible_id'), 'conditions' => array('Edit.status' => 0), 'contain' => array('Collectible' => array('fields' => array('Collectible.id, Collectible.name'))), "limit" => 25);

		$edits = $this -> paginate('Edit');
		debug($edits);
		$this -> set('edits', $edits);
	}

	function admin_collectibleEditList($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		$this -> paginate = array('conditions' => array('Edit.collectible_id' => $id, 'Edit.status' => 0), 'order' => array('Edit.created' => 'ASC'), 'contain' => array('AttributesCollectiblesEdit' => array('fields' => array('id')), 'UploadEdit' => array('fields' => array('id')), 'User', 'CollectibleEdit' => array('fields' => array('id'))), "limit" => 25);

		$edits = $this -> paginate('Edit');
		debug($edits);
		foreach ($edits as &$edit) {
			if (!empty($edit['CollectibleEdit']['id'])) {
				$edit['type'] = __('Collectible', true);
				$edit['type_id'] = $edit['CollectibleEdit']['id'];
				unset($edit['UploadEdit']);
				unset($edit['AttributesCollectiblesEdit']);
			} else if (!empty($edit['UploadEdit']['id'])) {
				$edit['type'] = __('Upload', true);
				$edit['type_id'] = $edit['UploadEdit']['id'];
				unset($edit['CollectibleEdit']);
				unset($edit['AttributesCollectiblesEdit']);
			} else if (!empty($edit['AttributesCollectiblesEdit']['id'])) {
				$edit['type'] = __('Attribute', true);
				$edit['type_id'] = $edit['AttributesCollectiblesEdit']['id'];
				unset($edit['CollectibleEdit']);
				unset($edit['UploadEdit']);
			}
		}
		debug($edits);
		$this -> set('edits', $edits);
	}

	/**
	 * Either need to update this function so that it used more models to hide the business logic
	 * or we need to call the indivudual controllers to do the approving.
	 *
	 * I think I like the first, where I am using more model interaction to doing the edit stuff or
	 * at least gather of the data to do the update.
	 */
	function admin_approval($id = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($id && is_numeric($id)) {
			if (isset($this -> data['Approval']['approve'])) {
				$this -> data = Sanitize::clean($this -> data);
				$approvedChange = false;
				$approvalNotes = '';
				if (isset($this -> data['Approval']['notes'])) {
					$approvalNotes = $this -> data['Approval']['notes'];
				}
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
				// $updateType = '';
				// //This tells us what mode we are doing, (E)dit, (A)dd...add is really going to be for the has many relationships
				// $mode = 'E';
				if ($this -> data['Approval']['approve'] === 'true') {
					$failRedirect = array();
					//Check what type of collectible data we are editing
					if (!empty($edit['Edit']['collectible_edit_id'])) {
						$collectibleEditId = $edit['Edit']['collectible_edit_id'];
						$updateFields = $this -> Edit -> CollectibleEdit -> getUpdateFields($collectibleEditId, true, $approvalNotes);
						//First check to make sure something is actually different.
						if (!empty($updateFields)) {
							$approvedChange = true;
							if (isset($updateFields['Revision'])) {
								//If we are going to approve the change, then we need to create a new revision
								$this -> loadModel('Revision');
								if ($this -> Revision -> save($updateFields['Revision'])) {
									$revisionId = $this -> Revision -> id;
									$updateFields['Collectible']['revision_id'] = $revisionId;
									unset($updateFields['Revision']);
								} else {
									//uh fuck you
								}
							}
							$failRedirect = array('admin' => true, 'controller' => 'collectible_edits', 'action' => 'admin_approval', $id, $collectibleEditId);
						} else {
							$approvedChange = false;
						}
					} else if (!empty($edit['Edit']['upload_edit_id'])) {
						$uploadEditId = $edit['Edit']['upload_edit_id'];
						//save the id of the upload that this is for, for the rev later
						$uploadId = $edit['Edit']['upload_id'];
						$updateFields = $this -> Edit -> UploadEdit -> getUpdateFields($uploadEditId, $approvalNotes);
						if (!empty($updateFields)) {
							$approvedChange = true;
							if (isset($updateFields['Revision'])) {
								//If we are going to approve the change, then we need to create a new revision
								$this -> loadModel('Revision');
								if ($this -> Revision -> save($updateFields['Revision'])) {
									$revisionId = $this -> Revision -> id;
									$updateFields['Upload']['revision_id'] = $revisionId;
									unset($updateFields['Revision']);
								} else {
									//uh fuck you
								}
							}
						} else {
							$approvedChange = false;
						}
					} else if (!empty($edit['Edit']['attributes_collectibles_edit_id'])) {
						$attributeEditId = $edit['Edit']['attributes_collectibles_edit_id'];
						$failRedirect = array('admin' => true, 'controller' => 'attributes_collectibles_edits', 'action' => 'admin_approval', $id, $attributeEditId);
						//save the id of the upload that this is for, for the rev later
						$attributeId = $edit['Edit']['attributes_collectible_id'];
						$updateFields = $this -> Edit -> AttributesCollectiblesEdit -> getUpdateFields($attributeEditId, $approvalNotes);
						if (!empty($updateFields)) {
							$approvedChange = true;
							if (isset($updateFields['Revision'])) {
								//If we are going to approve the change, then we need to create a new revision
								$this -> loadModel('Revision');
								if ($this -> Revision -> save($updateFields['Revision'])) {
									$revisionId = $this -> Revision -> id;
									$updateFields['AttributesCollectible']['revision_id'] = $revisionId;
									unset($updateFields['Revision']);
								} else {
									//uh fuck you
								}
							}
						} else {
							$approvedChange = false;
						}
					}
				} else {
					$approvedChange = false;
					//TODO if I deny the change, then I will take the notes and add them on to the email and then add
					// them to the edit model that did not get updated
				}

				$successMessage = '';

				if ($approvedChange) {
					$updateFields['Edit']['id'] = $id;
					$updateFields['Edit']['status'] = 1;
					$updateFields['Edit']['notes'] = $approvalNotes;
					$successMessage = __('The edit has been successfully approved.', true);
				} else {
					//If we are denying the change, lets make sure that the update fields is clean so we do not accidently do something we don't want to
					$updateFields = array();
					$updateFields['Edit']['id'] = $id;
					$updateFields['Edit']['status'] = 2;
					$updateFields['Edit']['notes'] = $approvalNotes;
					$successMessage = __('The edit has been denied.', true);
				}
				debug($updateFields);
				//Update the edit status and anything else
				//if ($this -> Edit -> updateAll($editUpdateFields, array('Edit.id' => $id))) {
				if ($this -> Edit -> saveAll($updateFields, array('validate' => false))) {
					//TODO There is a Incremenet Behavior I could use in the future
					$userUpdateFields = array();
					if ($approvedChange) {
						$userUpdateFields['User.edit_approve_count'] = 'User.edit_approve_count+1';
					} else {
						$userUpdateFields['User.edit_deny_count'] = 'User.edit_deny_count+1';
					}
					//Not sure if I care if this fails
					$this -> Edit -> User -> updateAll($userUpdateFields, array('User.id' => $userId));

					//Grab the user that made the changes
					$editUser = $this -> Edit -> User -> find("first", array('conditions' => array('User.id' => $userId), 'contain' => false));

					//Grab the updated collectible
					$updatedCollectible = $this -> Edit -> Collectible -> find("first", array('conditions' => array('Collectible.id' => $collectibleId), 'contain' => array('Revision')));

					$this -> __sendApprovalEmail($approvedChange, $editUser['User']['email'], $editUser['User']['username'], $updatedCollectible['Collectible']['name'], $edit['Edit']['collectible_id'], $approvalNotes);

					$this -> Session -> setFlash($successMessage, null, null, 'success');
				} else {
					$this -> Session -> setFlash(__('There was a problem submitting the edit.', true), null, null, 'error');
					$this -> redirect($failRedirect, null, true);
				}

				$this -> redirect(array('action' => 'index'), null, true);

			} else {
				$this -> redirect('/');
			}
		}
	}

	function __sendApprovalEmail($approvedChange = true, $email = null, $username = null, $collectibleName = null, $collectileId = null, $notes = '') {
		$return = true;
		if ($email) {
			// Set data for the "view" of the Email
			$this -> set('collectible_url', 'http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectileId);
			$this -> set(compact('collectibleName'));
			$this -> set(compact('username'));
			$this -> set(compact('notes'));
			$this -> Email -> smtpOptions = array('port' => Configure::read('Settings.Email.port'), 'timeout' => Configure::read('Settings.Email.timeout'), 'host' => Configure::read('Settings.Email.host'), 'username' => Configure::read('Settings.Email.username'), 'password' => Configure::read('Settings.Email.password'));
			$this -> Email -> delivery = 'smtp';
			$this -> Email -> to = $email;

			$this -> Email -> from = Configure::read('Settings.Email.from');
			if ($approvedChange) {
				$this -> Email -> template = 'edit_approval';
				$this -> Email -> subject = 'Your change has been successfully approved!';
			} else {
				$this -> Email -> template = 'edit_deny';
				$this -> Email -> subject = 'Oh no! Your change has been denied.';
			}
			$this -> Email -> sendAs = 'both';
			// you probably want to use both :)
			$return = $this -> Email -> send();
			$this -> set('smtp_errors', $this -> Email -> smtpError);
			if (!empty($this -> Email -> smtpError)) {
				$return = false;
				$this -> log('There was an issue sending the email ' . $this -> Email -> smtpError . ' for user ' . $email, 'error');
			}
		} else {
			$return = false;
		}

		return $return;
	}

}
?>