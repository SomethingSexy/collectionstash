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
	
	public function index(){
		$tags = $this -> Tag -> find('all', array('conditions' => array('Tag.active' => 1), 'contain'=> false, 'order' => array('Tag.tag' => 'ASC')));	
		debug($tags);
		$this->set(compact('tags'));
		$this -> loadModel('Manufacture');
		$manufactures = $this -> Manufacture -> getManufactures();
		debug($manufactures);
		$this -> set(compact('manufactures'));
		$this -> loadModel('Collectibletype');
		$collectibletypes = $this -> Collectibletype -> getCollectibleTypeSearchData();
		$this -> set(compact('collectibletypes'));
	}
}
?>