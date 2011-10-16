<?php
App::import('Sanitize');
class CollectibletypesController extends AppController {

	var $name = 'Collectibletypes';
	var $helpers = array('Html', 'Ajax', 'Minify.Minify');
	var $components = array('RequestHandler');

	public function getCollectibletypesData() {
		if ($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
		}
		//TODO update this to return the levels and lists
		if (!empty($this -> data)) {
			$this -> data = Sanitize::clean($this -> data, array('encode' => false));
			$manufacturerId = $this -> data['manufacture_id'];
			$collectibleTypeId = $this -> data['collectibletype_id'];
			if ($this -> data['init'] === 'true') {
				//This should return arrays of each level of collectibles types from the path of the given collectible type id.  It
				//will also return the collectible that is selected in the array
				$collectibleTypes = $this -> Collectibletype -> CollectibletypesManufacture -> getCollectibleTypesPaths($manufacturerId, $collectibleTypeId);
			} else {
				$collectibleTypes = $this -> Collectibletype -> CollectibletypesManufacture -> getCollectibleTypesChildren($manufacturerId, $collectibleTypeId);
				//$collectibleTypes = $this -> Collectibletype -> children($collectibleTypeId, true, array('Collectibletype.id', 'Collectibletype.name'));
			}

			$data = array();
			$data['success'] = array('isSuccess' => true);
			$data['isTimeOut'] = false;
			$data['data'] = array();
			$data['data']['collectibleTypes'] = $collectibleTypes;
			// $data['data']['specializedTypes'] = $specializedTypes;
			$this -> set('aCollectibleTypesData', $data);
		} else {
			$this -> set('aCollectibleTypesData', array('success' => array('isSuccess' => false), 'isTimeOut' => false));
		}
	}

	public function test(){
		$collectibleTypes = $this -> Collectibletype -> CollectibletypesManufacture -> getCollectibleTypesPaths('2', '1');
	}

	public function add() {
	// $data = array('Figure', 'Diorama', 'Prop Replica', 'Bust', 'Maquette', 'Ornament', 'Statue');
	// foreach ($data as $key => $value) {
	// $data['Collectibletype']['name'] = $value;
	// $this -> Collectibletype -> create();
	// $this -> Collectibletype -> save($data);
	// // }
	$data = array();
	$data['Collectibletype']['parent_id'] = null;
	$data['Collectibletype']['name'] = 'Vinyl Figure';
	$this -> Collectibletype -> create();
	$this -> Collectibletype -> save($data);
// 	
	// $this -> Collectibletype -> id = 3;
	// $this -> Collectibletype -> save(array('parent_id' => 11));
	
	
	//
	// $this -> render(false);
	}

}
?>