<?php
class AttributesController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	function index() {
		//$this->data = $this->Attribute->generatetreelist(null, null, null, '&nbsp;&nbsp;&nbsp;');

		//$this->Attribute->reorder(array('field' => 'Attribute.name', 'order' =>'ASC'));
		$this -> request -> data = $this -> Attribute -> find('threaded');

		// $this->data = $this->Attribute->find('list',
		//   array(
		//     'fields' => array('Attribute.id', 'Attribute.name'),
		//     'recursive' => 0,
		//     'conditions' => array(
		//       //'ParentCategory.parent_id' => 0
		//     ),
		//     'order' => 'Attribute.lft ASC'
		//   ));
		//
		//

		debug($this -> request -> data);

		$this -> Attribute -> getAttributeList();
	}

	// function add() {
// 
		// $data['Attribute']['parent_id'] = '1';
		// $data['Attribute']['name'] = 'Body';
		// $this -> Attribute -> save($data);
		// $this -> render(false);
	// }

	public function getAttributeList($id = null) {
		$attributes = $this -> Attribute -> getAttributeList($id);
		debug($attributes);
		$this -> set('aAttributes', $attributes);
	}

}
?>
