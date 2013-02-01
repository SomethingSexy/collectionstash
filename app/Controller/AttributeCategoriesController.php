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

	public function get() {
		$returnData = $this -> AttributeCategory -> get();

		$this -> autoRender = false;
		$view = new View($this, false);
		$view -> set('categories', $returnData['response']['data']);

		/* Grab output into variable without the view actually outputting! */
		$view_output = $view -> render('tree');
		$returnData['response']['data'] = $view_output;
		$this -> autoRender = true;

		$this -> set(compact('returnData'));
	}

}
?>
