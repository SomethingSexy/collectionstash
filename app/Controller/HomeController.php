<?php
App::uses('Sanitize', 'Utility');
class HomeController extends AppController {

	public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');
	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function index() {
		if ($this -> isLoggedIn()) {
			$this -> redirect(array('controller' => 'collectibles', 'action' => catalog));
		}

		// $this -> loadModel('Collectible');
		// //Updated to use modified desc, instead of created so I will get the latest ones added.
		// $recentlyAddedCollectibles = $this -> Collectible -> find('all', array('limit' => 12, 'conditions' => array('Collectible.state' => '0'), 'contain' => array('Upload', 'Manufacture', 'Collectibletype', 'License'), 'order' => array('Collectible.modified' => 'desc')));
		// $this -> set(compact('recentlyAddedCollectibles'));
// 
		// $this -> loadModel('Manufacture');
		// $manufactures = $this -> Manufacture -> find('all', array('limit' => 10, 'contain' => false, 'order' => array('Manufacture.collectible_count' => 'desc')));
		// $this -> set(compact('manufactures'));
// 
		// $this -> loadModel('License');
		// $licenses = $this -> License -> find('all', array('limit' => 10, 'contain' => false, 'order' => array('License.collectible_count' => 'desc')));
		// $this -> set(compact('licenses'));

	}

}
?>
