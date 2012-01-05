<?php
App::uses('Sanitize', 'Utility');
class SpecializedTypesController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify.Minify');

	public function getSpecializedTypesData() {
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