<?php
class Manufacture extends AppModel
{
    public $name = 'Manufacture';
    public $belongsTo = array('Series', 'Upload' => array('counterCache' => true));
    public $hasMany = array('Collectible' => array('className' => 'Collectible', 'foreignKey' => 'manufacture_id', 'dependent' => true), 'LicensesManufacture' => array('dependent' => true));
    public $actsAs = array('Containable', 'Sluggable' => array(
    /**
     * Ok so I want to build slugs on the fly instead of a database field, cause then I would
     * have to worry about updates and shit...
     *
     * The problem is, the slug I want to build for this one has associations i want to bind,
     * so I am thinking I set those below like so to grab those associations.  If the first one
     * in the arry is not "Model", then do it on the model alias
     */
    'displayField' => array('field1' => array('Model' => 'Manufacture', 'Field' => 'title')), 'showPrimary' => false,
    // 'slugField' => 'theNameOfYourSlugVirtualField',
    'replacement' => '-'
    //the char to implode the words in entry name...
    ));
    
    public $validate = array(
    //name field
    'title' => array('rule' => '/^[\\w\\s-.:&#]+$/', 'required' => true, 'message' => 'Invalid characters'),
    //series_id
    'series_id' => array('rule' => array('numeric'), 'allowEmpty' => true, 'message' => 'Please select a valid category.'),
    //url
    'url' => array('rule' => 'url', 'allowEmpty' => true, 'message' => 'Must be a valid url.'),
    // bio
    'bio' => array('maxLength' => array('rule' => array('maxLength', 5000), 'allowEmpty' => true, 'message' => 'Company bio must be less than 5000 characters.'), 'allowedCharacters' => array('rule' => '/^[a-z0-9\s\r\n ?&$%#@!*()+_\\\\#\':.,"\/-]+$/i', 'message' => 'Company bio has invalid characters')));

    function afterFind($results, $primary = false) {
        if ($results) {
            // If it is primary handle all of these things
            if ($primary) {
                foreach ($results as $key => $val) {
                    if (isset($val['Manufacture'])) {
                        if (!isset($val['Manufacture']['upload_id']) || (isset($val['Manufacture']['upload_id']) && is_null($val['Manufacture']['upload_id']))) {
                            unset($results[$key]['Upload']);
                        }
                        
                        $name = strtolower($val['Manufacture']['title']);
                        $slug = str_replace(' ', '-', $name);
                        $results[$key]['Manufacture']['slug'] = $slug;
                    }
                }
            }
        }
        
        return $results;
    }
    
    public function beforeDelete($cascade = true) {
        // delete the series
        $manufacturer = $this->find('first', array('conditions' => array('Manufacture.id' => $this->id), 'contain' => false));
        
        if ($manufacturer && !empty($manufacturer['Manufacture']['series_id'])) {
            if (!$this->Series->delete($manufacturer['Manufacture']['series_id'])) {
                return false;
            }
        }
        
        return true;
    }
    
    public function add($data, $user, $autoUpdate = false) {
        $retVal = $this->buildDefaultResponse();
        
        if (isset($data['Manufacture']['LicensesManufacture']) && !empty($data['Manufacture']['LicensesManufacture'])) {
            //array of brand names
            $brands = $data['Manufacture']['LicensesManufacture'];
            $brandMans['LicensesManufacture'] = array();
            foreach ($brands as $key => $value) {
                $brand = $this->LicensesManufacture->processLicense($value, $user['User']['id']);
                array_push($brandMans['LicensesManufacture'], $brand);
            }
            $data['LicensesManufacture'] = $brandMans['LicensesManufacture'];
        }
        
        unset($data['Manufacture']['LicensesManufacture']);
        // Also by default let's add a series
        $data['Series'] = array();
        $data['Series']['name'] = $data['Manufacture']['title'];
        $data['Series']['parent_id'] = null;
        $data['Manufacture']['user_id'] = $user['User']['id'];
        
        if ($this->saveAll($data, array('deep' => true))) {
            $id = $this->id;
            $manufacturer = $this->find('first', array('contain' => array('Upload', 'LicensesManufacture' => array('License')), 'conditions' => array('Manufacture.id' => $id)));
            
            $retVal['response']['data'] = $manufacturer['Manufacture'];
            $retVal['response']['data']['LicensesManufacture'] = $manufacturer['LicensesManufacture'];
            if (isset($manufacturer['Upload'])) {
                $retVal['response']['data']['Upload'] = $manufacturer['Upload'];
            }
            $retVal['response']['isSuccess'] = true;
        } else {
            $retVal['response']['isSuccess'] = false;
            $retVal['response']['data'] = $this->validationErrors;
        }
        
        return $retVal;
    }
    
