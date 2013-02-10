<?php
App::uses('Sanitize', 'Utility');
class ArtistsCollectiblesController extends AppController {
	public $helpers = array('Html', 'Js', 'Minify');

	function admin_approval($editId = null, $artistEditId = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($editId && is_numeric($editId) && $artistEditId && is_numeric($artistEditId)) {
			$this -> set('editId', $editId);
			if (empty($this -> request -> data)) {
				$artistEditVerion = $this -> ArtistsCollectible -> findEdit($artistEditId);
				if (!empty($artistEditVerion)) {
					$artist = array();
					$artist['ArtistsCollectible'] = $artistEditVerion['ArtistsCollectibleEdit'];
					$artist['Action'] = $artistEditVerion['Action'];
					$artistForEdit = $this -> ArtistsCollectible -> Artist -> find("first", array('contain' => false, 'conditions' => array('Artist.id' => $artist['ArtistsCollectible']['artist_id'])));
					$artist['Artist'] = $artistForEdit['Artist'];
					$this -> set(compact('artist'));
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