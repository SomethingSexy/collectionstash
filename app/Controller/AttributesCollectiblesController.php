<?php
/**
 * We will now use this controller for all adding, editing and removing.
 *
 * We will now only be able to edit, add, remove one at a time from the UI
 *
 * This should make the controller code a lot less compliated
 *
 * however, this will have to handle...adding brand new attributes which will have to
 * add a new Attribute (for approval) and add a new attribute collectible which will be an edit
 * - If the attribute collectible is approved then we will automatically approve the attributr
 *  - If the attribute collectible is denied then we will automatically delete the attribute as well
 *
 * - Update will be standard, just updating the count for now
 *
 * - Removing
 *      - Will indicate if we are just remove the link or removing the link and deleting the attribute
 *
 *  To handle duplicates through this UI,  you would delete the link and the attribute and then add the new one
 *
 *
 */
App::uses('Sanitize', 'Utility');
class AttributesCollectiblesController extends AppController
{
    public $helpers = array('Html', 'Js', 'Minify', 'Tree', 'CollectibleDetail');
    // TODO: this needs to go away in favor of part method
    function attribute($id) {
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        
        if ($this->request->isPost()) {
            // create
            $this->response->statusCode(401);
            return;
        } else if ($this->request->isPut()) {
            //update
            $this->response->statusCode(401);
            return;
        } else {
            //assume GET?
            // not bothering with the response stuff here for just a get
            $response = $this->AttributesCollectible->get($id);
            $this->set('returnData', $response);
        }
    }
    
