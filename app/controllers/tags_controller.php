<?php
class TagsController extends AppController {

	var $name = 'Tags';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	/**
	 * I think for now, I will grab all of the possible tags and dump them onto the page in some json object, or at least
	 * the first hit will do that.  Then I will use a jquery plugin to search through that.
	 * 
	 * I will have to decide if I want to do automatic tag adds for collectibles or make that a separate area to add a tag
	 * that does not exist.  Will need approval around that if I do.  
	 */
	public function getTagList() {
		if($this -> RequestHandler -> isAjax()) {
			Configure::write('debug', 0);
			//$this->render('../json/add');
		}
		//debug($this->params);
		$query = $this -> params['named']['query'];
		$tags = $this -> Tag -> find('list', array('fields' => array('Tag.id', 'Tag.tag'), 'conditions'=> array('Tag.tag LIKE' => $query . '%')));
		$keys = array_keys($tags);
		$values = array_values($tags);
		//debug($keys);
		//debug($values);
		//debug($tags);
		$this->set('aTags', array('suggestions'=>$values, 'data'=>$keys, 'query'=> $query));
	}

}
?>