<?php
class Artist extends AppModel {
	public $name = 'Artist';
	public $hasMany = array('ArtistsCollectible');
	public $actsAs = array('Containable');

	var $validate = array('name' => array('rule' => '/^[\\w\\s-.]+$/', 'required' => true, 'message' => 'Invalid characters'));

	function doAfterFind($results, $primary = false) {
		if ($results) {
			$name = strtolower($results['name']);
			$slug = str_replace(' ', '-', $name);
			$results['slug'] = $slug;
		}
		return $results;
	}

	/**
	 * This processes a single Collectibles Tag
	 */
	public function processArtist($tag) {
		$retVal['ArtistsCollectible']['collectible_id'] = $tag['ArtistsCollectible']['collectible_id'];
		if (isset($tag['ArtistsCollectible']['Artist']) && isset($tag['ArtistsCollectible']['Artist']['name']) && !empty($tag['ArtistsCollectible']['Artist']['name'])) {
			$tagResult = $this -> find("first", array('contain' => false, 'conditions' => array('LOWER(Artist.name)' => strtolower($tag['ArtistsCollectible']['Artist']['name']))));
			if (!empty($tagResult)) {
				$retVal['ArtistsCollectible']['artist_id'] = $tagResult['Artist']['id'];

			} else {
				//For now just set the active to true, later we might want to turn this back to not auto activate.
				$tagToSave['Artist'] = array();
				$tagToSave['Artist']['active'] = 1;
				$tagToSave['Artist']['name'] = $tag['ArtistsCollectible']['Artist']['name'];
				$this -> create();
				if ($this -> save($tagToSave)) {
					$tagId = $this -> id;
					$retVal['ArtistsCollectible']['artist_id'] = $tagId;
				}
			}
		}

		return $retVal;
	}

}
?>
