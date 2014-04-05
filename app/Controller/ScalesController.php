<?php
class MerchantsController extends AppController
{
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function data() {
        $this->autoRender = false;
        $query = $this->request->query['query'];
        $scales = $this->Scales->find('all', array('fields' => array('Scales.id', 'Scales.scale'), 'contain' => false, 'conditions' => array('Scales.scale LIKE' => $query . '%')));
        $this->response->body(json_encode(Set::extract('/Scale/.', $scales)));
    }
}
?>