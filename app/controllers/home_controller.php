<?php
class HomeController extends AppController {

	var $name = 'Home';
	var $helpers = array('Html', 'Ajax');
	var $uses = array();
	public function index() {
		$this->loadModel('Collectible');
		$randomCollectibleIds = $this -> Collectible -> find('list', array('fields' => 'id', 'order' => 'RAND()', 'limit' => 2));
		debug($randomCollectibleIds);
		$audios = $this -> Collectible -> find('all', array('contain' => array('Upload'), 'conditions' => array('Collectible.id' => $randomCollectibleIds), 'order' => 'RAND()'));
		debug($audios);
	}

}
?>