    public function update($data, $user, $autoUpdate = false) {
        $retVal = $this->buildDefaultResponse();
        
        if (isset($data['Manufacture']['LicensesManufacture']) && !empty($data['Manufacture']['LicensesManufacture'])) {
            //array of brand names
            $brands = $data['Manufacture']['LicensesManufacture'];
            $brandMans['LicensesManufacture'] = array();
            foreach ($brands as $key => $value) {
                $brand = $this->LicensesManufacture->processLicense($value, $user['User']['id']);
                array_push($brandMans['LicensesManufacture'], $brand);
            }
            $data['LicensesManufacture'] = $brandMans['LicensesManufacture'];
        }
        
        unset($data['Manufacture']['LicensesManufacture']);
        
        if ($this->saveAll($data, array('deep' => true))) {
            $id = $this->id;
            $manufacturer = $this->find('first', array('contain' => array('Upload', 'LicensesManufacture' => array('License')), 'conditions' => array('Manufacture.id' => $id)));
            
            $retVal['response']['data'] = $manufacturer['Manufacture'];
            if (isset($manufacturer['Upload'])) {
                $retVal['response']['data']['Upload'] = $manufacturer['Upload'];
            }
            $retVal['response']['data']['LicensesManufacture'] = $manufacturer['LicensesManufacture'];
            $retVal['response']['isSuccess'] = true;
        } else {
            $retVal['response']['isSuccess'] = false;
            $retVal['response']['data'] = $this->validationErrors;
        }
        
        return $retVal;
    }
    /**
     * This should be the main find for a manufacturer, it will handle caching eventually.
     */
    public function findByManufacturerId($id) {
        return $this->find('first', array('conditions' => array('Manufacture.id' => $id), 'contain' => false));
    }
    
    public function getManufactureList() {
        return $this->find('list', array('order' => array('Manufacture.title' => 'ASC')));
    }
    /**
     * This method will return all manufacturers, in array form.  It will return all manufacturer specific data
     * but no associated data.
     */
    public function getManufactures() {
        return $this->find('all', array('contain' => array('Upload'), 'order' => array('Manufacture.title' => 'ASC')));
    }
    
    public function getManufactureNameById($manufactureId) {
        $manufacture = $this->find('first', array('conditions' => array('Manufacture.id' => $manufactureId), 'fields' => array('Manufacture.title'), 'contain' => false));
        return $manufacture['Manufacture']['title'];
    }
    
    public function getManufactureSearchData() {
        $manufactures = $this->find("all", array('order' => array('Manufacture.title' => 'ASC'), 'contain' => false));
        return $manufactures;
    }
    /**
     * Given a manufactureId and a license Id, this method returns all of the series
     * for that combination
     */
    public function getSeries($manufactureId) {
        $series = $this->find('all', array('conditions' => array('Manufacture.id' => $manufactureId), 'contain' => array('Series'), 'fields' => array('Series.name', 'Series.id')));
        
        $seriesList = array();
        
        foreach ($series as $serie) {
            $seriesList[$serie['Series']['id']] = $serie['Series']['name'];
        }
        
        return $seriesList;
    }
    /**
     * TODO: Do I still need this?
     */
    public function getSeriesLevels($manufactureId, $seriesId = null) {
        /*
         * Grab this everytime because we are going to need it
        */
        // $licenseManufacturer = $this -> LicensesManufacture -> getLicenseManufacture($manufactureId, $licenseId);
        // debug($licenseManufacturer);
        $returnData = array();
        $returnData['selected'] = array();
        /*
         * Check to make sure we returned something, or something was found
         *
         *
         * We are returning the level count so it is easier for the front end to
         * handle this
        */
        if (!is_null($manufactureId) && !empty($manufactureId)) {
            /*
             * If the series id is null then we want to get the main level
            */
            if (is_null($seriesId)) {
                $series = $this->getSeries($manufactureId);
                /*
                 * Since we are returning the top layer, set it as series L0
                */
                $returnData['L0'] = $series;
                
                $returnData['levelCount'] = 1;
            } else {
                /*
                 * If it is not null then we need to get the level
                */
                $paths = $this->Series->getPath($seriesId, array(), true);
                $lastKey = 0;
                foreach ($paths as $key => $value) {
                    $processedSeries = array();
                    /*
                     * If there is no parent and in most cases this should be the first one and only
                     * one, just get all of the main level series for this one
                    */
                    if (is_null($value['Series']['parent_id'])) {
                        $series = $this->getSeries($manufactureId);
                        $processedSeries = $series;
                    } else {
                        /*
                         * If it not, then we are going to grab the parent_id and grab all of the children
                         * of that one to grab that level.
                        */
                        $series = $this->Series->children($value['Series']['parent_id'], true);
                        $processedSeries = $this->processSeries($series);
                    }
                    $returnData['selected']['L' . $key] = $value['Series']['id'];
                    $returnData['L' . $key] = $processedSeries;
                    $lastKey = $key;
                }
                /*
                 * Ok so at this point, we have one selected, we retrieved all parent levels now we need to see if the one selected
                 * has any children to return
                */
                $series = $this->Series->children($seriesId, true);
                if (!empty($series)) {
                    $processedChildrenSeries = $this->processSeries($series);
                    $returnData['L' . ++$lastKey] = $processedChildrenSeries;
                }
                //To get the true, count we need to add one more since it is
                //using array index
                $returnData['levelCount'] = ++$lastKey;
            }
        }
        
        return $returnData;
    }
    
    private function processSeries($series) {
        $processedSeries = array();
        foreach ($series as $key => $value) {
            $processedSeries[$value['Series']['id']] = $value['Series']['name'];
        }
        return $processedSeries;
    }
}
?>
