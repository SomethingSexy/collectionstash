<?php
class Tag extends AppModel {
	var $name = 'Tag';
	var $hasMany = array('CollectiblesTag');
	var $actsAs = array('Containable');

	/**
	 * Might make more sense in the future to actually add tags to the table when they actually committ the collectible, but works for now.
	 */
	public function processAddTags($tags) {
		$processedTags = array();
		foreach($tags as $key => $value) {
			debug($key);
			debug($value['tag']);
			if(!empty($value['tag'])) {
				$tagResult = $this -> find("first", array('conditions' => array('Tag.tag' => strtolower($value['tag']))));
				debug($tagResult);
				if(!empty($tagResult)) {
					array_push($processedTags, $tagResult['Tag']);
				} else {
					$value['active'] = 0;
					$value['tag'] = strtolower($value['tag']);
					$this -> create();
					if($this -> save($value)) {
						$tagId = $this -> id;
						$addedTag = $this -> findById($tagId);
						array_push($processedTags, $addedTag['Tag']);
					}
				}
			}

		}
		debug($processedTags);

		return $processedTags;
	}

}
?>
