<?php
App::uses('Sanitize', 'Utility');
class CollectibletypesController extends AppController
{
    
    public $helpers = array('Html', 'Js', 'Minify', 'Tree');
    
    
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