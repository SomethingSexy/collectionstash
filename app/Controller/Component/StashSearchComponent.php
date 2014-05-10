<?php
App::uses('Component', 'Controller');
class StashSearchComponent extends Component
{
    public $components = array('Search');
    
    public function initialize(Controller $controller) {
        $this->controller = $controller;
        $this->controller->loadModel('Stash');
        $this->controller->loadModel('CollectiblesUser');
        $this->controller->filters = array(
        //
        'm' => array('model' => 'Manufacture', 'multiple' => true, 'id' => 'id', 'user_selectable' => true, 'label' => 'Manufacturer', 'key' => 'title'),
        //
        'ct' => array('model' => 'Collectibletype', 'multiple' => true, 'id' => 'id', 'user_selectable' => true, 'label' => 'Platform', 'key' => 'name'),
        //
        'l' => array('model' => 'License', 'multiple' => true, 'id' => 'id', 'user_selectable' => true, 'label' => 'Brand', 'key' => 'name'),
        //
        's' => array('model' => 'Scale', 'multiple' => true, 'id' => 'id', 'user_selectable' => true, 'label' => 'Scale', 'key' => 'scale'),
        //
        'v' => array('model' => 'Collectible', 'multiple' => false, 'id' => 'variant', 'user_selectable' => true, 'label' => 'Variant', 'values' => array(1 => 'Yes', 0 => 'No')),
        //
        //'o' => array('custom' => true, 'multiple' => false, 'id' => 'order', 'user_selectable' => true, 'label' => 'Order by', 'values' => array('n' => 'Newest', 'o' => 'Oldest', 'a' => 'Ascending', 'd' => 'Descending'))
        );
    }
    
    public function search($user) {
        $saveSearchFilters = $this->Search->getFiltersFromQuery();
        $tableFilters = $this->Search->processQueryFilters($saveSearchFilters);
        
        $joins = array();
        
        if (!empty($saveSearchFilters)) {
            array_push($joins, array('table' => 'collectibles', 'alias' => 'Collectible2', 'type' => 'inner', 'conditions' => array('Collectible2.id = CollectiblesUser.collectible_id')));
            // if I am not filtering on something I will have to not add these joins because if the value is null then collectibles won't show up
            if (!empty($saveSearchFilters['m'])) {
                array_push($joins, array('table' => 'manufactures', 'alias' => 'Manufacture', 'type' => 'inner', 'conditions' => array('Collectible2.manufacture_id = Manufacture.id')));
            }
            if (!empty($saveSearchFilters['ct'])) {
                array_push($joins, array('table' => 'collectibletypes', 'alias' => 'Collectibletype', 'type' => 'inner', 'conditions' => array('Collectible2.collectibletype_id = Collectibletype.id')));
            }
            if (!empty($saveSearchFilters['l'])) {
                array_push($joins, array('table' => 'licenses', 'alias' => 'License', 'type' => 'inner', 'conditions' => array('Collectible2.license_id = License.id')));
            }
            if (!empty($saveSearchFilters['s'])) {
                array_push($joins, array('table' => 'scales', 'alias' => 'Scale', 'type' => 'inner', 'conditions' => array('Collectible2.scale_id = Scale.id')));
            }
        }
        
        $conditions = array('CollectiblesUser.active' => true, 'CollectiblesUser.user_id' => $user['User']['id']);
        array_push($conditions, $tableFilters);
        // Be very careful when changing this contains, it is tied to the type
        $this->controller->paginate = array('paramType' => 'querystring', 'findType' => 'orderAveragePrice', 'joins' => $joins, 'limit' => 25, 'order' => array('sort_number' => 'desc'), 'conditions' => $conditions, 'contain' => array('Condition', 'Merchant', 'Collectible' => array('User', 'CollectiblePriceFact', 'CollectiblesUpload' => array('Upload'), 'Manufacture', 'Collectibletype', 'ArtistsCollectible' => array('Artist'))));
        
        $saveSearchFilters = $this->Search->processFilters($saveSearchFilters);
        $this->controller->set(compact('saveSearchFilters'));
        
        return $this->controller->paginate('CollectiblesUser');
    }
    public function getFilters($userId) {
        // grab the default filers
        $filters = $this->controller->filters;
        
        $values = $this->controller->Stash->getFilters($userId);
        if (!empty($values)) {
            $filters['m']['values'] = $values['m']['values'];
            $filters['ct']['values'] = $values['ct']['values'];
            $filters['l']['values'] = $values['l']['values'];
            $filters['s']['values'] = $values['s']['values'];
        }
        
        return $filters;
    }
}
?>