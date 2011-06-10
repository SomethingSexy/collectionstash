<?php
class HomeController extends AppController {

	var $name = 'Home';
	var $helpers = array('Html', 'Ajax', 'FileUpload.FileUpload');
	var $uses = array();
	
	public function index() {
		//Note: This page is displaying goofy when it gets an image that is already width 200...doesn't work...which then causes the rest of the images not to resize..still a defect in that resize code
		$this->loadModel('Collectible');
		//$randomCollectibleIds = $this -> Collectible -> find('list', array('fields' => 'id', 'order' => 'RAND()', 'limit' => 5));
		//debug($randomCollectibleIds);
		
		$randomCollectibles = $this -> Collectible -> find('all', array('limit'=>5,'contain' => array('Upload', 'Manufacture'), 'order' => array('created' => 'desc')));
		
		//$randomCollectibles = $this -> Collectible -> find('all', array('contain' => array('Upload', 'Manufacture'), 'conditions' => array('Collectible.id' => $randomCollectibleIds), 'order' => 'RAND()'));
		debug($randomCollectibles);
		
		$this -> set(compact('randomCollectibles'));
	}

}
?>
