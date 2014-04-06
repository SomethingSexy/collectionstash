<?php
class ScalesController extends AppController
{
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function data() {
        $this->autoRender = false;
        $query = $this->request->query['query'];
        $scales = $this->Scale->find('all', array('fields' => array('Scale.id', 'Scale.scale'), 'contain' => false, 'conditions' => array('Scale.scale LIKE' => $query . '%')));
        $this->response->body(json_encode(Set::extract('/Scale/.', $scales)));
    }
}
?>