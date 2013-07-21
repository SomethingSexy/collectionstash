<?php
App::uses('Sanitize', 'Utility');
class HomeController extends AppController {

	public $helpers = array('Html', 'FileUpload.FileUpload', 'Minify');
	public function beforeFilter() {
		parent::beforeFilter();
	}

	/**
	 * This is going to do nothing for now.  The page has static text, unless the user is logged in then
	 * they will see the catalog page.
	 */
	public function index() {
		if ($this -> isLoggedIn()) {
			$this -> redirect(array('controller' => 'user', 'action' => 'home'));
		} else {
			$this -> layout = 'home';
		}
	}

}
?>
