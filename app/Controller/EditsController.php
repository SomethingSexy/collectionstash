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
 *
 * 7/31/12 - TC: Apparently I decided against the type column and instead I store the edit_id in the individual edit tables.  Although now that I have
 * 				 introduce "Attributes", which are different than collectibles...what should I do?
 *
 * 7/31/12 - TC: I am going to be splitting the tables and code up (I might add a component though for editing for controllers) for collectibles and attributes
 * 				 This will keep it simple.  I will still use the behavior but I will just have to have a separate main table for edits.  I could join
 * 				 those two together to have one main view but I am not sure how necessary that really is
 *
 * 8/5/12 - TC: Keep this controller for a main Edit model and then have a CollectiblesEdits and a AttributesEdits and an UploadsEdits join tables
 * 				that will have those specific tables edits information
 * 				- We will need to code a converter to convert from this table to ao CollectiblesEdits.  The collectibles edits table will continue
 * 				  to handle the edits for any of his associated data
 * 				- This way again one edit can be tied to a lot of things
 */

class EditsController extends AppController
{
    
    public $helpers = array('Html', 'Minify');
    // All of these methods would make a good component
    
    
    /**
     * This method will show all edits grouped together by a collectible.  This way I am only seeing the unique edits by that collectible
     */
    function admin_index() {
        $this->checkLogIn();
        $this->checkAdmin();
        $this->paginate = array('conditions' => array('Edit.status' => 0), "limit" => 25);
        $edits = $this->paginate('Edit');
        $this->set('edits', $edits);
        $this->layout = 'fluid';
    }
    /**
     * This method will show all of the edits for this edit.
     *
     * For now, we are only allowing one thing to be edited per edit but this will allow us to have multiple different things being edited at once.
     */
    function admin_view($id = null) {
        $editDetail = $this->Edit->findById($id);
        $this->set(compact('editDetail'));
    }
    /**
     * This is the new approval method, this will replace the old
     *
     * Name sucks balls but it is just me so who cares
     *
     * We also need to add email stuff in here
     */
    function admin_approval_2($id = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        if ($id && is_numeric($id)) {
            // make sure this is a post and it contains the right approval data
            if (($this->request->is('post') || $this->request->is('put')) && isset($this->request->data['Approval']['approve'])) {
                $this->request->data = Sanitize::clean($this->request->data);
                $approvalNotes = '';
                if (isset($this->request->data['Approval']['notes'])) {
                    $approvalNotes = $this->request->data['Approval']['notes'];
                }
                
                if ($this->request->data['Approval']['approve'] === 'true') {
                    // Because I might be approving something that is new, I need
                    // to pass in the user id of the logged in user because
                    // they will be marked as the one who is doing the approving
                    if ($this->Edit->publishEdit($id, $this->getUserId())) {
                        $this->Session->setFlash('The edit has been successfully approved.', null, null, 'success');
                    } else {
                        $this->Session->setFlash(__('There was a problem approving the edit.', true), null, null, 'error');
                    }
                } else {
                    if ($this->Edit->denyEdit($id, $this->getUserId())) {
                        $this->Session->setFlash('The edit has been successfully denied.', null, null, 'success');
                    } else {
                        $this->Session->setFlash(__('There was a problem denying the edit.', true), null, null, 'error');
                    }
                }
                
                $this->redirect(array('action' => 'index'), null, true);
            }
        }
    }
    /**
     * This function right now will return the history of the collectible edits the user has submitted.
     */
    function userHistory($username = null) {
        //Grab the user id of the person logged in
        $user = $this->Edit->User->find("first", array('conditions' => array('User.username' => $username), 'contain' => false));
        
        $this->paginate = array('paramType' => 'querystring', 'conditions' => array('Edit.user_id' => $user['User']['id']), "limit" => 10, 'contain' => array('Status', 'User' => array('fields' => array('id', 'username'))));
        
        $edits = $this->paginate('Edit');
        //TODO: Either on the paginate we need to get the detail of that edit or get it after the fact or not show it on the main page
        //I think I will add it back to the paginate because I don't want to drastically change the UI
        foreach ($edits as & $edit) {
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
        
        $extractEdits = Set::extract('/Edit/.', $edits);
        
        foreach ($extractEdits as $key => $value) {
            $extractEdits[$key]['User'] = $edits[$key]['User'];
            $extractEdits[$key]['Edits'] = $edits[$key]['Edits'];
            $extractEdits[$key]['Status'] = $edits[$key]['Status'];
        }
        
        $this->set('edits', $extractEdits);
    }
}
?>