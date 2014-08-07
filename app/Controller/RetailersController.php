<?php
class RetailersController extends AppController {

	public $helpers = array('Html', 'Form', 'Minify', 'Js', 'Time');

	public function retailers() {
		$this->autoRender = false;
		$query = $this -> request -> query['query'];
		$retailers = $this -> Retailer -> find('all', array('fields' => array('Retailer.id', 'Retailer.name'), 'conditions' => array('LOWER(Retailer.name) LIKE' => strtolower($query) . '%')));
		$this->response->body(json_encode(Set::extract('/Retailer/.', $retailers)));
	}
}
?>