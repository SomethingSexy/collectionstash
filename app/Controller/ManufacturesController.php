<?php
App::uses('Sanitize', 'Utility');
class ManufacturesController extends AppController {

    public $helpers = array('Html', 'Js', 'Minify');

    /**
     * This is the view for the public data
     */
    public function view($id = null) {
        if (isset($id) && is_numeric($id)) {
            $manufacture = $this -> Manufacture -> find("first", array('conditions' => array('Manufacture.id' => $id), 'contain' => false));
            if (!empty($manufacture)) {
                //TODO: update this to use counter cache once I can add from the app
                $licenseCount = $this -> Manufacture -> LicensesManufacture -> getLicenseCount($id);
                $manufacture['Manufacture']['license_count'] = $licenseCount;
                $collectibletypeCount = $this -> Manufacture -> CollectibletypesManufacture -> getCollectibletypeCount($id);
                $manufacture['Manufacture']['collectibletype_count'] = $collectibletypeCount;
                $totalCollectibles = $this -> Manufacture -> Collectible -> getCollectibleCount();

                if (is_numeric($totalCollectibles) && $totalCollectibles > 0) {
                    $manCollectibleCount = $manufacture['Manufacture']['collectible_count'];
                    $percentage = round($manCollectibleCount * 100 / $totalCollectibles) . '%';
                    $manufacture['Manufacture']['percentage_of_total'] = $percentage;
                }

                $licenses = $this -> Manufacture -> LicensesManufacture -> getLicensesByManufactureId($id);
                $this -> set(compact('licenses'));

                $manufacturerCollectibletypes = $this -> Manufacture -> CollectibletypesManufacture -> find('all', array('conditions' => array('CollectibletypesManufacture.manufacture_id' => $id)));

                $highestPriceCollectible = $this -> Manufacture -> Collectible -> find("first", array('limit' => 1, 'conditions' => array('Collectible.state' => '0', 'Manufacture.id' => $id), 'order' => array('Collectible.msrp' => 'desc'), 'contain' => array('Manufacture')));
                $lowestPriceCollectible = $this -> Manufacture -> Collectible -> find("first", array('limit' => 1, 'conditions' => array('Collectible.state' => '0', 'Manufacture.id' => $id), 'order' => array('Collectible.msrp' => 'asc'), 'contain' => array('Manufacture')));
                $manufacture['Manufacture']['highest_price'] = $highestPriceCollectible['Collectible']['msrp'];
                $manufacture['Manufacture']['lowest_price'] = $lowestPriceCollectible['Collectible']['msrp'];

                $lowestEditionCollectible = $this -> Manufacture -> Collectible -> find("first", array('limit' => 1, 'conditions' => array('Collectible.state' => '0', 'Manufacture.id' => $id, "not" => array('Collectible.edition_size' => null)), 'order' => array('Collectible.edition_size' => 'asc'), 'contain' => array('Manufacture')));
                $highestEditionCollectible = $this -> Manufacture -> Collectible -> find("first", array('limit' => 1, 'conditions' => array('Collectible.state' => '0', 'Manufacture.id' => $id, "not" => array('Collectible.edition_size' => null)), 'order' => array('Collectible.edition_size' => 'desc'), 'contain' => array('Manufacture')));
                if (!empty($lowestEditionCollectible)) {
                    $manufacture['Manufacture']['lowest_edition_size'] = $lowestEditionCollectible['Collectible']['edition_size'];
                }
                if (!empty($highestEditionCollectible)) {
                    $manufacture['Manufacture']['highest_edition_size'] = $highestEditionCollectible['Collectible']['edition_size'];
                }

                $this -> set(compact('manufacture'));
            } else {
                $this -> redirect("/");
            }

        } else {
            $this -> redirect("/");
        }
    }

    /*
     * This action will display a list of manufacturers
     */
    public function admin_list() {
        $this -> checkLogIn();
        $this -> checkAdmin();

        $manufacturers = $this -> Manufacture -> getManufactures();

        $this -> set(compact('manufacturers'));
    }

    /**
     * Admin view for manufacturer
     */
    public function admin_view($manufacturer_id = null) {
        $this -> checkLogIn();
        $this -> checkAdmin();
        if (isset($manufacturer_id) && is_numeric($manufacturer_id)) {
            $manufacture = $this -> Manufacture -> find("first", array('conditions' => array('Manufacture.id' => $manufacturer_id), 'contain' => false));
            if (!empty($manufacture)) {
                $licenses = $this -> Manufacture -> LicensesManufacture -> getFullLicensesByManufactureId($manufacturer_id);
                $this -> set(compact('licenses'));

                $collectibletypes = $this -> Manufacture -> CollectibletypesManufacture -> getAllCollectibleTypeByManufactureId($manufacturer_id);
                $this -> set(compact('collectibletypes'));

                $this -> set(compact('manufacture'));
            } else {
               $this -> redirect("/");
            }

        } else {
           $this -> redirect("/");
        }
    }

