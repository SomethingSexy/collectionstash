<?php
class MerchantsController extends AppController
{
    
    public $helpers = array('Html', 'Js', 'Minify');
    
    public function merchants() {
        $this->autoRender = false;
        $query = $this->request->query['query'];
        $merchants = $this->Merchant->find('all', array('fields' => array('Merchant.id', 'Merchant.name'), 'conditions' => array('Merchant.name LIKE' => $query . '%')));
        $this->response->body(json_encode(Set::extract('/Merchant/.', $merchants)));
    }
}
?>