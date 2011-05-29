<?php
class HomeController extends AppController {

	var $name = 'Home';
	var $helpers = array('Html', 'Ajax', 'FileUpload.FileUpload');
	var $uses = array();
	
	public function index() {
		$this->loadModel('Collectible');
		$randomCollectibleIds = $this -> Collectible -> find('list', array('fields' => 'id', 'order' => 'RAND()', 'limit' => 5));
		debug($randomCollectibleIds);
		$randomCollectibles = $this -> Collectible -> find('all', array('contain' => array('Upload'), 'conditions' => array('Collectible.id' => $randomCollectibleIds), 'order' => 'RAND()'));
		debug($randomCollectibles);
		
		$this -> set(compact('randomCollectibles'));
	}

}
?>
