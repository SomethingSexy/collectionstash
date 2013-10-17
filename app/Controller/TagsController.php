<?php
class TagsController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	public function getTagList() {
		$query = $this -> request -> query['query'];
		$tags = $this -> Tag -> find('list', array('fields' => array('Tag.id', 'Tag.tag'), 'conditions' => array('Tag.tag LIKE' => $query . '%', 'Tag.active' => 1)));
		$keys = array_keys($tags);
		$values = array_values($tags);
		$this -> set('aTags', $values);
	}

	public function index() {
		$tags = $this -> Tag -> find('all', array('conditions' => array('Tag.active' => 1), 'contain' => false, 'order' => array('Tag.tag' => 'ASC')));
		$this -> set(compact('tags'));
	}

}
?>