<?php
class Tag extends AppModel {
	public $name = 'Tag';
	public $hasMany = array('CollectiblesTag');
	public $actsAs = array('Containable');

	var $validate = array('tag' => array('rule' => '/^[\\w\\s-.]+$/', 'required' => true, 'message' => 'Invalid characters'));

	/**
	 * This processes a single Collectibles Tag
	 */
	public function processTag($tag) {
		$retVal['CollectiblesTag']['collectible_id'] = $tag['CollectiblesTag']['collectible_id'];
		if (isset($tag['CollectiblesTag']['Tag']) && isset($tag['CollectiblesTag']['Tag']['tag']) && !empty($tag['CollectiblesTag']['Tag']['tag'])) {
			$tagResult = $this -> find("first", array('contain' => false, 'conditions' => array('Tag.tag' => strtolower($tag['CollectiblesTag']['Tag']['tag']))));
			if (!empty($tagResult)) {
				$retVal['CollectiblesTag']['tag_id'] = $tagResult['Tag']['id'];

			} else {
				//For now just set the active to true, later we might want to turn this back to not auto activate.
				$tagToSave['Tag'] = array();
				$tagToSave['Tag']['active'] = 1;
				$tagToSave['Tag']['tag'] = strtolower($tag['CollectiblesTag']['Tag']['tag']);
				$this -> create();
				if ($this -> save($tagToSave)) {
					$tagId = $this -> id;
					$retVal['CollectiblesTag']['tag_id'] = $tagId;
				}
			}
		}

		return $retVal;
	}

}
?>
