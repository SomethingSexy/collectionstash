<?php
App::uses('Sanitize', 'Utility');
class CollectiblesTagsController extends AppController {
	public $helpers = array('Html', 'Js', 'Minify');

	function admin_approval($editId = null, $tagEditId = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($editId && is_numeric($editId) && $tagEditId && is_numeric($tagEditId)) {
			$this -> set('uploadEditId', $tagEditId);
			$this -> set('editId', $editId);
			if (empty($this -> request -> data)) {
				$uploadEditVersion = $this -> CollectiblesTag -> findEdit($tagEditId);
				debug($uploadEditVersion);
				if (!empty($uploadEditVersion)) {
					$tag = array();
					$tag['CollectiblesTag'] = $uploadEditVersion['CollectiblesTagEdit'];
					$tag['Action'] = $uploadEditVersion['Action'];
					debug($tag['CollectiblesTag']['tag_id']);
					$tagForEdit = $this -> CollectiblesTag -> Tag -> find("first", array('contain' => false, 'conditions' => array('Tag.id' => $tag['CollectiblesTag']['tag_id'])));
					debug($tagForEdit);
					$tag['Tag'] = $tagForEdit['Tag'];
					debug($tag);
					$this -> set('tag', $tag);
				} else {
					//uh fuck you
					$this -> redirect('/');
				}
			}

		} else {
			$this -> redirect('/');
		}
	}

}
?>