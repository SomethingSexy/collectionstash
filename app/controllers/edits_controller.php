<?php

class EditsController extends AppController {

	var $name = 'Edits';
	var $helpers = array('Html', 'Ajax');
	var $components = array('RequestHandler');

	function admin_index() {
		$this -> checkLogIn();
		$this -> checkAdmin();

		$this -> paginate = array('contain' => array('UploadEdit' => array('fields' => array('id')), 'User', 'CollectibleEdit' => array('fields' => array('id'))), "limit" => 25);

		$edits = $this -> paginate('Edit');
		//TODO might want to think about doing a behavior of some sort for this
		//TODO actually, for all edits, Upload and Attributes, I should always link up the collectible Id this is for
		//add this to the edit model
		foreach ($edits as &$edit) {
			if (!empty($edit['CollectibleEdit']['id'])) {
				$edit['type'] = __('Collectible', true);
				$edit['type_id'] = $edit['CollectibleEdit']['id'];
				unset($edit['UploadEdit']);
			} else if (!empty($edit['UploadEdit']['id'])) {
				$edit['type'] = __('Upload', true);
				$edit['type_id'] = $edit['UploadEdit']['id'];
				unset($edit['CollectibleEdit']);
			}
		}
		debug($edits);
		$this -> set('edits', $edits);
	}

}
?>