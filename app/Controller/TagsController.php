<?php
class TagsController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	public function getTagList() {
		if ($this -> request -> isAjax()) {
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

	public function index() {
		$tags = $this -> Tag -> find('all', array('conditions' => array('Tag.active' => 1), 'contain' => false, 'order' => array('Tag.tag' => 'ASC')));
		debug($tags);
		$this -> set(compact('tags'));
	}

}
?>