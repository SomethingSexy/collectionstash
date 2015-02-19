<?php
App::uses('Component', 'Controller');
class CollectibleSearchComponent extends Component
{
    public $components = array('Search');
    
    public function initialize(Controller $controller) {
        $this->controller = $controller;
        $this->controller->loadModel('Collectible');
        $this->controller->filters = array(
        //
        'm' => array('model' => 'Collectible', 'multiple' => true, 'id' => 'manufacture_id', 'user_selectable' => true, 'label' => 'Manufacturer', 'key' => 'title'),
        //
        'ct' => array('model' => 'Collectible', 'multiple' => true, 'id' => 'collectibletype_id', 'user_selectable' => true, 'label' => 'Platform', 'key' => 'name'),
        //
        'l' => array('model' => 'Collectible', 'multiple' => true, 'id' => 'license_id', 'user_selectable' => true, 'label' => 'Brand', 'key' => 'name'),
        //
        's' => array('model' => 'Collectible', 'multiple' => true, 'id' => 'scale_id', 'user_selectable' => true, 'label' => 'Scale', 'key' => 'scale'),
        //
        'v' => array('model' => 'Collectible', 'multiple' => false, 'id' => 'variant', 'user_selectable' => true, 'label' => 'Variant', 'values' => array(1 => 'Yes', 0 => 'No')),
        //
        'status' => array('custom' => true, 'multiple' => false, 'model' => 'Collectible', 'id' => 'status_id', 'user_selectable' => true, 'label' => 'Status', 'values' => array('2' => 'Pending', '4' => 'Active')),
        //
        't' => array('model' => 'Tag', 'id' => 'id'),
        //
        'o' => array('custom' => true, 'multiple' => false, 'id' => 'order', 'user_selectable' => true, 'label' => 'Order by', 'values' => array('n' => 'Newest', 'o' => 'Oldest', 'a' => 'Ascending', 'd' => 'Descending')));
    }
    
    public function search($conditions = null, $paramType = null) {
        $saveSearchFilters = $this->Search->getFiltersFromQuery();
        //If nothing is set, use alphabetical order as the default
        $order = array();
        $order['Collectible.name'] = 'ASC';
        $status = array();
        $status['Collectible.status_id'] = '4';
        $tableFilters = array();
        foreach ($saveSearchFilters as $filterKey => $filterGroup) {
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
            } else if ($filterKey === 'o') {
                // there can only be on
                $orderType = $filterGroup[0];
                //reset order
                $order = array();
                switch ($orderType) {
                    case "n":
                        $order['Collectible.modified'] = 'desc';
                        break;

                    case "o":
                        $order['Collectible.created'] = 'ASC';
                        break;

                    case "a":
                        $order['Collectible.name'] = 'ASC';
                        break;

                    case "d":
                        $order['Collectible.name'] = 'desc';
                        break;

                    default:
                        $order['Collectible.name'] = 'ASC';
                }
            } else if ($filterKey === 'status') {
                $statusType = $filterGroup[0];
                
                switch ($statusType) {
                    case 2:
                        $status['Collectible.status_id'] = 2;
                        break;

                    case 4:
                        $status['Collectible.status_id'] = 4;
                        break;

                    default:
                        $status['Collectible.status_id'] = 4;
                }
            }
        }
        
        if (!isset($filters)) {
            $filters = array();
        }
        //Some conditions were added
        if (!is_array($conditions)) {
            $conditions = array();
        }
        
        $joins = array();
        //Do some special logic here for tags because of how they are setup.
        if (isset($saveSearchFilters['t']) && $saveSearchFilters['t']) {
            array_push($joins, array('table' => 'collectibles_tags', 'alias' => 'CollectiblesTag', 'type' => 'inner', 'conditions' => array('Collectible.id = CollectiblesTag.collectible_id')));
            array_push($joins, array('table' => 'tags', 'alias' => 'Tag', 'type' => 'inner', 'conditions' => array('CollectiblesTag.tag_id = Tag.id')));
        }
        
        $listSize = Configure::read('Settings.Search.list-size');
        // set status here, this one is a litte special because we need a default
        array_push($conditions, $status);
        
        $query = array("joins" => $joins, 'order' => $order, "conditions" => array($conditions, $tableFilters), "contain" => array('Scale', 'ArtistsCollectible' => array('Artist'), 'AttributesCollectible' => array('Attribute' => array('AttributeCategory', 'Scale', 'Manufacture', 'AttributesUpload' => array('Upload'))), 'Manufacture', 'License', 'Collectibletype', 'CollectiblesUpload' => array('Upload'), 'CollectiblesTag' => array('Tag')), 'maxLimit' => 25);
        if (!is_null($paramType)) {
            $query['paramType'] = $paramType;
        }
        //See if a search was set
        if (isset($saveSearchFilters['search']) && $saveSearchFilters['search'] !== '') {
            //Using like for now because switch to InnoDB
            $test = array();
            array_push($test, array('AND' => array()));
            array_push($test[0]['AND'], array('OR' => array()));
            //array_push($test[0]['AND'][0]['OR'], array('Collectible.name LIKE' => '%' . $search . '%'));
            
            $names = explode(' ', $saveSearchFilters['search']);
            $regSearch = array();
            foreach ($names as $key => $value) {
                // in case any weird characters get in there that this will trim
                $name = trim($value);
                array_push($regSearch, array('Collectible.name REGEXP' => '[[:<:]]' . $name . '[[:>:]]'));
            }
            array_push($test[0]['AND'][0]['OR'], $regSearch);
            // keep this one a standard like
            array_push($test[0]['AND'][0]['OR'], array('License.name LIKE' => '%' . $saveSearchFilters['search'] . '%'));
            
            array_push($query['conditions'], $test);
            $this->controller->paginate = $query;
        } else {
            $this->controller->paginate = $query;
        }
        
        $data = $this->controller->paginate('Collectible');
        
        $this->controller->set('collectibles', $data);
        $this->controller->set('filters', $this->getFilters());
        $saveSearchFilters = $this->Search->processFilters($saveSearchFilters);
        $queryString = '?';
        
        foreach ($saveSearchFilters as $key => $value) {
            $queryString.= $value['type'] . '=' . $value['id'];
            if (count($saveSearchFilters) !== ($key + 1)) {
                $queryString.= '&';
            }
        }
        
        $this->controller->set('searchQueryString', $queryString);
        
        $this->controller->set(compact('saveSearchFilters'));
        
        return $data;
    }
    
    public function getFilters() {
        $filters = $this->controller->filters;
        
        $filters['m']['values'] = $this->controller->Collectible->Manufacture->find('list', array('contain' => false, 'fields' => array('Manufacture.id', 'Manufacture.title'), 'order' => array('Manufacture.title' => 'asc')));
        $filters['ct']['values'] = $this->controller->Collectible->Collectibletype->find('list', array('contain' => false, 'fields' => array('Collectibletype.id', 'Collectibletype.name')));
        $filters['l']['values'] = $this->controller->Collectible->License->find('list', array('contain' => false, 'fields' => array('License.id', 'License.name')));
        $filters['s']['values'] = $this->controller->Collectible->Scale->find('list', array('contain' => false, 'fields' => array('Scale.id', 'Scale.scale')));
        
        return $filters;
    }
}
?>