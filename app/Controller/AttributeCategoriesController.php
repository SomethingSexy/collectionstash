<?php
/**
 * This will need admin classes for adding new categories
 *
 * I do not think I will be opening that up to the public just yet
 *
 * Remember when adding new categories we will need to update the path name
 *
 * This will also handle moving categories to other categories for us...admin
 * 
 * 		//Shit is completely out of order, need to fix this TODO
		$this -> Attribute -> AttributeCategory -> recover('parent');
 */
class AttributeCategoriesController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	// function add() {
	//
	// $data['Attribute']['parent_id'] = '1';
	// $data['Attribute']['name'] = 'Body';
	// $this -> Attribute -> save($data);
	// $this -> render(false);
	// }

	public function getAttributeList($id = null) {
		$attributes = $this -> AttributeCategory -> getAttributeList($id);
		debug($attributes);
		$this -> set('aAttributes', $attributes);
	}
}
?>
