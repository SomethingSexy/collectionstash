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
		//TODO update this to return the levels and lists
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

	public function add() {
		// $data = array('Figure', 'Diorama', 'Prop Replica', 'Bust', 'Maquette', 'Ornament', 'Statue');
		// foreach ($data as $key => $value) {
		// $data['Collectibletype']['name'] = $value;
		// $this -> Collectibletype -> create();
		// $this -> Collectibletype -> save($data);
		// // }
		// $data = array();
		// $data['Collectibletype']['parent_id'] = '1';
		// $data['Collectibletype']['name'] = 'Figure Accessory';
		// $this -> Collectibletype -> create();
		// $this -> Collectibletype -> save($data);
// 
		// $this -> render(false);
	}

}
?>