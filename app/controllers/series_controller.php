<?php
class SeriesController extends AppController {

	var $name = 'Series';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	function add() {

		$data['Series']['parent_id'] = null;
		$data['Series']['name'] = 'Phase IV';
		$this -> Series -> save($data);
		$this -> render(false);
	}
}
?>