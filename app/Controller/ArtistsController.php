<?php
class ArtistsController extends AppController {

	public $helpers = array('Html', 'Form', 'FileUpload.FileUpload', 'Minify', 'Js', 'Time');

	public function persons() {
		$this->autoRender = false;
		$query = $this -> request -> query['query'];
		$persons = $this -> Artist -> find('all', array('fields' => array('Artist.id', 'Artist.name'), 'conditions' => array('LOWER(Artist.name) LIKE' => strtolower($query) . '%')));


		$this->response->body(json_encode(Set::extract('/Artist/.', $persons)));
	}

	public function index($id = null) {

		if (!isset($id) || !is_numeric($id)) {

			$this -> redirect("/");
			return;
		}
		$artist = $this -> Artist -> find("first", array('conditions' => array('Artist.id' => $id), 'contain' => false));
		if (empty($artist)) {
			$this -> redirect("/");
			return;
		}

		$this -> set(compact('artist'));
		$joins = array();
		array_push($joins, array('table' => 'artists_collectibles', 'alias' => 'ArtsitsCollectible', 'type' => 'inner', 'conditions' => array('Collectible.id = ArtsitsCollectible.collectible_id')));
		array_push($joins, array('table' => 'artists', 'alias' => 'Artist', 'type' => 'inner', 'conditions' => array('ArtsitsCollectible.artist_id = Artist.id')));

		$this -> loadModel('Collectible');

		$this -> paginate = array('joins' => $joins, 'limit' => 25, 'conditions' => array('Artist.id' => $id, 'Collectible.status_id' => 4), 'contain' => array('CollectiblesUpload' => array('Upload'), 'Manufacture', 'User', 'Collectibletype', 'ArtistsCollectible' => array('Artist')));
		$collectibles = $this -> paginate('Collectible');

		$this -> set(compact('collectibles'));

		$this -> layout = 'fluid';
	}

}
?>