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
 *
 * 3/6/12 - TC: Going to revamp the Edit model so that instead of all teh random ass columns, it is going to have a type column and a type_id
 *              The type column could be a string literal of "collectible", "upload","attributes", "tag".  This way I can easily add more types
 *               and it will still be human readable and I can have edits all in one place.  Although I am still not sure all of this is necessary but
 *              it keeps it clean.
 */

class EditsController extends AppController {

    public $helpers = array('Html', 'Minify');

    /**
     * This method will show all edits
     */
    function admin_index() {
        $this -> checkLogIn();
        $this -> checkAdmin();
        $this -> paginate = array('group' => array('Edit.collectible_id'), 'conditions' => array('Edit.status' => 0), 'contain' => array('Collectible' => array('fields' => array('Collectible.id, Collectible.name'))), "limit" => 25);

        $edits = $this -> paginate('Edit');
        // debug($edits);
        $this -> set('edits', $edits);
    }

    /**
     * This method will show all edits by a collectible id
     */
    function admin_collectibleEditList($id = null) {
        $this -> checkLogIn();
        $this -> checkAdmin();
        $this -> paginate = array('conditions' => array('Edit.collectible_id' => $id, 'Edit.status' => 0), 'order' => array('Edit.created' => 'ASC'), "limit" => 25);

        $edits = $this -> paginate('Edit');
        // debug($edits);
        $this -> set('edits', $edits);
    }

