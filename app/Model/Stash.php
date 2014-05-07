<?php
class Stash extends AppModel
{
    public $name = 'Stash';
    public $useTable = 'stashes';
    public $hasMany = array('CollectiblesUser' => array('dependent' => true), 'StashFact' => array('dependent' => true));
    public $belongsTo = array('User' => array('counterCache' => true), 'EntityType' => array('dependent' => true));
    public $actsAs = array('Containable');
    
    public function beforeSave() {
        return true;
    }
    
    function afterFind($results, $primary = false) {
        if ($results && $primary) {
            foreach ($results as $key => $val) {
                if (isset($val['Stash'])) {
                    if (isset($val['StashFact']) && !empty($val['StashFact'])) {
                        
                        // currently we only have one
                        $results[$key]['StashFact'] = $val['StashFact'][0];
                    } else {
                        unset($results[$key]['StashFact']);
                    }
                }
            }
        }
        
        return $results;
    }
    
    public function getStashDetails($userId) {
        $this->Behaviors->attach('Containable');
        $stashes = $this->find("all", array('contain' => array('CollectiblesUser'), 'conditions' => array('user_id' => $userId)));
        
        $slimStashes = array();
        
        //debug($stashes);
        foreach ($stashes as $key => $stash) {
            $slimStashes[$key]['Stash'] = $stash['Stash'];
            $slimStashes[$key]['Stash']['count'] = count($stash['CollectiblesUser']);
        }
        
        return $slimStashes;
    }
    
    public function getNumberOfCollectiblesInStash($stashId) {
        $count = $this->CollectiblesUser->find("count", array('conditions' => array('CollectiblesUser.stash_id' => $stashId)));
        return $count;
    }
    
    /**
     * This function will return a bunch of stats around a stash
     * [StashStats] => Array (
     * 		[count]
     * 		[total_cost]
     *
     * )
     */
    public function getStashStats($stashId) {
        
        /*
         * TODO: At some point should probably have a count stored in the database, along with stash totals.
        */
        
        //setup return object
        $stats = array();
        $stats['StashStats'] = array();
        $stashCollectibles = $this->CollectiblesUser->find('all', array('conditions' => array('CollectiblesUser.stash_id' => $stashId), 'contain' => false));
        $stashCount = count($stashCollectibles);
        $stashTotal = 0;
        foreach ($stashCollectibles as $key => $userCollectible) {
            $floatCost = (float)$userCollectible['CollectiblesUser']['cost'];
            $formatCost = number_format($floatCost, 2, '.', '');
            $stashTotal+= $formatCost;
        }
        
        $formatTotal = number_format($stashTotal, 2, '.', '');
        $stats['StashStats']['cost_total'] = $formatTotal;
        $stats['StashStats']['count'] = $stashCount;
        
        return $stats;
    }
    
    /**
     * Given a user id and a stash type, return the stash
     */
    public function getStash($userId) {
        return $this->find('first', array('conditions' => array('Stash.user_id' => $userId)));
    }
    
    public function getStashId($userId) {
        $stash = $this->find('first', array('contain' => false, 'conditions' => array('Stash.user_id' => $userId)));
        
        return $stash['Stash']['id'];
    }
    
