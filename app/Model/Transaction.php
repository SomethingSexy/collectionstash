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
						if (isset($val['Transaction']['sale_date'])) {
							$datetime = strtotime($val['Transaction']['sale_date']);
							$datetime = date("m/d/y g:i A", $datetime);
							$results[$key]['Transaction']['sale_date'] = $datetime;
						}
					}
				}
			} else {
				if (isset($results[$this -> primaryKey])) {

					if (isset($results['sale_date'])) {
						$datetime = strtotime($results['sale_date']);
						$datetime = date("m/d/y g:i A", $datetime);
						$results['sale_date'] = $datetime;
					}
				} else {

					foreach ($results as $key => $val) {

						if (isset($val['Transaction'])) {
							if (isset($val['Transaction']['sale_date'])) {
								$datetime = strtotime($val['Transaction']['sale_date']);
								$datetime = date("m/d/y g:i A", $datetime);
								$results[$key]['Transaction']['sale_date'] = $datetime;
							}
						}
					}
				}
			}

		}
		return $results;
	}

}
?>
