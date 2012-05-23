<?php
class LatestComment extends AppModel {
	public $name = 'LatestComment';
	public $belongsTo = array('EntityType' => array('contain' => array('Stash')), 'Comment', 'User' => array('fields' => array('id', 'username')));
	public $actsAs = array('Containable');

	/**
	 * This saves the comment as the latest comment for the given comment type.  The comment
	 * type is unique in this table, we only want to see the latest comments for
	 * each comment type.
	 *
	 * This will check to see if one exists, if it doesn't then it will create, otherwise update
	 */
	public function saveLatest($comment) {
		$retVal = true;
		//Hmm, going to try this to force a transaction around the find and update/save
		$dataSource = $this -> getDataSource();
		$dataSource -> begin();
		$latestComment = array();
		$latestComment['LatestComment']['comment_id'] = $comment['Comment']['id'];
		$latestComment['LatestComment']['entity_type_id'] = $comment['Comment']['entity_type_id'];
		$latestComment['LatestComment']['user_id'] = $comment['Comment']['user_id'];

		$existingLatestComment = $this -> find("first", array('contain' => false, 'conditions' => array('LatestComment.entity_type_id' => $comment['Comment']['entity_type_id'])));
		if (!empty($existingLatestComment)) {
			$latestComment['LatestComment']['id'] = $existingLatestComment['LatestComment']['id'];
			$this -> id = $existingLatestComment['LatestComment']['id'];
		}

		$comment = $latestComment;
		if (!$this -> save($comment)) {
			$retVal = false;
		}
		//Commit regardless
		$dataSource -> commit();
		return $retVal;
	}

	public function afterFind($results, $primary = false) {
		foreach ($results as $key => $val) {
			if (isset($results[$key]['LatestComment'])) {
				/*
				 * Grab the entity Core, this will properly retrieve the data for this entity
				 *
				 * There might be a better way to do this more automagically but this will work for now.
				 */
				$entity = $this -> EntityType -> getEntityCore($results[$key]['LatestComment']['entity_type_id']);
				if ($entity['EntityType']['type'] === 'stash') {
					$results[$key]['EntityType'] = $entity['EntityType'];
					$results[$key]['EntityType']['Stash'] = $entity['Stash'];
				} else if ($entity['EntityType']['type'] === 'collectible') {
					$results[$key]['EntityType'] = $entity['EntityType'];
					$results[$key]['EntityType']['Collectible'] = $entity['Collectible'];
				}
			}
		}
		return $results;
	}

}
?>
