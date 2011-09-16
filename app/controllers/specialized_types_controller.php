<?php
App::import('Sanitize');
class SpecializedTypesController extends AppController {

	var $name = 'SpecializedTypes';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	public function getSpecializedTypesData() {
		if ($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
		}

		if (!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			$specializedTypes = $this -> SpecializedType -> CollectibletypesManufactureSpecializedType -> getSpecializedTypes($this -> data['manufacture_id'], $this -> data['collectibletype_id']);
			$data = array();
			$data['success'] = array('isSuccess' => true);
			$data['isTimeOut'] = false;
			$data['data'] = array();
			$data['data']['specializedTypes'] = $specializedTypes;
			$this -> set('aSpecializedTypesData', $data);
		} else {
			$this -> set('aSpecializedTypesData', array('success' => array('isSuccess' => false), 'isTimeOut' => false));
		}
	}
}
?>