<?php
App::uses('Component', 'Controller');
class SearchComponent extends Component
{
    public function initialize(Controller $controller) {
        $this->controller = $controller;
    }
    
    public function getFiltersFromQuery() {
        $saveSearchFilters = array();
        // handle this one separately for now as well
        if (isset($this->controller->request->query['q'])) {
            $this->controller->request->data['Search'] = array();
            $this->controller->request->data['Search']['search'] = '';
            $this->controller->request->data['Search']['search'] = $this->controller->request->query['q'];
        }
        // handle this one separately
        if (isset($this->controller->request->query['o'])) {
        } else {
            $this->controller->request->query['o'] = 'a';
        }
        
        if (isset($this->controller->request->data['Search']['search']) && trim($this->controller->request->data['Search']['search']) !== '') {
            $search = $this->controller->request->data['Search']['search'];
            $search = ltrim($search);
            $search = rtrim($search);
            $saveSearchFilters['search'] = $search;
        }
        
        foreach ($this->controller->filters as $filterkey => $filter) {
            if (isset($this->controller->request->query[$filterkey])) {
                
                $queryValue = $this->controller->request->query[$filterkey];
                if (strpos($queryValue, ',') !== false) {
                    $queryValue = rtrim($queryValue, ",");
                    $queryValue = explode(",", $queryValue);
                } else {
                    $queryValue = array($queryValue);
                }
                
                foreach ($queryValue as $key => $value) {
                    if (!isset($saveSearchFilters[$filterkey])) {
                        $saveSearchFilters[$filterkey] = array();
                    }
                    array_push($saveSearchFilters[$filterkey], $value);
                }
            }
        }
        
        return $saveSearchFilters;
    }
    // this will take the filters from the query and make table filters out of them
    public function processQueryFilters($currentFilters = array()) {
        $tableFilters = array();
        foreach ($currentFilters as $filterKey => $filterGroup) {
            // if the one we are looking at is a custom
            if (!isset($this->controller->filters[$filterKey]['custom']) || !$this->controller->filters[$filterKey]['custom']) {
                $modelFilters = array('AND' => array());
                array_push($modelFilters['AND'], array('OR' => array()));
                $filtersSet = false;
                
                if (is_array($filterGroup)) {
                    foreach ($filterGroup as $key => $value) {
                        array_push($modelFilters['AND'][0]['OR'], array($this->controller->filters[$filterKey]['model'] . '.' . $this->controller->filters[$filterKey]['id'] => $value));
                        $filtersSet = true;
                    }
                }
                
                if ($filtersSet) {
                    array_push($tableFilters, $modelFilters);
                }
            }
        }
        
        return $tableFilters;
    }
    
    public function processFilters($searchFilters) {
        $retVal = array();
        // if we have some settings
        if (isset($searchFilters['m'])) {
            
            $this->controller->loadModel('Manufacture');
            foreach ($searchFilters['m'] as $key => $value) {
                $manufacturer = $this->controller->Manufacture->find("first", array('conditions' => array('Manufacture.id' => $value), 'contain' => false));
                array_push($retVal, array('id' => $value, 'label' => $manufacturer['Manufacture']['title'], 'type' => 'm'));
            }
        }
        
        if (isset($searchFilters['ct'])) {
            
            $this->controller->loadModel('Collectibletype');
            foreach ($searchFilters['ct'] as $key => $value) {
                $collectibleType = $this->controller->Collectibletype->find("first", array('conditions' => array('Collectibletype.id' => $value), 'contain' => false));
                array_push($retVal, array('id' => $value, 'label' => $collectibleType['Collectibletype']['name'], 'type' => 'ct'));
            }
        }
        
        if (isset($searchFilters['l'])) {
            $this->controller->loadModel('License');
            foreach ($searchFilters['l'] as $key => $value) {
                $license = $this->controller->License->find("first", array('conditions' => array('License.id' => $value), 'contain' => false));
                array_push($retVal, array('id' => $value, 'label' => $license['License']['name'], 'type' => 'l'));
            }
        }
        
        if (isset($searchFilters['s'])) {
            $this->controller->loadModel('Scale');
            foreach ($searchFilters['s'] as $key => $value) {
                $scale = $this->controller->Scale->find("first", array('conditions' => array('Scale.id' => $value), 'contain' => false));
                array_push($retVal, array('id' => $value, 'label' => $scale['Scale']['scale'], 'type' => 's'));
            }
        }
        
        if (isset($searchFilters['status'])) {
            foreach ($searchFilters['status'] as $key => $value) {
                if ($value === '2') {
                    array_push($retVal, array('id' => '2', 'label' => __('Pending'), 'type' => 'status'));
                } else if ($value === '4') {
                    array_push($retVal, array('id' => '4', 'label' => __('Active'), 'type' => 'status'));
                }
            }
        }
        
        if (isset($searchFilters['v'])) {
            foreach ($searchFilters['v'] as $key => $value) {
                if ($value === '1') {
                    array_push($retVal, array('id' => 1, 'label' => __('Variant'), 'type' => 'v'));
                } else if ($value === '0') {
                    array_push($retVal, array('id' => 0, 'label' => __('Not a Variant'), 'type' => 'v'));
                }
            }
        }
        
        if (isset($searchFilters['search'])) {
            array_push($retVal, array('id' => $searchFilters['search'], 'label' => $searchFilters['search'], 'type' => 'q'));
        }
        
        if (isset($searchFilters['t'])) {
            $this->controller->loadModel('Tag');
            foreach ($searchFilters['t'] as $key => $value) {
                $tag = $this->controller->Tag->find("first", array('conditions' => array('Tag.id' => $value), 'contain' => false));
                array_push($retVal, array('id' => $value, 'label' => $tag['Tag']['tag'], 'type' => 't'));
            }
        }
        
        if (isset($searchFilters['o'])) {
            foreach ($searchFilters['o'] as $key => $value) {
                array_push($retVal, array('id' => $value, 'label' => $this->controller->filters['o']['values'][$value], 'type' => 'o'));
            }
        }
        
        return $retVal;
    }
}
?>