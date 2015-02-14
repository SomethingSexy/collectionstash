<?php
/**
 * One time shell to update the collectible views
 *
 */
class OneTimeUpdateCollectibleViewsShell extends AppShell
{
    public $uses = array('CollectibleView', 'Collectible');
    
    public function main() {
        // Just grab all of the users and I will do any manually processing, should be faster
        $collectibles = $this->CollectibleView->find('all', array('fields' => array('DISTINCT CollectibleView.collectible_id')));
        
        foreach ($collectibles as $key => $collectible) {
        	$id = $collectible['CollectibleView']['collectible_id'];
            $count = $this->CollectibleView->find('count', array('conditions' => array('CollectibleView.collectible_id' => $id)));
            $this->Collectible->updateAll(array('Collectible.viewed' => 'Collectible.viewed+' . $count), array('Collectible.id' => $id));
        }
    }
}
?>