<?php
class Stash extends AppModel {
	var $name = 'Stash';
	var $useTable = 'stashes';
	var $hasMany = array('CollectiblesUser' => array('dependent' => true), 'PostersUser');
	var $belongsTo = array('User' => array('counterCache' => true));
	var $actsAs = array('Revision', 'Containable');

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
		} else {
			debug($totalAllowed);
			if(true) {
				debug($stashName);
				if($this -> find('count', array('conditions' => array('Stash.name' => $stashName, 'Stash.user_id' => $this -> data['Stash']['user_id']))) > 0) {
					debug($stashName);
					$this -> invalidate('name', 'A stash with that name already exists.');
					$valid = false;
				} else {

					$stashName = trim($stashName);
					debug($stashName);
					if(empty($stashName)) {
						debug($stashName);
						$valid = false;
						$this -> invalidate('name', 'You must enter a stash name.');
					}

				}
			}
		}

		return $valid;
	}

	var $validate = array(
		'name' => array(
			'validValues' => array(
				'rule' => '/^[\\w\\s-]+$/', 
				'message' => 'Alphanumeric only'),
			'length' => array (
				'rule' => array('maxLength', 50),
				'message' => 'Max length of 50.'),			
			)
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

}
?>
