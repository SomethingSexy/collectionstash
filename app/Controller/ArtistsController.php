<?php
class ArtistsController extends AppController {

	public $helpers = array('Html', 'Js', 'Minify');

	public function getArtistList() {
		$query = $this -> request -> query['query'];
		$tags = $this -> Artist -> find('list', array('fields' => array('Artist.id', 'Artist.name'), 'conditions' => array('LOWER(Artist.name) LIKE' => strtolower($query) . '%')));
		$keys = array_keys($tags);
		$values = array_values($tags);
		$this -> set('returnData', array('suggestions' => $values, 'data' => $keys, 'query' => $query));
	}

}
?>