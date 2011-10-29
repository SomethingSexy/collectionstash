<?php
class SeriesController extends AppController {

	var $name = 'Series';
	var $helpers = array('Html', 'Ajax', 'Minify.Minify');
	var $components = array('RequestHandler');

	function add() {

		$data['Series']['parent_id'] = null;
		$data['Series']['name'] = 'Series 1';
		$this -> Series -> save($data);
		$this -> render(false);
	}

}
?>