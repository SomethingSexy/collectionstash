<?php
App::uses('Sanitize', 'Utility');
class ManufacturesController extends AppController
{
    
    public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js', 'Time');
    public $components = array('Image');
    
    public function manufacturer($id = null) {
        
        if (!$this->isLoggedIn()) {
            $this->response->statusCode(401);
            return;
        }
        
        if ($this->request->isPost()) {
            // create
            $collectible['Manufacture'] = $this->request->input('json_decode', true);
            $collectible['Manufacture'] = Sanitize::clean($collectible['Manufacture']);
            $response = $this->Manufacture->add($collectible, $this->getUser(), true);
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
            }
            
            $this->set('returnData', $response['response']['data']);
        } else if ($this->request->isPut()) {
            //update
            $collectible['Manufacture'] = $this->request->input('json_decode', true);
            $collectible['Manufacture'] = Sanitize::clean($collectible['Manufacture']);
            $response = $this->Manufacture->update($collectible, $this->getUser(), true);
            if (!$response['response']['isSuccess']) {
                $this->response->statusCode(400);
                $this->set('returnData', $response['response']['data']);
            } else {
                $data = $response['response']['data'];
                if ($data['Upload']) {
                    $thumbnail = $this->Image->image($data['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
                    $data['Upload']['thumbnail_url'] = $thumbnail['path'];
                    $data['Upload']['delete_url'] = '/uploads/remove/' . $data['Upload']['id'] . '/false';
                    $data['Upload']['delete_type'] = 'DELETE';
                    $data['Upload']['pending'] = false;
                    $data['Upload']['allowDelete'] = true;
                }
                $this->set('returnData', $data);
            }
        }
    }
    /**
     * This is the view for the public data
     */
    public function index($id = null) {
        if (isset($id) && is_numeric($id)) {
            $manufacture = $this->Manufacture->find("first", array('conditions' => array('Manufacture.id' => $id), 'contain' => false));
            if (!empty($manufacture)) {
                //TODO: update this to use counter cache once I can add from the app
                $licenseCount = $this->Manufacture->LicensesManufacture->getLicenseCount($id);
                $manufacture['Manufacture']['license_count'] = $licenseCount;
                
                $totalCollectibles = $this->Manufacture->Collectible->getCollectibleCount();
                
                if (is_numeric($totalCollectibles) && $totalCollectibles > 0) {
                    $manCollectibleCount = $manufacture['Manufacture']['collectible_count'];
                    $percentage = round($manCollectibleCount * 100 / $totalCollectibles) . '%';
                    $manufacture['Manufacture']['percentage_of_total'] = $percentage;
                }
                
                $licenses = $this->Manufacture->LicensesManufacture->getLicensesByManufactureId($id);
                $this->set(compact('licenses'));
                
                $highestPriceCollectible = $this->Manufacture->Collectible->find("first", array('limit' => 1, 'conditions' => array('Collectible.status_id' => '4', 'Manufacture.id' => $id), 'order' => array('Collectible.msrp' => 'desc'), 'contain' => array('Manufacture')));
                $lowestPriceCollectible = $this->Manufacture->Collectible->find("first", array('limit' => 1, 'conditions' => array('Collectible.status_id' => '4', 'Manufacture.id' => $id), 'order' => array('Collectible.msrp' => 'asc'), 'contain' => array('Manufacture')));
                
                if (isset($highestPriceCollectible['Collectible'])) {
                    $manufacture['Manufacture']['highest_price'] = $highestPriceCollectible['Collectible']['msrp'];
                    $manufacture['Manufacture']['lowest_price'] = $lowestPriceCollectible['Collectible']['msrp'];
                } else {
                    $manufacture['Manufacture']['highest_price'] = "-";
                    $manufacture['Manufacture']['lowest_price'] = "-";
                }
                
                $lowestEditionCollectible = $this->Manufacture->Collectible->find("first", array('limit' => 1, 'conditions' => array('Collectible.status_id' => '4', 'Manufacture.id' => $id, "not" => array('Collectible.edition_size' => null)), 'order' => array('Collectible.edition_size' => 'asc'), 'contain' => array('Manufacture')));
                $highestEditionCollectible = $this->Manufacture->Collectible->find("first", array('limit' => 1, 'conditions' => array('Collectible.status_id' => '4', 'Manufacture.id' => $id, "not" => array('Collectible.edition_size' => null)), 'order' => array('Collectible.edition_size' => 'desc'), 'contain' => array('Manufacture')));
                if (!empty($lowestEditionCollectible)) {
                    $manufacture['Manufacture']['lowest_edition_size'] = $lowestEditionCollectible['Collectible']['edition_size'];
                } else {
                    $manufacture['Manufacture']['lowest_edition_size'] = "-";
                }
                if (!empty($highestEditionCollectible)) {
                    $manufacture['Manufacture']['highest_edition_size'] = $highestEditionCollectible['Collectible']['edition_size'];
                } else {
                    $manufacture['Manufacture']['highest_edition_size'] = "-";
                }
                
                $this->loadModel('Collectible');
                
                $this->paginate = array('limit' => 25, 'conditions' => array('Collectible.manufacture_id' => $id, 'Collectible.status_id' => 4), 'contain' => array('CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype', 'ArtistsCollectible' => array('Artist')));
                $collectibles = $this->paginate('Collectible');
                
                $this->set(compact('collectibles'));
                
                $this->set(compact('manufacture'));
                
                $this->layout = 'fluid';
            } else {
                $this->redirect("/");
            }
        } else {
            $this->redirect("/");
        }
    }
    
    public function search() {
        $companies = $this->Manufacture->getManufactures();
        $extractCompanies = Set::extract('/Manufacture/.', $companies);
        
        foreach ($extractCompanies as $key => $value) {
            if (isset($companies[$key]['Upload'])) {
                $extractCompanies[$key]['Upload'] = $companies[$key]['Upload'];
                $thumbnail = $this->Image->image($companies[$key]['Upload']['name'], array('uploadDir' => 'files', 'width' => 100, 'height' => 200, 'imagePathOnly' => true));
                $extractCompanies[$key]['Upload']['thumbnail_url'] = $thumbnail['path'];
                $extractCompanies[$key]['Upload']['delete_url'] = '/uploads/remove/' . $companies[$key]['Upload']['id'] . '/false';
                $extractCompanies[$key]['Upload']['delete_type'] = 'DELETE';
                $extractCompanies[$key]['Upload']['pending'] = false;
                $extractCompanies[$key]['Upload']['allowDelete'] = true;
            }
        }
        
        $this->set('companies', $extractCompanies);
        
        $permissions = array();
        
        if ($this->isUserAdmin()) {
            $permissions['edit_manufacturer'] = true;
            $brands = $this->Manufacture->Collectible->License->find('all', array('contain' => false));
            $this->set('brands', $brands);
        } else {
            $permissions['edit_manufacturer'] = false;
        }
        
        $this->set(compact('permissions'));
        
        $this->layout = 'require';
    }
    
    public function data() {
        $this->autoRender = false;
        $query = $this->request->query['query'];
        $manufacturers = $this->Manufacture->find('all', array('fields' => array('Manufacture.id', 'Manufacture.title'), 'contain' => false, 'conditions' => array('Manufacture.title LIKE' => $query . '%')));
        $this->response->body(json_encode(Set::extract('/Manufacture/.', $manufacturers)));
    }
    /*
     * This action will display a list of manufacturers
    */
    public function admin_list() {
        $this->checkLogIn();
        $this->checkAdmin();
        
        $manufacturers = $this->Manufacture->getManufactures();
        
        $this->set(compact('manufacturers'));
        
        $this->layout = 'fluid';
    }
    /**
     * Admin view for manufacturer
     */
    public function admin_view($manufacturer_id = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        if (isset($manufacturer_id) && is_numeric($manufacturer_id)) {
            $manufacture = $this->Manufacture->find("first", array('conditions' => array('Manufacture.id' => $manufacturer_id), 'contain' => false));
            if (!empty($manufacture)) {
                $licenses = $this->Manufacture->LicensesManufacture->getFullLicensesByManufactureId($manufacturer_id);
                $this->set(compact('licenses'));
                
                $this->set(compact('manufacture'));
            } else {
                $this->redirect("/");
            }
        } else {
            $this->redirect("/");
        }
        
        $this->layout = 'fluid';
    }
    
    public function admin_add() {
        $this->checkLogIn();
        $this->checkAdmin();
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                $this->request->data = Sanitize::clean($this->request->data);
                if ($this->Manufacture->save($this->request->data)) {
                    $this->Session->setFlash(__('The manufacturer was successfully added.', true), null, null, 'success');
                    $this->redirect(array('action' => 'view', $this->Manufacture->id));
                } else {
                    $this->Session->setFlash(__('There was a problem adding this manufacturer.', true), null, null, 'error');
                }
            }
        } else {
        }
        
