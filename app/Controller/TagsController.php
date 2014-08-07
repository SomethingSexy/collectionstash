<?php
class TagsController extends AppController
{
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function tags() {
        $this->autoRender = false;
        $query = $this->request->query['query'];
        $tags = $this->Tag->find('all', array('fields' => array('Tag.id', 'Tag.tag'), 'conditions' => array('Tag.tag LIKE' => $query . '%', 'Tag.active' => 1)));
        $this->response->body(json_encode(Set::extract('/Tag/.', $tags)));
    }
    
    public function index() {
        $tags = $this->Tag->find('all', array('conditions' => array('Tag.active' => 1), 'contain' => false, 'order' => array('Tag.tag' => 'ASC')));
        $this->set(compact('tags'));
    }
}
?>