    /**
     * This method will show all of the edits for this edit.
     *
     * For now, we are only allowing one thing to be edited per edit but this will allow us to have multiple different things being edited at once.
     */
    function admin_view($id = null) {
        $editDetail = $this -> Edit -> getEditDetail($id);
        debug($editDetail);
        $this -> set(compact('editDetail'));
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

                if ($this -> request -> data['Approval']['approve'] === 'true') {
                    /*
                     * If it is an approval, we then need to find all of the things we are
                     * editing and then approve them.
                     */

                    $editDetail = $this -> Edit -> getEditDetail($id);

                    /**
                     * Loop through all of the specific edits for this edit.
                     *
                     * This is for future use, each edit object is saved individually for now
                     * but in the future we will allow multiple different models to be attached
                     * to one edit.
                     *
                     */
                    foreach ($editDetail['Edits'] as $key => $value) {
                        $failRedirect = array();
                        //Check what type of collectible data we are editing
                        if ($value['edit_type'] === 'Collectible') {
                            $collectibleEditId = $value['id'];
                            $updateFields = $this -> Edit -> Collectible -> getUpdateFields($collectibleEditId, true, $approvalNotes);
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
                        } else if ($value['edit_type'] === 'Upload') {
                            $uploadEditId = $value['id'];
                            //save the id of the upload that this is for, for the rev later
                            // $uploadId = $value['base_id'];
                            $updateFields = $this -> Edit -> Collectible -> Upload -> getUpdateFields($uploadEditId, false, $approvalNotes);
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
                        } else if ($value['edit_type'] === 'Attribute') {
                            $attributeEditId = $value['id'];
                            $failRedirect = array('admin' => true, 'controller' => 'attributes_collectibles_edits', 'action' => 'admin_approval', $id, $attributeEditId);
                            //save the id of the upload that this is for, for the rev later
                            // $attributeId = $edit['Edit']['type_id'];
                            $updateFields = $this -> Edit -> Collectible -> AttributesCollectible -> getUpdateFields($attributeEditId, false, $approvalNotes);
                            debug($updateFields);
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
                        } else if ($value['edit_type'] === 'Tag') {
                            $tagEditId = $value['id'];
                            $failRedirect = array('admin' => true, 'controller' => 'collectibles_tags', 'action' => 'admin_approval', $id, $tagEditId);
                            //save the id of the upload that this is for, for the rev later
                            // $attributeId = $edit['Edit']['type_id'];
                            $updateFields = $this -> Edit -> Collectible -> CollectiblesTag -> getUpdateFields($tagEditId, false, $approvalNotes);
                            debug($updateFields);
                            if (!empty($updateFields)) {
                                $approvedChange = true;
                                if (isset($updateFields['Revision'])) {
                                    //If we are going to approve the change, then we need to create a new revision
                                    $this -> loadModel('Revision');
                                    if ($this -> Revision -> save($updateFields['Revision'])) {
                                        $revisionId = $this -> Revision -> id;
                                        $updateFields['CollectiblesTag']['revision_id'] = $revisionId;

                                    } else {
                                        //uh fuck you
                                    }
                                }
                                //This sucks major dick, need to figure out a better way to handle this, since these I am completely deleting
                                if ($updateFields['Revision']['action'] === 'D') {
                                    if ($this -> Edit -> Collectible -> CollectiblesTag -> delete($updateFields['CollectiblesTag']['id'])) {

                                    } else {
                                        //Log it for now
                                        CakeLog::write('error', 'There was a problem deleting a CollectiblesTag with id ' . $updateFields['CollectiblesTag']['id']);
                                    }
                                    unset($updateFields['CollectiblesTag']);
                                }

                                unset($updateFields['Revision']);
                            } else {
                                $approvedChange = false;
                            }
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
                //We need to bind and unbind models here depending on what we are trying to save so we can save
                //them all in one.
                $this -> Edit -> bindModel(array('belongsTo' => array('Upload')));
                $this -> Edit -> bindModel(array('belongsTo' => array('AttributesCollectible')));
                $this -> Edit -> bindModel(array('belongsTo' => array('CollectiblesTag')));
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
                $this -> Edit -> unbindModel(array('belongsTo' => array('Upload')));
                $this -> Edit -> unbindModel(array('belongsTo' => array('AttributesCollectible')));
                $this -> Edit -> unbindModel(array('belongsTo' => array('CollectiblesTag')));
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
        $this -> paginate = array('conditions' => array('Edit.user_id' => $userId), "limit" => 25);

        $edits = $this -> paginate('Edit');
        debug($edits);
        //TODO: Either on the paginate we need to get the detail of that edit or get it after the fact or not show it on the main page
        //I think I will add it back to the paginate because I don't want to drastically change the UI
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
            $email -> viewVars(array('collectibleName' => $collectibleName, 'username' => $username, 'notes' => $notes, 'collectible_url' => 'http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectileId));
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

    // function updateTables() {
        // $edits = $this -> Edit -> find("all", array('contain'=> false));
        // foreach ($edits as $key => $value) {
            // if(isset($value['Edit']['collectible_edit_id']) && !empty($value['Edit']['collectible_edit_id'])) {
                // $this -> loadModel('CollectiblesEdit');
                // $this -> CollectiblesEdit -> read(null, $value['Edit']['collectible_edit_id']);
                // $this -> CollectiblesEdit -> set(array('edit_id'=> $value['Edit']['id']));   
                // $this -> CollectiblesEdit -> save();
            // } else if(isset($value['Edit']['attributes_collectibles_edit_id']) && !empty($value['Edit']['attributes_collectibles_edit_id'])){
                // $this -> loadModel('AttributesCollectiblesEdit');
                // $this -> AttributesCollectiblesEdit -> read(null, $value['Edit']['attributes_collectibles_edit_id']);
                // $this -> AttributesCollectiblesEdit -> set(array('edit_id'=> $value['Edit']['id']));   
                // $this -> AttributesCollectiblesEdit -> save();                
            // }
            // else if(isset($value['Edit']['upload_edit_id']) && !empty($value['Edit']['upload_edit_id'])){
                // $this -> loadModel('UploadsEdit');
                // $this -> UploadsEdit -> read(null, $value['Edit']['upload_edit_id']);
                // $this -> UploadsEdit -> set(array('edit_id'=> $value['Edit']['id']));   
                // $this -> UploadsEdit -> save();                
            // }
        // }
        // debug($edits);
        // $this -> render(false);
    // }

}
?>