<?php
App::import('Sanitize');
class CollectibletypesController extends AppController {

	var $name = 'Collectibletypes';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	public function getCollectibletypesData() {
		if ($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
		}

		if (!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			$collectibleTypes = $this -> Collectibletype -> CollectibletypesManufacture -> getCollectibleTypeByManufactureId($this -> data['manufacture_id']);
			$specializedTypes = array();
			if (!empty($collectibletypes)) {
				reset($collectibletypes);
				$firstColType = key($collectibletypes);
				$specializedTypes = $this -> Collectibletype -> CollectibletypesManufacture -> CollectibletypesManufactureSpecializedType -> getSpecializedTypes($this -> data['manufacture_id'], $firstColType);
			}

			$data = array();
			$data['success'] = array('isSuccess' => true);
			$data['isTimeOut'] = false;
			$data['data'] = array();
			$data['data']['collectibleTypes'] = $collectibleTypes;
			$data['data']['specializedTypes'] = $specializedTypes;
			$this -> set('aCollectibleTypesData', $data);
		} else {
			$this -> set('aCollectibleTypesData', array('success' => array('isSuccess' => false), 'isTimeOut' => false));
		}
	}

}
?>