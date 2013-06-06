<?php
class Stash extends AppModel {
	public $name = 'Stash';
	public $useTable = 'stashes';
	public $hasMany = array('CollectiblesUser' => array('dependent' => true));
	public $belongsTo = array('User' => array('counterCache' => true), 'EntityType' => array('dependent' => true));
	public $actsAs = array('Containable');

	public function beforeSave() {
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
		$stashCount = count($stashCollectibles);
		$stashTotal = 0;
		foreach ($stashCollectibles as $key => $userCollectible) {
			$floatCost = (float)$userCollectible['CollectiblesUser']['cost'];
			$formatCost = number_format($floatCost, 2, '.', '');
			$stashTotal += $formatCost;
		}

		$formatTotal = number_format($stashTotal, 2, '.', '');
		$stats['StashStats']['cost_total'] = $formatTotal;
		$stats['StashStats']['count'] = $stashCount;

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

	/**
	 * Depending on the graph we will want to switch these out based on the graph options we support
	 *
	 * pick one for the default and then the rest will be loaded via ajax
	 *
	 * Performance update: create a stats table that calculates every user's stash to add add, remove counts by month and year
	 *
	 */
	public function getStashHistory($user) {
		$collectibles = $this -> CollectiblesUser -> find('all', array('joins' => array( array('alias' => 'Stash', 'table' => 'stashes', 'type' => 'inner', 'conditions' => array('Stash.id = CollectiblesUser.stash_id', 'Stash.name = "Default"'))), 'order' => array('purchase_date' => 'desc'), 'contain' => false, 'conditions' => array('CollectiblesUser.user_id' => $user['User']['id'])));

		debug($collectibles);

		// we need to find the beginning and the end
		//
		// then we need to figure out our ranges, every month, or a subset of months or years

		// then once we have our ranges, we can organize them into those ranges and add counts

		//0000-00-00

		// this would be a line graph of purchases

		// or it could be a bar graph of purchases with how many you sold ontop
		$templateData = array();
		foreach ($collectibles as $collectible) {
			if ($collectible['CollectiblesUser']['purchase_date'] !== null && $collectible['CollectiblesUser']['purchase_date'] !== '0000-00-00') {

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
			if ($collectible['CollectiblesUser']['remove_date'] !== null && $collectible['CollectiblesUser']['remove_date'] !== '0000-00-00') {

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

		ksort($templateData);

		$oldestYear = key($templateData);
		end($templateData);
		$newestYear = key($templateData);

		reset($templateData);

		debug($newestYear);
		debug($oldestYear);

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
		debug($templateData);
		// we need to fill out empty years, months now

		// if I wanted to do an overall , I would have to do a tally of when things were per month and then if something was removed that month subtract, but each month would have to carry over the previous months count

		return $templateData;

	}

}
?>
