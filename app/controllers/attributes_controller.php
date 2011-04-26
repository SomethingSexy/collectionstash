<?php
class AttributesController extends AppController {

	var $name = 'Attributes';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');
	function index() {
		//$this->data = $this->Attribute->generatetreelist(null, null, null, '&nbsp;&nbsp;&nbsp;');

		//$this->Attribute->reorder(array('field' => 'Attribute.name', 'order' =>'ASC'));
		$this -> data = $this -> Attribute -> find('threaded');

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

		debug($this -> data);

		$this -> Attribute -> getAttributeList();
	}

	function add() {

		// $data['Attribute']['parent_id'] =  3;
		$data['Attribute']['name'] = 'Test';
		$this -> Attribute -> save($data);
		$this -> render(false);
	}

	public function getAttributeList($id =null) {
		if($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
			//$this->render('../json/add');
		}

		$attributes = $this -> Attribute -> getAttributeList($id);

		$this -> set('aAttributes', $attributes);
	}

}
?>
