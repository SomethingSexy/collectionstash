<?php
class Transaction extends AppModel {
	public $name = 'Transaction';
	public $belongsTo = array('Collectible', 'Listing');
	public $actsAs = array('Containable');

	public $validate = array();

	function afterFind($results, $primary = false) {
		if ($results) {
			// If it is primary handle all of these things
			if ($primary) {
				foreach ($results as $key => $val) {
					if (isset($val['Transaction'])) {
						if (isset($val['Transaction']['sale_date']) && $val['Transaction']['sale_date'] !== '0000-00-00 00:00:00') {
							$datetime = strtotime($val['Transaction']['sale_date']);
							$datetime = date("m/d/Y", $datetime);
							$results[$key]['Transaction']['sale_date'] = $datetime;
						} else {
							$results[$key]['Transaction']['sale_date'] = '';
						}
					}
				}
			} else {
				if (isset($results[$this -> primaryKey])) {
					if (isset($results['sale_date'])) {
						if ($results['sale_date'] !== '0000-00-00 00:00:00') {
							$datetime = strtotime($results['sale_date']);
							$datetime = date("m/d/Y", $datetime);
							$results['sale_date'] = $datetime;
						} else {
							$results['sale_date'] = '';
						}
					}
				} else {
					foreach ($results as $key => $val) {
						if (isset($val['Transaction'])) {
							if (isset($val['Transaction']['sale_date'])) {
								if ($val['Transaction']['sale_date'] !== '0000-00-00 00:00:00') {
									$datetime = strtotime($val['Transaction']['sale_date']);
									$datetime = date("m/d/Y", $datetime);
									$results[$key]['Transaction']['sale_date'] = $datetime;
								} else {
									$results[$key]['Transaction']['sale_date'] = '';
								}
							}
						}
					}
				}
			}

		}
		return $results;
	}

	public function beforeSave($options = array()) {
		if (!empty($this -> data['Transaction']['sale_date'])) {
			$this -> data['Transaction']['sale_date'] = $this -> dateFormatBeforeSave($this -> data['Transaction']['sale_date']);
		}
		return true;
	}

	public function dateFormatBeforeSave($dateString) {
		return date('y-m-d', strtotime($dateString));
	}

	/**
	 * This will get the transaction graph data for a collectible.
	 *
	 * Right now it is going to return all of the data as an area of sale date and price.
	 *
	 * We won't get fancy to start
	 */
	public function getTransactionGraphData($collectibleId) {
		$retVal = array();

		$transactions = $this -> find('all', array('conditions' => array('Transaction.collectible_id' => $collectibleId), 'contain' => false));

		foreach ($transactions as $key => $value) {
			// if I have fucked up dates, don't add to graphing data.
			if ($value['Transaction']['sale_date'] !== '0000-00-00 00:00:00' && !empty($value['Transaction']['sale_date']) && $value['Transaction']['sale_date'] !== '01/01/1970' && $value['Transaction']['sale_date'] !== '12/31/1969') {
				array_push($retVal, array(strtotime($value['Transaction']['sale_date']) * 1000, $value['Transaction']['sale_price']));
			}

		}

		array_multisort($retVal);

		return $retVal;
	}
}
?>