    function part($id) {
        $this->autoRender = false;
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        // create
        if ($this->request->isPost()) {
            $part = $this->request->input('json_decode', true);
            
            $response = $this->AttributesCollectible->add($part, $this->getUser());
            if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                $this->response->statusCode(401);
            } else if (!$response['response']['isSuccess'] && $response['response']['code'] === 400) {
                $this->response->statusCode(400);
                $this->response->body(json_encode($response['response']['data']));
            } else {
                $successResponse = $response['response']['data'];
                if ($successResponse['isEdit']) {
                    $successResponse['isNew'] = true;
                }
                // return the data which should be the full part
                $this->response->body(json_encode($successResponse));
            }
        } else if ($this->request->isPut()) {
            //update
            $part['AttributesCollectible'] = $this->request->input('json_decode', true);
            $part['AttributesCollectible'] = Sanitize::clean($part['AttributesCollectible']);
            $response = $this->AttributesCollectible->update($part, $this->getUser());
            
            if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                $this->response->statusCode(401);
            } else if (!$response['response']['isSuccess'] && $response['response']['code'] === 400) {
                $this->response->statusCode(400);
                $this->response->body(json_encode($response['response']['data']));
            } else {
                
                $successResponse = array('isEdit' => $response['response']['data']['isEdit']);
                if ($successResponse['isEdit']) {
                    $successResponse['isNew'] = false;
                }
                
                $this->response->body(json_encode($successResponse));
            }
        } else if ($this->request->isDelete()) {
            $part['AttributesCollectible'] = $this->request->input('json_decode', true);
            $part['AttributesCollectible'] = Sanitize::clean($part['AttributesCollectible']);
            $part['AttributesCollectible']['id'] = $id;
            
            $response = $this->AttributesCollectible->remove($part, $this->getUser());
            
            if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                $this->response->statusCode(401);
            } else if (!$response['response']['isSuccess'] && $response['response']['code'] === 400) {
                $this->response->statusCode(400);
                $this->response->body(json_encode($response['response']['data']));
            } else {
                $this->response->body(json_encode(array('isEdit' => $response['response']['data']['isEdit'])));
            }
        } else if ($this->request->isGet()) {
            debug($id);
            $response = $this->AttributesCollectible->get($id);
            debug($response);
            $part = Set::extract('/AttributesCollectible/.', $response);
            $part['Attribute'] = $response['Attribute'];
            $part['Revision'] = $response['Revision'];
            $this->response->body(json_encode($part));
        }
    }
    /**
     * This method will return the history for a given attributes collectible
     */
    function history($id = null) {
        $this->checkLogIn();
        if ($id && is_numeric($id)) {
            //Date and timestamp of update and user who did the update
            $this->AttributesCollectible->id = $id;
            $history = $this->AttributesCollectible->revisions(null, true);
            //As of 9/7/11, because of the way we have to add an attributes collectible, the first revision is going to be bogus.
            //Pop it off here until we can update the revision behavior so that we can specific a save to not add a revision.
            $lastHistory = end($history);
            if ($lastHistory['AttributesCollectible']['revision_id'] === '0') {
                array_pop($history);
            }
            reset($history);
            debug($history);
            $this->set(compact('history'));
        } else {
            $this->redirect($this->referer());
        }
    }
    /**
     *
     */
    public function admin_approval($editId = null, $attributeEditId = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        if ($editId && is_numeric($editId) && $attributeEditId && is_numeric($attributeEditId)) {
            $this->set('attributeEditId', $attributeEditId);
            $this->set('editId', $editId);
            if (empty($this->request->data)) {
                $attribute = $this->AttributesCollectible->getEditForApproval($attributeEditId);
                debug($attribute);
                if ($attribute) {
                    // method does not return deeper associaions, need to fix that at some point
                    $attributeCategory = $this->AttributesCollectible->Attribute->AttributeCategory->find("first", array('contain' => false, 'conditions' => array('AttributeCategory.id' => $attribute['Attribute']['attribute_category_id'])));
                    
                    $attribute['Attribute']['AttributeCategory'] = $attributeCategory['AttributeCategory'];
                    $manufacturer = $this->AttributesCollectible->Attribute->Manufacture->find("first", array('contain' => false, 'conditions' => array('Manufacture.id' => $attribute['Attribute']['manufacture_id'])));
                    $attribute['Attribute']['Manufacture'] = $manufacturer['Manufacture'];
                    $scale = $this->AttributesCollectible->Attribute->Scale->find("first", array('contain' => false, 'conditions' => array('Scale.id' => $attribute['Attribute']['scale_id'])));
                    $attribute['Attribute']['Scale'] = $scale['Scale'];
                    $status = $this->AttributesCollectible->Attribute->Status->find("first", array('contain' => false, 'conditions' => array('Status.id' => $attribute['Attribute']['status_id'])));
                    $attribute['Attribute']['Status'] = $status['Status'];
                    // we also want to see any collectibles currently linked to this item
                    debug($attribute['Attribute']['id']);
                    $attributesCollectible = $this->AttributesCollectible->find("all", array('conditions' => array('Attribute.id' => $attribute['Attribute']['id'])));
                    debug($attributesCollectible);
                    $this->set(compact('attributesCollectible'));
                    
                    $this->set(compact('attribute'));
                    //TODO: this should be a helper or something to get all of the data necessary to render the add attribute window
                    $attributeCategories = $this->AttributesCollectible->Attribute->AttributeCategory->find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id', 'path_name'), 'order' => 'lft ASC'));
                    $this->set(compact('attributeCategories'));
                    
                    $scales = $this->AttributesCollectible->Attribute->Scale->find("list", array('fields' => array('Scale.id', 'Scale.scale'), 'order' => array('Scale.scale' => 'ASC')));
                    $this->set(compact('scales'));
                    
                    $manufactures = $this->AttributesCollectible->Attribute->Manufacture->getManufactureList();
                    $this->set(compact('manufactures'));
                } else {
                    //uh fuck you
                    $this->redirect('/');
                }
            }
        } else {
            $this->redirect('/');
        }
    }
    
    public function isValid() {
        if (!$this->isLoggedIn()) {
            $data['response'] = array();
            $data['response']['isSuccess'] = false;
            $error = array('message' => __('You must be logged in to update this item.'));
            $error['inline'] = false;
            $data['response']['errors'] = array();
            array_push($data['response']['errors'], $error);
            $this->set('returnData', $data);
            return;
        }
        
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data = Sanitize::clean($this->request->data);
            
            $response = $this->AttributesCollectible->validateAttrbitue($this->request->data);
            
            if ($response) {
                $this->set('returnData', $response);
            } else {
                //Something really fucked up
                $data['isSuccess'] = false;
                $data['errors'] = array('message', __('Invalid request.'));
                $this->set('returnData', $data);
            }
        } else {
            $data['isSuccess'] = false;
            $data['errors'] = array('message', __('Invalid request.'));
            $this->set('returnData', $data);
            return;
        }
    }
}
?>