    /**
     * Depending on the graph we will want to switch these out based on the graph options we support
     *
     * pick one for the default and then the rest will be loaded via ajax
     *
     * Performance update: create a stats table that calculates every user's stash to add add, remove counts by month and year
     *
     */
    public function getStashGraphHistory($user) {
        $collectibles = $this->CollectiblesUser->find('all', array('joins' => array(array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'order' => array('purchase_date' => 'desc'), 'contain' => false, 'conditions' => array('CollectiblesUser.user_id' => $user['User']['id'])));
        
        // we need to find the beginning and the end
        //
        // then we need to figure out our ranges, every month, or a subset of months or years
        
        // then once we have our ranges, we can organize them into those ranges and add counts
        
        //0000-00-00
        
        // this would be a line graph of purchases
        
        // or it could be a bar graph of purchases with how many you sold ontop
        $templateData = array();
        foreach ($collectibles as $collectible) {
            if ($collectible['CollectiblesUser']['purchase_date'] !== null && $collectible['CollectiblesUser']['purchase_date'] !== '0000-00-00' && !empty($collectible['CollectiblesUser']['purchase_date'])) {
                
                $time = strtotime($collectible['CollectiblesUser']['purchase_date']);
                $date = date('m d y', $time);
                $date = date_parse_from_format('m d y', $date);
                
                if (!isset($templateData[$date['year']])) {
                    $templateData[$date['year']] = array();
                }
                
                if (!isset($templateData[$date['year']][$date['month']])) {
                    $templateData[$date['year']][$date['month']] = array();
                }
                
                if (!isset($templateData[$date['year']][$date['month']]['purchased'])) {
                    $templateData[$date['year']][$date['month']]['purchased'] = array();
                }
                
                array_push($templateData[$date['year']][$date['month']]['purchased'], $collectible);
            }
            if ($collectible['CollectiblesUser']['remove_date'] !== null && $collectible['CollectiblesUser']['remove_date'] !== '0000-00-00' && !empty($collectible['CollectiblesUser']['remove_date'])) {
                
                $time = strtotime($collectible['CollectiblesUser']['remove_date']);
                $date = date('m d y', $time);
                $date = date_parse_from_format('m d y', $date);
                
                if (!isset($templateData[$date['year']])) {
                    $templateData[$date['year']] = array();
                }
                
                if (!isset($templateData[$date['year']][$date['month']])) {
                    $templateData[$date['year']][$date['month']] = array();
                    $templateData[$date['year']][$date['month']]['purchased'] = array();
                }
                
                if (!isset($templateData[$date['year']][$date['month']]['sold'])) {
                    $templateData[$date['year']][$date['month']]['sold'] = array();
                }
                array_push($templateData[$date['year']][$date['month']]['sold'], $collectible);
            }
        }
        
        if (!empty($templateData)) {
            ksort($templateData);
            $oldestYear = key($templateData);
            end($templateData);
            $newestYear = key($templateData);
            reset($templateData);
            
            for ($i = $oldestYear; $i <= $newestYear; $i++) {
                
                // if it isn't set, set the year
                if (!isset($templateData[$i])) {
                    $templateData[$i] = array();
                }
                
                for ($m = 1; $m < 13; $m++) {
                    if (!isset($templateData[$i][$m])) {
                        $templateData[$i][$m] = array();
                    }
                    
                    if (!isset($templateData[$i][$m]['purchased'])) {
                        $templateData[$i][$m]['purchased'] = array();
                    }
                    
                    if (!isset($templateData[$i][$m]['sold'])) {
                        $templateData[$i][$m]['sold'] = array();
                    }
                }
                
                ksort($templateData[$i]);
            }
        }
        
        // we need to fill out empty years, months now
        
        // if I wanted to do an overall , I would have to do a tally of when things were per month and then if something was removed that month subtract, but each month would have to carry over the previous months count
        
        return $templateData;
    }
    
    public function getProfileSettings($user) {
        $stash = $this->find("first", array('conditions' => array('Stash.user_id' => $user['User']['id']), 'contain' => false));
        $profileSettings = array();
        $profileSettings['privacy'] = $stash['Stash']['privacy'];
        $profileSettings['id'] = $stash['Stash']['id'];
        
        return $profileSettings;
    }
    
    /**
     * Get filters for a given stash
     */
    public function getFilters($userId) {
        $filters = array();
        $collectibles = $this->CollectiblesUser->find('all', array('conditions' => array('CollectiblesUser.user_id' => $userId, 'CollectiblesUser.active' => true), 'contain' => array('Collectible', 'Stash')));
        if (empty($collectibles)) {
            return $filters;
        }
        
        $typeIds = array_unique(Set::extract($collectibles, '/Collectible/collectibletype_id'));
        $manIds = array_unique(Set::extract($collectibles, '/Collectible/manufacture_id'));
        $licenseIds = array_unique(Set::extract($collectibles, '/Collectible/license_id'));
        $scaleIds = array_unique(Set::extract($collectibles, '/Collectible/scale_id'));
        
        $filters['m']['values'] = $this->CollectiblesUser->Collectible->Manufacture->find('list', array('conditions' => array('Manufacture.id' => $manIds), 'contain' => false, 'fields' => array('Manufacture.id', 'Manufacture.title'), 'order' => array('Manufacture.title' => 'asc')));
        $filters['ct']['values'] = $this->CollectiblesUser->Collectible->Collectibletype->find('list', array('conditions' => array('Collectibletype.id' => $typeIds), 'contain' => false, 'fields' => array('Collectibletype.id', 'Collectibletype.name')));
        $filters['l']['values'] = $this->CollectiblesUser->Collectible->License->find('list', array('conditions' => array('License.id' => $licenseIds), 'contain' => false, 'fields' => array('License.id', 'License.name')));
        $filters['s']['values'] = $this->CollectiblesUser->Collectible->Scale->find('list', array('conditions' => array('Scale.id' => $scaleIds), 'contain' => false, 'fields' => array('Scale.id', 'Scale.scale')));
        
        return $filters;
    }
}
?>
