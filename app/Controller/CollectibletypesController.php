<?php
App::uses('Sanitize', 'Utility');
class CollectibletypesController extends AppController
{
    
    public $helpers = array('Html', 'Js', 'Minify', 'Tree');
    
    // Admin pages?
    public function getCollectibletypesData() {
        
        //$this -> render('get_collectibletypes_data', 'json');
        //TODO update this to return the levels and lists
        if (!empty($this->request->data)) {
            $this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
            $manufacturerId = $this->request->data['manufacture_id'];
            $collectibleTypeId = $this->request->data['collectibletype_id'];
            if ($this->request->data['init'] === 'true') {
                
                //This should return arrays of each level of collectibles types from the path of the given collectible type id.  It
                //will also return the collectible that is selected in the array
                $collectibleTypes = $this->Collectibletype->CollectibletypesManufacture->getCollectibleTypesPaths($manufacturerId, $collectibleTypeId);
            } else {
                $collectibleTypes = $this->Collectibletype->CollectibletypesManufacture->getCollectibleTypesChildren($manufacturerId, $collectibleTypeId);
                
                //$collectibleTypes = $this -> Collectibletype -> children($collectibleTypeId, true, array('Collectibletype.id', 'Collectibletype.name'));
                
            }
            
            $data = array();
            $data['success'] = array('isSuccess' => true);
            $data['isTimeOut'] = false;
            $data['data'] = array();
            $data['data']['collectibleTypes'] = $collectibleTypes;
            
            // $data['data']['specializedTypes'] = $specializedTypes;
            $this->set('aCollectibleTypesData', $data);
        } else {
            $this->set('aCollectibleTypesData', array('success' => array('isSuccess' => false), 'isTimeOut' => false));
        }
    }
    
    public function data() {
        $this->autoRender = false;
        $query = $this->request->query['query'];
        $collectibletypes = $this->Collectibletype->find('all', array('fields' => array('Collectibletype.id', 'Collectibletype.name'), 'contain' => false, 'conditions' => array('Collectibletype.name LIKE' => $query . '%')));
        $this->response->body(json_encode(Set::extract('/Collectibletype/.', $collectibletypes)));
    }
    
    public function admin_list() {
        $collectibletypes = $this->Collectibletype->find('all', array('contain' => false, 'fields' => array('name', 'lft', 'rght', 'id'), 'order' => 'lft ASC'));
        $this->set(compact('collectibletypes'));
        
        $this->layout = 'fluid';
    }
    
    public function admin_add() {
        $invalidRequest = false;
        $invalidSave = false;
        $invalidPost = false;
        $isSuccess = false;
        if ($this->isLoggedIn() && $this->isUserAdmin()) {
            
            //Make sure it a post, if not don't accept it
            if ($this->request->is('post')) {
                if (!empty($this->request->data)) {
                    if ($this->Collectibletype->save($this->request->data)) {
                        $isSuccess = true;
                    } else {
                        $invalidSave = true;
                    }
                } else {
                    $invalidPost = true;
                }
            } else {
                $invalidPost = true;
            }
        } else {
            $invalidRequest = true;
        }
        if ($this->request->isAjax()) {
            $data = array();
            if ($invalidSave) {
                $data['success'] = array('isSuccess' => false);
                $data['isTimeOut'] = false;
                $data['data'] = array();
                $data['errors'] = array($this->Collectibletype->validationErrors);
            } else if ($invalidPost) {
                $data['success'] = array('isSuccess' => false);
                $data['isTimeOut'] = false;
                $data['data'] = array();
                $data['errors'][0] = array('invalidRequest' => 'The request was invalid.');
            } else if ($invalidRequest) {
                
                //If they are not logged in and are trying to access this then just time them out
                $data['success'] = array('isSuccess' => false);
                $data['isTimeOut'] = true;
                $data['data'] = array();
            } else {
                
                //successful
                $data['success'] = array('isSuccess' => true);
                $data['isTimeOut'] = false;
                $data['data'] = array('id' => $this->Collectibletype->id);
            }
            
            //better way to handle this?
            $this->set('aCollecibletype', $data);
            $this->render('admin_add_ajax');
        } else {
            if ($isSuccess) {
                $this->redirect(array('action' => 'list'));
            }
        }
        
        $this->layout = 'fluid';
    }
    
    public function admin_remove() {
        $data = array();
        if ($this->isLoggedIn() && $this->isUserAdmin()) {
            if (!empty($this->request->data) && $this->request->is('post')) {
                $this->Collectibletype->id = $this->request->data['Collectibletype']['id'];
                if ($this->Collectibletype->delete()) {
                    $data['success'] = array('isSuccess' => true);
                    $data['isTimeOut'] = false;
                    $data['data'] = array();
                } else {
                    $data['success'] = array('isSuccess' => false);
                    $data['isTimeOut'] = false;
                    $data['errors'] = array($this->Collectibletype->validationErrors);
                }
            } else {
                $data['success'] = array('isSuccess' => false);
                $data['isTimeOut'] = false;
                $data['data'] = array();
                $data['errors'][0] = array('invalidRequest' => 'The request was invalid.');
            }
        } else {
            $data['success'] = array('isSuccess' => false);
            $data['isTimeOut'] = false;
            $data['data'] = array();
            $data['errors'][0] = array('invalidRequest' => 'The request was invalid.');
        }
        
        $this->set('aCollecibletype', $data);
    }
}
?>