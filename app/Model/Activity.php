<?php
class Activity extends AppModel {
	public $name = 'Activity';
	public $actsAs = array('Containable');
	public $belongsTo = array('User', 'ActivityType');
	
	/**
	 * Right now, I am doing all activity processing server
	 * side, so we are decoding here...if this becomes a performance
	 * issue we will offload this to the client side
	 */
	public function afterFind($results, $primary = false) {
		foreach ($results as $key => $val) {
			if ($primary && isset($val['Activity'])) {
				$results[$key]['Activity']['data'] = json_decode($results[$key]['Activity']['data']);
			}
		}
		return $results;
	}

}
?>
