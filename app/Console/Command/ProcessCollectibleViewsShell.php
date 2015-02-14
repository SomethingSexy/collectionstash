<?php
/**
 * This will process collectible views
 *
 *
 * This will run once a day.
 *
 * Right now this will
 *
 */
App::uses('TransactionFactory', 'Lib/Transaction');
class ProcessCollectibleViewsShell extends AppShell
{
    public $uses = array('CollectibleView', 'Collectible');
    
    public function main() {
        $month = date("m");
        $day = date("d") - 1;
        // This will have the year string
        $year = date("Y");
        // Supposedly this is the slowest but I am not too worried about it for now
        $start = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, $day, $year));
        $end = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $day, $year));
        // grab all collectible views from the previous day
        // this seems a bit goofy but it works although the data is being returned funny
        $collectibles = $this->CollectibleView->find('all', array('conditions' => array('CollectibleView.created BETWEEN ? AND ?' => array($start, $end)), 'group' => array('CollectibleView.collectible_id'), 'fields' => array('CollectibleView.collectible_id', 'COUNT(*) as count')));

        foreach ($collectibles as $key => $collectible) {
            if ($collectible[0]) {
                $id = $collectible['CollectibleView']['collectible_id'];
                $count = $collectible[0]['count'];
                $this->Collectible->updateAll(array('Collectible.viewed' => 'Collectible.viewed+' . $count, 'Collectible.modified' => 'NOW()'), array('Collectible.id' => $id));
            }
        }
    }
}
?>