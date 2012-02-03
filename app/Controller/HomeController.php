<?php
App::uses('Sanitize', 'Utility');
class HomeController extends AppController {

	public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');
	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function index() {
		//Note: This page is displaying goofy when it gets an image that is already width 200...doesn't work...which then causes the rest of the images not to resize..still a defect in that resize code
		$this -> loadModel('Collectible');
		//$randomCollectibleIds = $this -> Collectible -> find('list', array('fields' => 'id', 'order' => 'RAND()', 'limit' => 5));
		//debug($randomCollectibleIds);

		//Updated to use modified desc, instead of created so I will get the latest ones added.
		$randomCollectibles = $this -> Collectible -> find('all', array('limit' => 4, 'conditions' => array('Collectible.state' => '0'), 'contain' => array('Upload', 'Manufacture', 'Collectibletype', 'License'), 'order' => array('Collectible.modified' => 'desc')));

		//$randomCollectibles = $this -> Collectible -> find('all', array('contain' => array('Upload', 'Manufacture'), 'conditions' => array('Collectible.id' => $randomCollectibleIds), 'order' => 'RAND()'));
		debug($randomCollectibles);

		$this -> set(compact('randomCollectibles'));

		// $this -> loadModel('Manufacture');
		//
		// $manufactures = $this -> Manufacture -> getManufactures();
		// //debug($manufactures);
		// $this -> set(compact('manufactures'));
		// $this -> loadModel('License');
		// $licenses = $this -> License -> getLicenses();
		// //debug($licenses);
		// $this -> set(compact('licenses'));
	}

}
?>
