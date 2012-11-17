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

	public function getAttributeList($id = null) {
		/*
		 * What I will do. Use Ajax, display the first list of attributes.
		 *
		 * Then when they select an attribute, go grab the next list of attributes, if there are none
		 * then we jsut present the description field and other info.  If there ia list we display
		 * the new drop down until we are done.
		 *
		 * We will need to convert the current level list to a key/value pair list.
		 */
		$allChildren = $this -> generateTreeList(array('Attribute.parent_id' => $id), null, null, '...');
		asort($allChildren);

		return $allChildren;
	}

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

}
?>
