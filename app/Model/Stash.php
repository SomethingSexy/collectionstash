<?php
class Stash extends AppModel {
	public $name = 'Stash';
	public $useTable = 'stashes';
	public $hasMany = array('CollectiblesUser' => array('dependent' => true));
	public $belongsTo = array('User' => array('counterCache' => true), 'EntityType' => array('dependent' => true));
	public $actsAs = array('Containable');

	public function beforeSave() {

		debug($this -> data);
		return true;
	}

	public function getStashDetails($userId) {
		$this -> Behaviors -> attach('Containable');
		$stashes = $this -> find("all", array('contain' => array('CollectiblesUser'), 'conditions' => array('user_id' => $userId)));

		$slimStashes = array();
		//debug($stashes);
		foreach ($stashes as $key => $stash) {
			$slimStashes[$key]['Stash'] = $stash['Stash'];
			$slimStashes[$key]['Stash']['count'] = count($stash['CollectiblesUser']);
		}
		debug($slimStashes);
		return $slimStashes;
	}

	public function getNumberOfCollectiblesInStash($stashId) {
		$count = $this -> CollectiblesUser -> find("count", array('conditions' => array('CollectiblesUser.stash_id' => $stashId)));
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
		$stashCollectibles = $this -> CollectiblesUser -> find('all', array('conditions' => array('CollectiblesUser.stash_id' => $stashId), 'contain' => false));
		debug($stashCollectibles);
		$stashCount = count($stashCollectibles);
		$stashTotal = 0;
		foreach ($stashCollectibles as $key => $userCollectible) {
			$floatCost = (float)$userCollectible['CollectiblesUser']['cost'];
			$formatCost = number_format($floatCost, 2, '.', '');
			debug($formatCost);
			$stashTotal += $formatCost;
		}

		debug($stashTotal);
		$formatTotal = number_format($stashTotal, 2, '.', '');
		$stats['StashStats']['cost_total'] = $formatTotal;
		$stats['StashStats']['count'] = $stashCount;
		debug($stats);

		return $stats;
	}

}
?>
