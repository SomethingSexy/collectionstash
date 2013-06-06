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

	/**
	 * Given a user id and a stash type, return the stash
	 */
	public function getStash($userId, $stashType = 'Default') {
		return $this -> find('first', array('conditions' => array('Stash.user_id' => $userId, 'Stash.name' => $stashType)));
	}

	public function getStashId($userId, $stashType = 'Default') {
		$stash = $this -> find('first', array('contain' => false, 'conditions' => array('Stash.user_id' => $userId, 'Stash.name' => $stashType)));

		return $stash['Stash']['id'];
	}

	/**
	 * Given a $collectibleId and a $user this method will return the stash counts
	 */
	public function getCollectibleStashCount($collectibleId, $user) {
		$retVal = array();
		// going to make them separate requests for now
		$stashes = $this -> find('all', array('contain' => false, 'conditions' => array('Stash.user_id' => $user['User']['id'])));

		foreach ($stashes as $key => $value) {
			$count = $this -> CollectiblesUser -> find('count', array('conditions' => array('CollectiblesUser.collectible_id' => $collectibleId, 'CollectiblesUser.stash_id' => $value['Stash']['id']), 'contain' => false));

			array_push($retVal, array('type' => $value['Stash']['name'], 'count' => $count));
		}

		return $retVal;
	}

	public function getStashHistory($user) {
		$collectibles = $this -> CollectiblesUser -> find('all', array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'contain' => false, 'conditions' => array('CollectiblesUser.user_id' => $user['User']['id'], 'CollectiblesUser.active' => true)));
		
		//0000-00-00
		
		debug($collectibles);
		
		
	}

}
?>
