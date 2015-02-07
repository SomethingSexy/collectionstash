<?php
App::uses('Sanitize', 'Utility');
App::uses('CakeEvent', 'Event');
App::uses('ActivityTypes', 'Lib/Activity');
class CollectiblesController extends AppController
{
    
    public $helpers = array('Html', 'Form', 'Js' => array('Jquery'), 'FileUpload.FileUpload', 'CollectibleDetail', 'Minify', 'Tree');
    public $components = array('CollectibleSearch', 'Image');
    /**
     * This method will allow us to quick add a collectible from a selected collectible.
     * This method will base the new collectible off the manufacture and type that this collectible is.
     *
     * We will probably want to check against the type of collectible first in the future to know what
     * we are doing first.
     *
     * Use Cases:
     *  - Add a similar collectible (collectible Id = xx, variant = false)
     *  - Add a variant collectible (collectible Id = xx, variant = true)
     *  - Add a similar collectible that is a variant of a base collectible (collectible Id = xx, variant = false)
     *      - For this case, we will determine here IF the collectible we are copying from IS a variant, then we will use its base collectible as the base collectible for the new collectible
     *
     * $collectibleId - this is the id that we are "Copying"
     * $variant - this is a variant add?
     */
    function quickCreate($collectibleId = null, $variant = false) {
        $this->checkLogIn();
        if (!is_null($collectibleId) && is_numeric($collectibleId)) {
            
            if ($variant === 'true') {
                //If we are adding a variant, copy the collectible completely
                // setting its parent to this collectible and making it a variant
                $response = $this->Collectible->createCopy($collectibleId, $this->getUserId(), true);
                
                if ($response['response']['isSuccess']) {
                    $this->redirect(array('action' => 'edit', $response['response']['data']['collectible_id']));
                } else {
                }
            } else {
            }
        } else {
            $this->redirect($this->referer());
        }
    }
    /**
     * This will be used to create a new collectible, with just
     * the type to start as well if they are trying to add a custom, original piece or a standard collectible
     */
    public function create($collectibleTypeId = null, $original = false, $custom = false) {
        $this->checkLogIn();
        if ($collectibleTypeId && is_numeric($collectibleTypeId)) {
            $response = $this->Collectible->createInitial($collectibleTypeId, $original, $custom, $this->getUserId());
            if ($response['response']['isSuccess']) {
                $this->redirect(array('action' => 'edit', $response['response']['data']['id']));
            } else {
            }
        }
        // Always do this in case there is an error
        $collectibleTypes = $this->Collectible->Collectibletype->find('threaded', array('contain' => false));
        $this->set(compact('collectibleTypes'));
    }
    
