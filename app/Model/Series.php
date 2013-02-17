<?php
class Series extends AppModel {
	public $name = 'Series';
	public $useTable = 'series';
	public $hasMany = array('Collectible', 'Manufacture');
	public $actsAs = array('Tree', 'Containable');

	public $validate = array(
	//name field
	'name' => array('maxLength' => array('rule' => array('maxLength', 200), 'allowEmpty' => false, 'message' => 'Invalid length.')));

	public function add($data, $user, $autoUpdate = true) {
		$retVal = $this -> buildDefaultResponse();
		if ($this -> save($data)) {
			$id = $this -> id;
			$series = $this -> find('first', array('contain' => false, 'conditions' => array('Series.id' => $id)));
			$retVal['response']['data'] = $series['Series'];

			$retVal['response']['isSuccess'] = true;
		} else {
			$retVal['response']['isSuccess'] = false;
			$errors = $this -> convertErrorsJSON($this -> validationErrors, 'Series');
			$retVal['response']['errors'] = $errors;
		}

		return $retVal;
	}

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

	public function getSeriesByManufacturer($manufacturerId) {
		$retVal = $this -> buildDefaultResponse();

		if (!is_numeric($manufacturerId)) {
			$retVal['response']['isSuccess'] = false;
			$retVal['response']['errors'] = array('message', __('Invalid request.'));
			return $retVal;
		}

		$manufacturer = $this -> Manufacture -> find('first', array('contain' => false, 'conditions' => array('Manufacture.id' => $manufacturerId)));

		if (empty($manufacturer)) {
			$retVal['response']['isSuccess'] = false;
			$retVal['response']['errors'] = array('message', __('Invalid request.'));
			return $retVal;
		}

		// $series = $this -> find('threaded', array('conditions' => array('Series.id' => $manufacturer['Manufacture']['id'])));

		$parent = $this -> find('first', array('contain' => false, 'conditions' => array('Series.id' => $manufacturer['Manufacture']['series_id'])));
		$series = $this -> find('threaded', array('conditions' => array('Series.lft >=' => $parent['Series']['lft'], 'Series.rght <=' => $parent['Series']['rght'])));
		// With this one we need to include the parent, otherwise it won't work

		// $series = $this -> children($manufacturer['Manufacture']['id']);
		$retVal['response']['data'] = $series;
		$retVal['response']['isSuccess'] = true;
		return $retVal;
	}

}
?>