    public function admin_add() {
        $this -> checkLogIn();
        $this -> checkAdmin();
        if ($this -> request -> is('post') || $this -> request -> is('put')) {
            if (!empty($this -> request -> data)) {
                $this -> request -> data = Sanitize::clean($this -> request -> data);
                if ($this -> Manufacture -> save($this -> request -> data)) {
                    $this -> Session -> setFlash(__('The manufacturer was successfully added.', true), null, null, 'success');
                    $this -> redirect(array('action' => 'view', $this -> Manufacture -> id));
                } else {
                    $this -> Session -> setFlash(__('There was a problem adding this manufacturer.', true), null, null, 'error');
                }
            }
        } else {

        }
    }

    public function admin_edit($manufacturer_id = null) {
        $this -> checkLogIn();
        $this -> checkAdmin();
        if ($this -> request -> is('post') || $this -> request -> is('put')) {
            if (!empty($this -> request -> data)) {
                $this -> request -> data = Sanitize::clean($this -> request -> data);
                if ($this -> Manufacture -> save($this -> request -> data)) {
                    $this -> Session -> setFlash(__('The manufacturer was successfully updated.', true), null, null, 'success');
                    $this -> redirect(array('action' => 'view', $this -> Manufacture -> id));
                } else {
                    $this -> Session -> setFlash(__('There was a problem updating this manufacturer.', true), null, null, 'error');
                }
            }
        } else {
            if (!isset($manufacturer_id) && !is_numeric($manufacturer_id)) {
                $this -> redirect(array('action' => 'list'));
                return;
            }
            $manufacture = $this -> Manufacture -> find("first", array('conditions' => array('Manufacture.id' => $manufacturer_id), 'contain' => false));
            if (empty($manufacture)) {
                $this -> redirect(array('action' => 'list'));
                return;
            }
            $this -> request -> data = $manufacture;
        }
    }

    public function admin_add_license($manufacturer_id = null) {
        $this -> checkLogIn();
        $this -> checkAdmin();
        $this -> set(compact('manufacturer_id'));
        //at minimum we need a manufacturer id
        if ($this -> request -> is('post')) {
            if (!empty($this -> request -> data)) {
                foreach ($this -> request -> data['LicensesManufacture'] as $key => &$value) {
                    $value['manufacture_id'] = $manufacturer_id;
                }
            }
            if ($this -> Manufacture -> LicensesManufacture -> saveMany($this -> request -> data['LicensesManufacture'])) {
                $this -> Session -> setFlash(__('The licenses were successfully associated.', true), null, null, 'success');
                $this -> redirect(array('action' => 'view', $manufacturer_id));
            } else {
                $this -> Session -> setFlash(__('There was a problem associated the licenses to the manufacturer.', true), null, null, 'error');
                $licenses = $this -> Manufacture -> LicensesManufacture -> getLicensesNotAssMan($manufacturer_id);
                $this -> set(compact('licenses'));
            }
        } else {
            $licenses = $this -> Manufacture -> LicensesManufacture -> getLicensesNotAssMan($manufacturer_id);
            $this -> set(compact('licenses'));
        }
    }

    public function admin_add_collectibletype($manufacturer_id = null){
        $this -> checkLogIn();
        $this -> checkAdmin();
        $this -> set(compact('manufacturer_id'));
        //at minimum we need a manufacturer id
        if ($this -> request -> is('post')) {
            if (!empty($this -> request -> data)) {
                foreach ($this -> request -> data['CollectibletypesManufacture'] as $key => &$value) {
                    $value['manufacture_id'] = $manufacturer_id;
                }
            }
            if ($this -> Manufacture -> CollectibletypesManufacture -> saveMany($this -> request -> data['CollectibletypesManufacture'])) {
                $this -> Session -> setFlash(__('The collectible type were successfully associated.', true), null, null, 'success');
                $this -> redirect(array('action' => 'view', $manufacturer_id));
            } else {
                $this -> Session -> setFlash(__('There was a problem associated the collectible type to the manufacturer.', true), null, null, 'error');
                $collectibletypes = $this -> Manufacture -> CollectibletypesManufacture -> getCollectibleTypesNotAssMan($manufacturer_id);
                $this -> set(compact('collectibletypes'));
            }
        } else {
            $collectibletypes = $this -> Manufacture -> CollectibletypesManufacture -> getCollectibleTypesNotAssMan($manufacturer_id);
            debug($collectibletypes);
            $this -> set(compact('collectibletypes'));
        }        
    }

}
?>