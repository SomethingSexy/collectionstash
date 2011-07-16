<?php
class TagsController extends AppController {

	var $name = 'Tags';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	public function getTagList() {
		if($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
			//$this->render('../json/add');
		}
		//debug($this->params);
		$query = $this -> params['named']['query'];
		$tags = $this -> Tag -> find('list', array('fields' => array('Tag.id', 'Tag.tag'), 'conditions' => array('Tag.tag LIKE' => $query . '%', 'Tag.active' => 1)));
		$keys = array_keys($tags);
		$values = array_values($tags);
		//debug($keys);
		//debug($values);
		//debug($tags);
		$this -> set('aTags', array('suggestions' => $values, 'data' => $keys, 'query' => $query));
	}
}
?>