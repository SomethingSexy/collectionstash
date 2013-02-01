<?php
/**
 * This model contains the path name as a field for fast reference.
 *
 * However this means whenever we add a new category or a move an existing one
 * we need to update all path names that are effected
 */
class AttributeCategory extends AppModel {
	var $name = 'AttributeCategory';
	var $actsAs = array('Tree', 'Containable');
	public $hasMany = array('Attribute');

	public function updatePath($id) {
		$paths = $this -> getPath($id);
		$pathName = '';
		$totalPaths = count($paths);
		foreach ($paths as $key => $value) {
			$pathName .= $value['AttributeCategory']['name'];
			if (++$key != $totalPaths) {
				$pathName .= '/';
			}
		}

		$this -> read(null, $id);
		$this -> set(array('path_name' => $pathName));
		$this -> save();
	}

	public function get() {
		$retVal = $this -> buildDefaultResponse();

		$series = $this -> find('threaded');
		// With this one we need to include the parent, otherwise it won't work
		$retVal['response']['data'] = $series;
		$retVal['response']['isSuccess'] = true;
		return $retVal;
	}

}
?>
