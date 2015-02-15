<?php
/**
 * This is the main controller used for all interactions with an attribute
 *
 * When trying to delete an attribute and we want to link it to an existin collectible
 *
 * Let's add the ability to search on a collectible and select an attribute from
 * that collectible
 */
App::uses('Sanitize', 'Utility');
class AttributesController extends AppController
{
    //'CollectibleDetail' should really be named Field Helper or something
    public $helpers = array('Html', 'Js', 'Minify', 'Tree', 'CollectibleDetail', 'FileUpload.FileUpload');
    
    function part($id = null) {
        $this->autoRender = false;
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        
        if ($this->request->isPost()) { // create
            $this->response->statusCode(401);
            return;
        } else if ($this->request->isPut()) { //update
            $part['Attribute'] = $this->request->input('json_decode', true);
            $part['Attribute'] = Sanitize::clean($part['Attribute']);
            $response = $this->Attribute->update($part, $this->getUser());
            
            if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                $this->response->statusCode(401);
            } else if (!$response['response']['isSuccess'] && $response['response']['code'] === 400) {
                $this->response->statusCode(400);
                $this->response->body(json_encode($response['response']['data']));
            } else if (!$response['response']['isSuccess']) {
                $this->response->statusCode(500);
            } else {
                $successResponse = $response['response']['data'];
                if ($successResponse['isEdit']) {
                    $successResponse['isNew'] = false;
                }
                // return the data which should be the full part
                $this->response->body(json_encode($successResponse));
            }
        } else if ($this->request->isDelete()) {
            // on the delete, data is coming from data
            $part['Attribute'] = Sanitize::clean($this->request->data);
            $response = $this->Attribute->remove($part, $this->getUser());
            
            if (!$response['response']['isSuccess'] && $response['response']['code'] === 401) {
                $this->response->statusCode(401);
            } else if (!$response['response']['isSuccess'] && $response['response']['code'] === 400) {
                $this->response->statusCode(400);
                $this->response->body(json_encode($response['response']['data']));
            } else {
                $this->response->body(json_encode($response['response']['data']));
            }
        } else {
            //assume GET?
            // not bothering with the response stuff here for just a get
            $response = $this->AttributesCollectible->get($id);
            $this->set('returnData', $response);
        }
    }
    /**
     * This will be the main view page to view all of the details of
     * an attribute
     */
    public function view($id = null) {
        if (is_null($id) || !is_numeric($id)) {
            $this->Session->setFlash(__('Invalid collectible', true));
            $this->redirect(array('action' => 'index'));
        }
        
        $attribute = $this->Attribute->find('first', array('conditions' => array('Attribute.id' => $id), 'contain' => array('Manufacture', 'Scale', 'Artist', 'AttributeCategory', 'Status', 'User', 'AttributesUpload' => array('Upload'))));
        
        $attributeCollectibles = $this->Attribute->AttributesCollectible->find('all', array('joins' => array(array('alias' => 'Collectible2', 'table' => 'collectibles', 'type' => 'inner', 'conditions' => array('Collectible2.id = AttributesCollectible.collectible_id', 'Collectible2.status_id = "4"'))), 'conditions' => array('AttributesCollectible.attribute_id' => $id), 'contain' => array('Collectible' => array('CollectiblesUpload' => array('Upload')))));
        $attribute['AttributesCollectible'] = $attributeCollectibles;
        
        if (!empty($attribute) && $attribute['Attribute']['status_id'] === '4') {
            $this->set(compact('attribute'));
            $this->layout = 'fluid';
        } else {
            $this->render('viewMissing');
        }
    }
    
    /**
     * Attributes can be added at two places
     *  - Stand alone
     *  - Or when being added new when adding a collectible
     *
     *  - When they are being directly attached to a collectible, if the attribute is denied then we will want to automatically delete the link
     */
    function admin_approve($id = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        $this->request->data = Sanitize::clean($this->request->data);
        if ($id && is_numeric($id) && isset($this->request->data['Approval']['approve'])) {
            $response = $this->Attribute->approve($id, $this->request->data, $this->getUserId());
            if ($response['response']['isSuccess'] === true) {
                if ($response['response']['code'] === 1) {
                    $this->Session->setFlash(__('The item was successfully approved.', true), null, null, 'success');
                } else if ($response['response']['code'] === 2) {
                    $this->Session->setFlash(__('The item was successfully denied.', true), null, null, 'success');
                }
                $this->redirect(array('admin' => true, 'action' => 'index'), null, true);
            } else {
                $this->Session->setFlash(__('There was a problem approving the item.', true), null, null, 'error');
                $this->redirect(array('admin' => true, 'action' => 'view', $id), null, true);
            }
        } else {
            $this->Session->setFlash(__('Invalid item.', true), null, null, 'error');
            $this->redirect(array('admin' => true, 'action' => 'index'), null, true);
        }
    }
    
    function admin_approval($editId = null, $attributeEditId = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        if ($editId && is_numeric($editId) && $attributeEditId && is_numeric($attributeEditId)) {
            $this->set('$attributeEditId', $attributeEditId);
            $this->set('editId', $editId);
            if (empty($this->request->data)) {
                // Remember that the "Attribute" being returned from here is really the AttributeEdit, so we need to
                // use the base_id
                $attribute = $this->Attribute->getEditForApproval($attributeEditId);
                debug($attribute);
                if ($attribute) {
                    // We also want to find all collectibles that this attribute is currently tied too
                    // Because this is an edit we want the base id
                    if (isset($attribute['AttributeEdit'])) {
                        $attributesCollectible = $this->Attribute->AttributesCollectible->find("all", array('conditions' => array('AttributesCollectible.attribute_id' => $attribute['AttributeEdit']['base_id'])));
                    } else {
                        $attributesCollectible = $this->Attribute->AttributesCollectible->find("all", array('conditions' => array('AttributesCollectible.attribute_id' => $attribute['Attribute']['base_id'])));
                    }
                    
                    debug($attributesCollectible);
                    $this->set(compact('attributesCollectible'));
                    $this->set(compact('attribute'));
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
            
            $response = $this->Attribute->validateAttrbitue($this->request->data);
            
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