    public function admin_edit($id) {
        // Need to check login
        $this->checkLogIn();
        $this->checkAdmin();
        $collectible = $this->Collectible->find('first', array('contain' => array('Status'), 'conditions' => array('Collectible.id' => $id)));
        // Admin gets allowed access to view and edit everything
        if (empty($collectible)) {
            $this->render('viewMissing');
            return;
        }
        // This is the basic stuff to get for edit attributes
        $attributeCategories = $this->Collectible->AttributesCollectible->Attribute->AttributeCategory->find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
        $this->set(compact('attributeCategories'));
        // Pass the id to the view to use
        $this->set('collectibleId', $id);
        $this->set('adminMode', true);
        // For now, we only need to worrying about deleting if it is status 4, otherwise
        // the admin can just deny an approval if it is status 2
        $this->set('allowDelete', $this->isUserAdmin() && $collectible['Status']['id'] === '4');
        $this->render('edit');
    }
    /**
     * New view but not sure what this is going to do yet
     */
    public function edit($id) {
        $this->layout = 'require';
        // Need to check login
        $this->checkLogIn();
        // Need to get the collectible
        // Based on status and the logged in user, need to determine if we can proceed
        
        // If the status is draft, then the only person who can edit it is the person who submitted it
        // If the status is submitted, then the only person who can edit it is the persno who submitted it and an admin
        // If the status is active, then anyone can edit it
        $collectible = $this->Collectible->getCollectible($id);
        
        $collectible = $collectible['response']['data']['collectible'];
        $variants = array();
        if (isset($collectible['response']['data']['variants'])) {
            $variants = $collectible['response']['data']['variants'];
        }
        $this->set('variants', $variants);
        
        if (!empty($collectible)) {
            if (!$this->Collectible->isEditPermission($id, $this->getUser())) {
                $this->render('editAccess');
                return;
            }
        } else {
            $this->render('viewMissing');
            return;
        }
        $parts = array();
        // only do an extract if not empty, otherwise it seems to return
        // an array with one empty element
        if (!empty($collectible['AttributesCollectible'])) {
            $parts = Set::extract('/AttributesCollectible/.', $collectible);
        }
        unset($collectible['AttributesCollectible']);
        // we have to do some processing on the part uploads, kind of lame
        foreach ($parts as $partKey => $part) {
            foreach ($part['Attribute']['AttributesUpload'] as $key => $value) {
                $thumbnail = $this->Image->image($value['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
                // ugh this might be overkill but I would like for the user to know, when editing if an upload is already pending an edit, such as delete, so there
                // aren't multiple deletes.
                $pending = $this->Collectible->AttributesCollectible->Attribute->AttributesUpload->findPendingEdits(array('AttributesUploadEdit.base_id' => $value['id']));
                debug($pending);
                // this should only contain action_type === 4, which is a removal
                $pendingRemoval = false;
                foreach ($pending as $editKey => $edit) {
                    if ($edit['Action']['action_type_id'] === '4') {
                        $pendingRemoval = true;
                    }
                }
                $parts[$partKey]['Attribute']['AttributesUpload'][$key]['Upload']['thumbnail_url'] = $thumbnail['path'];
                $parts[$partKey]['Attribute']['AttributesUpload'][$key]['Upload']['delete_url'] = '/attributes_uploads/remove/' . $value['id'] . '/false';
                $parts[$partKey]['Attribute']['AttributesUpload'][$key]['Upload']['delete_type'] = 'POST';
                
                $parts[$partKey]['Attribute']['AttributesUpload'][$key]['Upload']['pending'] = $pendingRemoval;
                $parts[$partKey]['Attribute']['AttributesUpload'][$key]['Upload']['allowDelete'] = !$pendingRemoval;
                if ($pendingRemoval) {
                    $parts[$partKey]['Attribute']['AttributesUpload'][$key]['Upload']['pendingText'] = __('Pending Removal');
                }
            }
        }
        
        $this->set('parts', $parts);
        // This is the basic stuff to get for edit attributes
        // This one will always be required
        $attributeCategories = $this->Collectible->AttributesCollectible->Attribute->AttributeCategory->find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
        $this->set(compact('attributeCategories'));
        // Pass the id to the view to use
        $this->set('collectibleId', $id);
        // if the user is an admin and the status is 4 then allow deleting
        $this->set('allowDelete', $this->isUserAdmin() && $collectible['Status']['id'] === '4');
        
        $collectibleTypeId = $collectible['Collectible']['collectibletype_id'];
        // Get and return all brands, this is for adding new manufacturers
        // and also used for types that might allow not having a manufacturer
        $brands = $this->Collectible->License->find('all', array('contain' => false));
        $this->set('brands', $brands);
        //Grab all scales
        $scales = $this->Collectible->Scale->find("all", array('contain' => false, 'fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
        // $returnData['response']['data']['scales'] = $scales;
        $this->set('scales', $scales);
        //Grab all currencies
        $currencies = $this->Collectible->Currency->find("all", array('contain' => false, 'fields' => array('Currency.id', 'Currency.iso_code')));
        
        $this->set('currencies', $currencies);
        
        $artists = $this->Collectible->ArtistsCollectible->Artist->find("all", array('order' => array('Artist.name' => 'ASC'), 'contain' => false));
        
        $this->set('artists', $artists);
        
        $categories = $this->Collectible->AttributesCollectible->Attribute->AttributeCategory->find("all", array('contain' => false));
        
        $this->set('categories', $categories);
        
        $manufacturers = $this->Collectible->Manufacture->find('all', array('contain' => array('Upload', 'LicensesManufacture' => array('License'))));
        
        $extractManufacturers = Set::extract('/Manufacture/.', $manufacturers);
        
        foreach ($extractManufacturers as $key => $value) {
            $extractManufacturers[$key]['LicensesManufacture'] = $manufacturers[$key]['LicensesManufacture'];
            if (isset($manufacturers[$key]['Upload'])) {
                $extractManufacturers[$key]['Upload'] = $manufacturers[$key]['Upload'];
                $thumbnail = $this->Image->image($manufacturers[$key]['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
                $extractManufacturers[$key]['Upload']['thumbnail_url'] = $thumbnail['path'];
                $extractManufacturers[$key]['Upload']['delete_url'] = '/uploads/remove/' . $manufacturers[$key]['Upload']['id'] . '/false';
                $extractManufacturers[$key]['Upload']['delete_type'] = 'DELETE';
                $extractManufacturers[$key]['Upload']['pending'] = false;
                $extractManufacturers[$key]['Upload']['allowDelete'] = true;
            }
        }
        
        $this->set('manufacturers', $extractManufacturers);
        
        $customStatuses = $this->Collectible->CustomStatus->find('all', array('contain' => false));
        // $returnData['response']['data']['customStatuses'] = $customStatuses;
        $this->set('customStatuses', $customStatuses);
        //TODO: This is here temporarily until all of the attribute modals are
        // converted to backbone
        $this->set(compact('collectible'));
        
        $permissions = array();
        
        if ($this->isUserAdmin()) {
            $permissions['edit_manufacturer'] = true;
        } else {
            $permissions['edit_manufacturer'] = false;
        }
        
        $this->set(compact('permissions'));
    }
    
    public function collectible($id = null, $replacementId = null) {
        
        if ($this->request->isPut()) {
            $collectible['Collectible'] = $this->request->input('json_decode', true);
            //$collectible['Collectible'] = Sanitize::clean($collectible['Collectible']);
            
            $response = $this->Collectible->saveCollectible($collectible, $this->getUser());
            
            $request = $this->request->input('json_decode');
            
            if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                $this->response->statusCode(401);
            } else {
                // request becomes an actual object and not an array
                $request->isEdit = $response['response']['data']['isEdit'];
            }
            
            $this->set('returnData', $request);
        } else if ($this->request->isDelete()) {
            // I think it makes sense to use rest delete
            // for changing the status to a delete
            // although I am going to physically delete it
            // not change the status :)
            $response = $this->Collectible->remove($id, $this->getUser(), $replacementId);
            
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
            }
            
            $this->set('returnData', $response);
        } else if ($this->request->isGet()) {
            $returnData = $this->Collectible->getCollectible($id);
            $this->set('returnData', $returnData['response']['data']['collectible']['Collectible']);
        }
    }
    // This is the new API for returning collectibles, should support search, filter and sort
    public function collectibles() {
        
        $collectibles = $this->CollectibleSearch->search(array(), 'querystring');
        $extractCollectibles = Set::extract('/Collectible/.', $collectibles);
        
        foreach ($extractCollectibles as $key => $value) {
            $extractCollectibles[$key]['CollectiblesUpload'] = $collectibles[$key]['CollectiblesUpload'];
            $extractCollectibles[$key]['Collectibletype'] = $collectibles[$key]['Collectibletype'];
            $extractCollectibles[$key]['Manufacture'] = $collectibles[$key]['Manufacture'];
            $extractCollectibles[$key]['License'] = $collectibles[$key]['License'];
            $extractCollectibles[$key]['AttributesCollectible'] = $collectibles[$key]['AttributesCollectible'];
        }
        
        $this->set('collectibles', $extractCollectibles);
    }
    
    public function status($id) {
        // check login
        // check to make sure they can make this change
        
        if ($this->request->isPut()) {
            $data = $this->request->input('json_decode', true);
            $hasDupList = false;
            if (isset($data['hasDupList']) && $data['hasDupList']) {
                $hasDupList = true;
            }
            $response = $this->Collectible->updateStatus($id, $this->getUser(), $hasDupList);
            
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
            }
            
            $this->set('returnData', $response);
            // we need to check the response here
            
            
        }
    }
    // This will handle the updating of tags
    public function tag($id = null) {
        
        if ($this->request->isPost()) {
            $collectible['CollectiblesTag'] = $this->request->input('json_decode', true);
            $response = $this->Collectible->CollectiblesTag->add($collectible, $this->getUser());
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
            }
            
            $this->set('returnData', $response);
            // we need to check the response here
            
            
        } else if ($this->request->isDelete()) {
            $collectible['CollectiblesTag'] = array();
            $collectible['CollectiblesTag']['id'] = $id;
            $response = $this->Collectible->CollectiblesTag->remove($collectible, $this->getUser());
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
            }
            
            $this->set('returnData', $response);
        }
    }
    // This will handle the updating of artists
    public function artist($id = null) {
        
        if ($this->request->isPost()) {
            $collectible['ArtistsCollectible'] = $this->request->input('json_decode', true);
            $response = $this->Collectible->ArtistsCollectible->add($collectible, $this->getUser());
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
            }
            
            $this->set('returnData', $response);
            // we need to check the response here
            
            
        } else if ($this->request->isDelete()) {
            $collectible['ArtistsCollectible'] = array();
            $collectible['ArtistsCollectible']['id'] = $id;
            $response = $this->Collectible->ArtistsCollectible->remove($collectible, $this->getUser());
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
            }
            
            $this->set('returnData', $response);
        }
    }
    /**
     * this method will be used to allow them to delete a collectible
     */
    public function delete($id) {
        $this->Collectible->remove($id, $this->getUser());
    }
    /**
     * This will process cache clearing requests
     */
    public function cache() {
        // we don't need a view for this one
        $this->autoRender = false;
        if (!$this->isLoggedIn()) {
            $this->response->body(__('You do not have permissions to complete this request.'));
            $this->response->statusCode(401);
            return;
        }
        
        if (!$this->isUserAdmin()) {
            $this->response->body(__('You do not have permissions to complete this request.'));
            $this->response->statusCode(401);
            return;
        }
        
        if (!$this->request->isPost()) {
            $this->response->body(__('Invalid request.'));
            // invalid request
            $this->response->statusCode(400);
            return;
        }
        
        $cache = $this->request->input('json_decode', true);
        
        if ($cache['clearAll']) {
            $this->Collectible->clearAll();
        } else if ($cache['collectible_id']) {
            $this->Collectible->clearCache($cache['collectible_id'], true);
        } else {
            $this->response->body(__('Invalid request.'));
            // invalid request
            $this->response->statusCode(400);
            return;
        }
        
        $this->response->body('{}');
    }
    
    function view($id = null) {
        if (!$id) {
            $this->Session->setFlash(__('Invalid collectible', true));
            $this->redirect(array('action' => 'index'));
        }
        
        $collectible = $this->Collectible->getCollectible($id);
        
        if (!$collectible['response']['isSuccess']) {
            $this->render('viewMissing');
            return;
        }
        
        $collectible = $collectible['response']['data']['collectible'];
        // View should also work for status of submitted and active
        // any other status should redirect to a missing view: daft and deleted
        if (!empty($collectible) && ($collectible['Collectible']['status_id'] === '4' || $collectible['Collectible']['status_id'] === '2')) {
            // Figure out all permissions
            $editPermission = $this->Collectible->isEditPermission($collectible, $this->getUser());
            $this->set('allowEdit', $editPermission);
            
            $stashablePermission = $this->Collectible->isStashable($collectible, $this->getUser());
            $this->set('isStashable', $stashablePermission);
            // if it is submitted and the accesing user is the one who created it
            // then they can edit the status, which means they can make it a draft
            if ($collectible['Collectible']['status_id'] === '2') {
                $this->set('showStatus', true);
                if ($collectible['Collectible']['user_id'] === $this->getUserId()) {
                    $this->set('allowStatusEdit', true);
                } else {
                    $this->set('allowStatusEdit', false);
                }
            } else {
                $this->set('showStatus', false);
                $this->set('allowStatusEdit', false);
            }
            
            if ($collectible['Collectible']['custom'] || $collectible['Collectible']['original']) {
                $this->set('allowVariantAdd', false);
            } else {
                $this->set('allowVariantAdd', true);
            }
            // Set and get all other info needed
            $this->set('collectible', $collectible);
            $count = $this->Collectible->getNumberofCollectiblesInStash($id);
            $this->set('collectibleCount', $count);
            
            $variants = $this->Collectible->getCollectibleVariants($id);
            $this->set('variants', $variants);
            // This is for the logged in user
            if ($this->isLoggedIn()) {
                $collectibleUserCount = $this->Collectible->CollectiblesUser->getCollectibleOwnedCount($id, $this->getUser());
                $this->set(compact('collectibleUserCount'));
                $collectibleWishListCount = $this->Collectible->CollectiblesWishList->getCollectibleWishListCount($id, $this->getUser());
                $this->set(compact('collectibleWishListCount'));
            }
            
            $transactionGraphData = $this->Collectible->Listing->Transaction->getTransactionGraphData($id);
            $this->set(compact('transactionGraphData'));
            // retrieve all comments
            $comments = $this->Collectible->EntityType->Comment->getComments($collectible['Collectible']['entity_type_id'], $this->getUserId());
            
            $extractComments = Set::extract('/Comment/.', $comments['comments']);
            
            foreach ($extractComments as $key => $value) {
                $extractComments[$key]['User'] = $comments['comments'][$key]['User'];
                $extractComments[$key]['permissions'] = $comments['comments'][$key]['permissions'];
            }
            
            $this->set('comments', $extractComments);
            // permissions
            $permissions = array();
            
            if ($this->isLoggedIn()) {
                $permissions['add_comment'] = true;
            } else {
                $permissions['add_comment'] = false;
            }
            $this->set(compact('permissions'));
            
            $userUploads = $this->Collectible->CollectiblesUser->find('all', array('contain' => array('UserUpload'), 'conditions' => array('CollectiblesUser.collectible_id' => $id, "not" => array("CollectiblesUser.user_upload_id" => null))));
            
            $extractUserUploads = Set::extract('/UserUpload/.', $userUploads);
            
            foreach ($extractUserUploads as $key => $value) {
                $img = $this->Image->image($value['name'], array('uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $value['user_id'], 'imagePathOnly' => true));
                $extractUserUploads[$key]['imagePath'] = $img['path'];
            }
            
            $this->set('userUploads', $extractUserUploads);
            $this->layout = 'require';
            
            $this->getEventManager()->dispatch(new CakeEvent('Controller.Track.view', $this, array('type' => 'collectible', 'id' => $id, 'ip' => $this->getClientIP(), 'user_id' => $this->getUserId())));
        } else {
            $this->render('viewMissing');
        }
    }
    
    function search() {
        /*
         *For now update so we do not return originals and customs
        */
        $collectibles = $this->CollectibleSearch->search(array('Collectible.original' => false, 'Collectible.custom' => false));
        // I can use this to pull the pagination data off the request and pass it to the view
        // although in the JSON view, I should be able to pull all of the data off the request
        // and build out the JSON object and send that down, with access to the pagination
        // information.  I can pass it as meta data that the client side script can then use
        // to know how to make the next set of requests
        if ($this->request->isAjax()) {
            $this->render('searchJson');
        } else {
            // for now if it is a standard request we will want to return
            // if the user owns this collectible, obviously only run this check if
            // they are checked in
            
            if ($this->isLoggedIn()) {
                // modify the return data and then set it again
                foreach ($collectibles as $key => $value) {
                    $collectibleUserCount = $this->Collectible->CollectiblesUser->getCollectibleOwnedCount($value['Collectible']['id'], $this->getUser());
                    $collectibleWishListCount = $this->Collectible->CollectiblesWishList->getCollectibleWishListCount($value['Collectible']['id'], $this->getUser());
                    
                    $collectibles[$key]['Collectible']['userCounts'] = array('stashCount' => $collectibleUserCount, 'wishListCount' => $collectibleWishListCount);
                }
                
                $this->set(compact('collectibles'));
            }
            
            $this->layout = 'fluid';
            $this->set('viewType', 'list');
            $this->render('searchList');
        }
    }
    /**
     * We need to two methods because the tile stuff using the infinite scroll
     * which uses the standard HTML response to parse out the contents
     */
    function searchTiles($type = 'list') {
        /*
         * Call the parent method now, that method handles pretty much everything now
        */
        $collectibles = $this->CollectibleSearch->search(array('Collectible.original' => false, 'Collectible.custom' => false));
        
        if ($this->isLoggedIn()) {
            // modify the return data and then set it again
            foreach ($collectibles as $key => $value) {
                $collectibleUserCount = $this->Collectible->CollectiblesUser->getCollectibleOwnedCount($value['Collectible']['id'], $this->getUser());
                $collectibleWishListCount = $this->Collectible->CollectiblesWishList->getCollectibleWishListCount($value['Collectible']['id'], $this->getUser());
                
                $collectibles[$key]['Collectible']['userCounts'] = array('stashCount' => $collectibleUserCount, 'wishListCount' => $collectibleWishListCount);
            }
            
            $this->set(compact('collectibles'));
        }
        
        $this->set('viewType', 'tiles');
        $this->layout = 'fluid';
        $this->render('searchTiles');
    }
    
    function history($id = null) {
        $this->checkLogIn();
        if ($id && is_numeric($id)) {
            $this->Collectible->id = $id;
            $history = $this->Collectible->revisions(null, true);
            $this->loadModel('User');
            //TODO the revision behavior needs to get updated so that we can return associated data with it
            //Maybe the revision behavior should also interact with the Revision model
            //Making this by reference so we can modify it, is this proper in php?
            foreach ($history as $key => & $collectible) {
                $collectibleRevision = $this->Collectible->Revision->findById($collectible['Collectible']['revision_id'], array('contain' => false));
                $collectible['Collectible']['action'] = $collectibleRevision['Revision']['action'];
                if ($collectibleRevision['Revision']['action'] !== 'A') {
                    $editUserDetails = $this->User->findById($collectibleRevision['Revision']['user_id'], array('contain' => false));
                    $collectible['Collectible']['user_name'] = $editUserDetails['User']['username'];
                } else {
                    $userId = $collectible['Collectible']['user_id'];
                    $userDetails = $this->User->findById($userId, array('contain' => false));
                    $collectible['Collectible']['user_name'] = $userDetails['User']['username'];
                }
            }
            
            $this->set(compact('history'));
            //Grab a list of all attributes associated with this collectible, or were associated with this collectible.  We will display a list of all
            //of these attributes then we can go into further history detail if we need too
            $attributeHistory = $this->Collectible->AttributesCollectible->find("all", array('conditions' => array('AttributesCollectible.collectible_id' => $id)));
            $this->set(compact('attributeHistory'));
            //Update this in the future since we only allow one Upload for now
            $collectibleUpload = $this->Collectible->Upload->find("first", array('contain' => false, 'conditions' => array('Upload.collectible_id' => $id)));
            $uploadHistory = array();
            if (!empty($collectibleUpload)) {
                $this->Collectible->Upload->id = $collectibleUpload['Upload']['id'];
                $uploadHistory = $this->Collectible->Upload->revisions(null, true);
                //This is like the worst thing ever and needs to get cleaned up
                //Making this by reference so we can modify it, is this proper in php?
                foreach ($uploadHistory as $key => & $upload) {
                    $uploadRevision = $this->Collectible->Revision->findById($upload['Upload']['revision_id'], array('contain' => false));
                    
                    $upload['Upload']['action'] = $uploadRevision['Revision']['action'];
                    
                    $editUserDetails = $this->User->findById($uploadRevision['Revision']['user_id'], array('contain' => false));
                    $upload['Upload']['user_name'] = $editUserDetails['User']['username'];
                }
                //As of 9/7/11, because of the way we have to add an upload, the first revision is going to be bogus.
                //Pop it off here until we can update the revision behavior so that we can specific a save to not add a revision.
                $lastUpload = end($uploadHistory);
                if ($lastUpload['Upload']['revision_id'] === '0') {
                    array_pop($uploadHistory);
                }
                reset($uploadHistory);
            }
            
            $this->set(compact('uploadHistory'));
        } else {
            $this->redirect($this->referer());
        }
    }
    
    function historyDetail($id = null, $version_id = null) {
        $this->checkLogIn();
        
        if ($id && $version_id && is_numeric($id) && is_numeric($version_id)) {
            $this->Collectible->id = $id;
            $collectible = $this->Collectible->revisions(array('conditions' => array('version_id' => $version_id)), true);
            
            $this->set(compact('collectible'));
        } else {
            //$this -> redirect($this -> referer());
            
            
        }
    }
    /**
     * This will return all user history, this is a public api
     */
    function userHistory($username) {
        //Grab the user id of the person logged in
        $user = $this->Collectible->User->find("first", array('conditions' => array('User.username' => $username), 'contain' => false));
        
        $conditions = array();
        $conditions['Collectible.user_id'] = $user['User']['id'];
        // handle both cases
        $conditions['OR'] = array('Collectible.status_id' => array('1', '2', '3', '4'));
        $this->paginate = array('paramType' => 'querystring', 'conditions' => $conditions, 'contain' => array('User' => array('fields' => array('id', 'username')), 'Collectibletype', 'Manufacture', 'Status'), 'limit' => 10);
        $collectibles = $this->paginate('Collectible');
        
        $extractCollectibles = Set::extract('/Collectible/.', $collectibles);
        
        foreach ($extractCollectibles as $key => $value) {
            $extractCollectibles[$key]['User'] = $collectibles[$key]['User'];
            $extractCollectibles[$key]['Collectibletype'] = $collectibles[$key]['Collectibletype'];
            $extractCollectibles[$key]['Manufacture'] = $collectibles[$key]['Manufacture'];
            $extractCollectibles[$key]['Status'] = $collectibles[$key]['Status'];
        }
        
        $this->set('collectibles', $extractCollectibles);
    }
    
    function admin_index() {
        $this->checkLogIn();
        $this->checkAdmin();
        
        $this->paginate = array("conditions" => array('status_id' => 2), "contain" => array('Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')));
        
        $collectilbes = $this->paginate('Collectible');
        
        $this->set('collectibles', $collectilbes);
        
        $this->layout = 'fluid';
    }
    
    function admin_cache() {
        $this->checkLogIn();
        $this->checkAdmin();
        
        $this->layout = 'fluid';
    }
    
    function admin_view($id = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        
        if (!$id) {
            $this->Session->setFlash(__('Invalid collectible', true));
            $this->redirect(array('action' => 'index'));
        }
        
        $collectible = $this->Collectible->getCollectible($id);
        $collectible = $collectible['response']['data']['collectible'];
        // View should also work for status of submitted and active
        if (!empty($collectible) && ($collectible['Collectible']['status_id'] === '4' || $collectible['Collectible']['status_id'] === '2')) {
            // Figure out all permissions
            $editPermission = $this->Collectible->isEditPermission($collectible, $this->getUser());
            $this->set('allowEdit', $editPermission);
            
            $stashablePermission = $this->Collectible->isStashable($collectible, $this->getUser());
            $this->set('isStashable', $stashablePermission);
            // figure out how to merge this with the rest later
            if ($collectible['Collectible']['status_id'] === '2') {
                $this->set('showStatus', true);
                if ($collectible['Collectible']['user_id'] === $this->getUserId()) {
                    $this->set('allowStatusEdit', true);
                } else {
                    $this->set('allowStatusEdit', false);
                }
            } else {
                $this->set('showStatus', false);
                $this->set('allowStatusEdit', false);
            }
            
            if ($collectible['Collectible']['custom'] || $collectible['Collectible']['original']) {
                $this->set('allowVariantAdd', false);
            } else {
                $this->set('allowVariantAdd', true);
            }
            // Set and get all other info needed
            $this->set('collectible', $collectible);
            $count = $this->Collectible->getNumberofCollectiblesInStash($id);
            $this->set('collectibleCount', $count);
            
            $variants = $this->Collectible->getCollectibleVariants($id);
            $this->set('variants', $variants);
            
            $this->layout = 'require';
        } else {
            $this->render('viewMissing');
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
        $this->checkLogIn();
        $this->checkAdmin();
        if ($editId && is_numeric($editId) && $collectibleEditId && is_numeric($collectibleEditId)) {
            $this->set('collectibleEditId', $collectibleEditId);
            $this->set('editId', $editId);
            if (empty($this->request->data)) {
                $collectible = $this->Collectible->getEditForApproval($collectibleEditId);
                //TODO hack for now
                if (isset($collectible['Collectible']['series_id']) && !empty($collectible['Collectible']['series_id'])) {
                    $fullSeriesPath = $this->Collectible->Series->buildSeriesPathName($collectible['Collectible']['series_id']);
                    $collectible['Collectible']['seriesPath'] = $fullSeriesPath;
                }
                if ($collectible) {
                    $this->set('collectible', $collectible);
                } else {
                    //uh fuck you
                    $this->redirect('/');
                }
            }
        } else {
            $this->redirect('/');
        }
    }
    
    function admin_approve($id = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        if ($id && is_numeric($id) && isset($this->request->data['Approval']['approve'])) {
            $collectible = $this->Collectible->find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('User', 'CollectiblesUpload' => array('Upload'), 'AttributesCollectible', 'Collectibletype', 'Manufacture', 'ArtistsCollectible' => array('Artist'))));
            $this->request->data = Sanitize::clean($this->request->data);
            $notes = $this->request->data['Approval']['notes'];
            //Approve
            if ($this->request->data['Approval']['approve'] === 'true') {
                if (!empty($collectible) && $collectible['Collectible']['status_id'] === '2') {
                    $data = array();
                    $data['Collectible'] = array();
                    $data['Collectible']['id'] = $collectible['Collectible']['id'];
                    $data['Collectible']['status_id'] = 4;
                    $data['Revision']['action'] = 'P';
                    $data['Revision']['user_id'] = $this->getUserId();
                    $data['Revision']['notes'] = $this->request->data['Approval']['notes'];
                    if ($this->Collectible->saveAll($data, array('validate' => false))) {
                        //Ugh need to get this again so I can get the Revision id
                        $collectible = $this->Collectible->find('first', array('conditions' => array('Collectible.id' => $id), 'contain' => array('Manufacture', 'Collectibletype', 'ArtistsCollectible' => array('Artist'), 'User', 'CollectiblesUpload' => array('Upload'), 'AttributesCollectible' => array('Attribute'))));
                        //update with the new revision id
                        // TODO: this should be added to all uploads, and tags, and artists, etc...I am not sure how much this matter anymore.
                        // I am wonder if instead we do an activity based approach on the collectible itself instead of trying to do this revision stuff.
                        // we have rev tables to show changes that happen between updates but the revision table was suppose to show overall changes.
                        // we are probably better off doing something more useful like an activity table.
                        if (isset($collectible['CollectiblesUpload']) && !empty($collectible['CollectiblesUpload'])) {
                            
                            $this->Collectible->CollectiblesUpload->id = $collectible['CollectiblesUpload'][0]['id'];
                            if (!$this->Collectible->CollectiblesUpload->saveField('revision_id', $collectible['Collectible']['revision_id'])) {
                                //If it fails, let it pass but log the problem.
                                $this->log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
                            }
                            
                            $this->Collectible->CollectiblesUpload->Upload->id = $collectible['CollectiblesUpload'][0]['Upload']['id'];
                            if (!$this->Collectible->CollectiblesUpload->Upload->saveField('revision_id', $collectible['Collectible']['revision_id'])) {
                                //If it fails, let it pass but log the problem.
                                $this->log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
                            }
                            $this->Collectible->CollectiblesUpload->Upload->id = $collectible['CollectiblesUpload'][0]['Upload']['id'];
                            if (!$this->Collectible->CollectiblesUpload->Upload->saveField('status_id', 4)) {
                                //If it fails, let it pass but log the problem.
                                $this->log('Failed to update the upload with the collectible id and revision id (with approval) for collectible ' . $collectible['Collectible']['id'] . ' and upload id ' . $collectible['Upload']['id'], 'error');
                            }
                        }
                        
                        if (isset($collectible['AttributesCollectible']) && !empty($collectible['AttributesCollectible'])) {
                            foreach ($collectible['AttributesCollectible'] as $key => $value) {
                                $this->Collectible->AttributesCollectible->id = $value['id'];
                                if (!$this->Collectible->AttributesCollectible->saveField('revision_id', $collectible['Collectible']['revision_id'])) {
                                    //If it fails, let it pass but log the problem.
                                    $this->log('Failed to update the AttributesCollectible with the revision id (with approval) for collectible ' . $collectible['Collectible']['id'], 'error');
                                }
                                $this->Collectible->AttributesCollectible->Attribute->id = $value['Attribute']['id'];
                                if (!$this->Collectible->AttributesCollectible->Attribute->saveField('status_id', 4)) {
                                    //If it fails, let it pass but log the problem.
                                    $this->log('Failed to update the attribute with the status id of 4 (with approval) for collectible ' . $collectible['Collectible']['id'], 'error');
                                }
                                $this->Collectible->AttributesCollectible->Attribute->id = $value['Attribute']['id'];
                                if (!$this->Collectible->AttributesCollectible->Attribute->saveField('revision_id', $collectible['Collectible']['revision_id'])) {
                                    //If it fails, let it pass but log the problem.
                                    $this->log('Failed to update the attribute with the revision id (with approval) for collectible ' . $collectible['Collectible']['id'], 'error');
                                }
                            }
                        }
                        
                        $this->getEventManager()->dispatch(new CakeEvent('Controller.Activity.add', $this, array('activityType' => ActivityTypes::$ADMIN_APPROVE_NEW, 'user' => $this->getUser(), 'object' => $collectible, 'target' => $collectible, 'type' => 'Collectible')));
                        
                        $this->getEventManager()->dispatch(new CakeEvent('Controller.Collectible.approve', $this, array('approve' => $approvedChange, 'userId' => $collectible['User']['id'], 'collectileId' => $collectible['Collectible']['id'], 'notes' => $notes)));
                        
                        $this->Session->setFlash(__('The collectible was successfully approved.', true), null, null, 'success');
                        $this->redirect(array('admin' => true, 'action' => 'index'), null, true);
                    } else {
                        $this->Session->setFlash(__('There was a problem approving the collectible.', true), null, null, 'error');
                        $this->redirect(array('admin' => true, 'action' => 'view', $id), null, true);
                    }
                } else {
                    $this->Session->setFlash(__('The collectible has been approved already.', true), null, null, 'error');
                    $this->redirect(array('admin' => true, 'action' => 'index'), null, true);
                }
            } else {
                //fuck it, I am deleting it
                if ($this->Collectible->delete($collectible['Collectible']['id'], true)) {
                    //If this fails oh well
                    //TODO: This should be in some callback
                    //Have to do this because we have a belongsTo relationship on Collectible, probably should be a hasOne, fix at some point
                    $this->Collectible->Revision->delete($collectible['Collectible']['revision_id']);
                    //Have to do the same thing with Entity
                    $this->Collectible->EntityType->delete($collectible['Collectible']['entity_type_id']);
                    
                    $this->getEventManager()->dispatch(new CakeEvent('Controller.Collectible.deny', $this, array('approve' => $approvedChange, 'userId' => $collectible['User']['id'], 'collectileId' => $collectible['Collectible']['id'], 'collectible' => $collectible, 'notes' => $notes)));
                    
                    $this->Session->setFlash(__('The collectible was successfully denied.', true), null, null, 'success');
                    $this->redirect(array('admin' => true, 'action' => 'index'), null, true);
                } else {
                    $this->Session->setFlash(__('There was a problem denying the collectible.', true), null, null, 'error');
                    $this->redirect(array('admin' => true, 'action' => 'view', $id), null, true);
                }
            }
        } else {
            $this->Session->setFlash(__('Invalid collectible.', true), null, null, 'error');
            $this->redirect(array('admin' => true, 'action' => 'index'), null, true);
        }
    }
}
?>