        $this->layout = 'fluid';
    }
    
    public function admin_delete($id) {
        $this->checkLogIn();
        $this->checkAdmin();
        if (is_numeric($id)) {
            $this->request->data = Sanitize::clean($this->request->data);
            if ($this->Manufacture->delete($id)) {
                $this->Session->setFlash(__('The manufacturer was successfully deleted.', true), null, null, 'success');
                $this->redirect(array('action' => 'admin_list'));
            } else {
                $this->Session->setFlash(__('There was a problem deleting this manufacturer.', true), null, null, 'error');
                $this->redirect(array('action' => 'view', $this->Manufacture->id));
            }
        } else {
            $this->redirect(array('action' => 'admin_list'));
        }
        
        $this->layout = 'fluid';
    }
    
    public function admin_edit($manufacturer_id = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        if ($this->request->is('post') || $this->request->is('put')) {
            if (!empty($this->request->data)) {
                $this->request->data = Sanitize::clean($this->request->data);
                if ($this->Manufacture->save($this->request->data)) {
                    $this->Session->setFlash(__('The manufacturer was successfully updated.', true), null, null, 'success');
                    $this->redirect(array('action' => 'view', $this->Manufacture->id));
                } else {
                    $this->Session->setFlash(__('There was a problem updating this manufacturer.', true), null, null, 'error');
                }
            }
        } else {
            if (!isset($manufacturer_id) && !is_numeric($manufacturer_id)) {
                $this->redirect(array('action' => 'list'));
                return;
            }
            $manufacture = $this->Manufacture->find("first", array('conditions' => array('Manufacture.id' => $manufacturer_id), 'contain' => false));
            if (empty($manufacture)) {
                $this->redirect(array('action' => 'list'));
                return;
            }
            $this->request->data = $manufacture;
        }
        
        $this->layout = 'fluid';
    }
    
    public function admin_add_license($manufacturer_id = null) {
        $this->checkLogIn();
        $this->checkAdmin();
        $this->set(compact('manufacturer_id'));
        //at minimum we need a manufacturer id
        if ($this->request->is('post')) {
            if (!empty($this->request->data)) {
                foreach ($this->request->data['LicensesManufacture'] as $key => & $value) {
                    $value['manufacture_id'] = $manufacturer_id;
                }
            }
            if ($this->Manufacture->LicensesManufacture->saveMany($this->request->data['LicensesManufacture'])) {
                $this->Session->setFlash(__('The licenses were successfully associated.', true), null, null, 'success');
                $this->redirect(array('action' => 'view', $manufacturer_id));
            } else {
                $this->Session->setFlash(__('There was a problem associated the licenses to the manufacturer.', true), null, null, 'error');
                $licenses = $this->Manufacture->LicensesManufacture->getLicensesNotAssMan($manufacturer_id);
                $this->set(compact('licenses'));
            }
        } else {
            $licenses = $this->Manufacture->LicensesManufacture->getLicensesNotAssMan($manufacturer_id);
            $this->set(compact('licenses'));
        }
        
        $this->layout = 'fluid';
    }
}
?>