<?php
class Stash extends AppModel {
	var $name = 'Stash';
	var $useTable = 'stashes';
	var $hasMany = array('CollectiblesUser' => array('dependent' => true));
	var $belongsTo = array('User' => array('counterCache' => true));
	var $actsAs = array('Containable');

	function beforeValidate() {
		$valid = true;
		$totalCount = $this -> data['Stash']['total_count'];
		//Add one because we are adding it.
		$totalCount = $totalCount + 1;
		$totalAllowed = Configure::read('Settings.Stash.total-allowed');
		$stashName = $this -> data['Stash']['name'];

		if($totalCount > $totalAllowed) {
			$this -> validationErrors['totalAllowed'] = 'You have reached your max number of stashes.';
			$valid = false;
		} 
		// else {
			// debug($totalAllowed);
			// if(true) {
				// debug($stashName);
				// if($this -> find('count', array('conditions' => array('Stash.name' => $stashName, 'Stash.user_id' => $this -> data['Stash']['user_id']))) > 0) {
					// debug($stashName);
					// $this -> invalidate('name', 'A stash with that name already exists.');
					// $valid = false;
				// } else {
// 
					// $stashName = trim($stashName);
					// debug($stashName);
					// if(empty($stashName)) {
						// debug($stashName);
						// $valid = false;
						// $this -> invalidate('name', 'You must enter a stash name.');
					// }
// 
				// }
			// }
		// }

		return $valid;
	}

	var $validate = array(
		'name' => array('validValues' => array('rule' => '/^[\\w\\s-]+$/', 'message' => 'Alphanumeric only'), 'length' => array('rule' => array('maxLength', 50), 'message' => 'Max length of 50.')),
		'privacy' => array('rule'=>array('inList', array('0','1')), 'message' => 'That value does not seem right.')
		);

	public function getStashDetails($userId) {
		$this -> Behaviors -> attach('Containable');
		$stashes = $this -> find("all", array('contain' => array('CollectiblesUser'), 'conditions' => array('user_id' => $userId)));

		$slimStashes = array();
		//debug($stashes);
		foreach($stashes as $key => $stash) {
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
		$stashCollectibles = $this -> CollectiblesUser -> find('all', array('conditions' => array('CollectiblesUser.stash_id' => $stashId), 'contain'=> false));
		debug($stashCollectibles);
		$stashCount = count($stashCollectibles);	
		$stashTotal = 0;
		foreach($stashCollectibles as $key => $userCollectible) {
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
