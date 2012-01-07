<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
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

	public $helpers = array('Html', 'Minify');

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
			if (isset($this -> request -> data['Approval']['approve'])) {
				$this -> request -> data = Sanitize::clean($this -> request -> data);
				$approvedChange = false;
				$approvalNotes = '';
				if (isset($this -> request -> data['Approval']['notes'])) {
					$approvalNotes = $this -> request -> data['Approval']['notes'];
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
				if ($this -> request -> data['Approval']['approve'] === 'true') {
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

	/**
	 * This function right now will return the history of the collectibles the user has submitted.
	 */
	function userHistory() {
		$this -> checkLogIn();
		$userId = $this -> getUserId();
		$this -> paginate = array('conditions' => array('Edit.user_id' => $userId), 'contain' => array('Collectible' => array('fields' => array('id', 'name')), 'AttributesCollectiblesEdit' => array('fields' => array('id')), 'UploadEdit' => array('fields' => array('id')), 'User', 'CollectibleEdit' => array('fields' => array('id'))), "limit" => 25);

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

	function __sendApprovalEmail($approvedChange = true, $userEmail = null, $username = null, $collectibleName = null, $collectileId = null, $notes = '') {
		$return = true;
		if ($userEmail) {
			$email = new CakeEmail('smtp');
			$email -> emailFormat('both');
			$email -> to($userEmail);
			$email -> viewVars(array('collectibleName' => $collectibleName, 'username' => $username,'notes'=> $notes,'collectible_url' => 'http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectileId));
			if ($approvedChange) {
				$email -> template('edit_approval', 'simple');
				$email -> subject('Your change has been successfully approved!');
			} else {
				$email -> template('edit_deny', 'simple');
				$email -> subject('Oh no! Your change has been denied.');
			}
			$email -> send();
		} else {
			$return = false;
		}

		return $return;
	}

}
?>