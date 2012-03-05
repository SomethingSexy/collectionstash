<?php
class Series extends AppModel {
	public $name = 'Series';
	public $useTable = 'series';
	public $hasMany = array('Collectible', 'Manufacture');
	public $actsAs = array('Tree', 'Containable');

    public $validate = array(
    //name field
    'name' => array('minLength' => array('rule' => 'notEmpty', 'message' => 'Name is required.'), 'maxLength' => array('rule' => array('maxLength', 200), 'message' => 'Invalid length.')));

	/**
	 * This method, given a series id will build the series name path.
	 *
	 * This means that it will take the hierarchy of the path and build out
	 * the name representation
	 */
	public function buildSeriesPathName($seriesId) {
		$paths = $this -> getPath($seriesId);
		$seriesPathName = '';
		$totalPaths = count($paths);
		foreach ($paths as $key => $value) {
			$seriesPathName .= $value['Series']['name'];
			if (++$key != $totalPaths) {
				$seriesPathName .= '/';
			}
		}

		return $seriesPathName;
	}

}
?>
