<?php
/**
 * This should be turned into a generic model type object.  Not sure I 100% agree with this type of design but it will
 * prevent me from having a whole crap ton of extra tables.
 *
 * This will be used to store references to different models to link up comments and subscriptions, or anything else that needs to dynamically links to models
 *
 * I will be renaming this model and table so I don't have to make existing changes to the database
 *
 * This model will contain additional properties about that relationship, for example it contains a comment_count, to indicate how many comments
 * this entity has.
 *
 * TODO:
 * Do I want to tie directly, every entity to the entity model? via an entity_type_id in that entity model?
 *
 * There wil be only one entry per model
 *
 * This would help make subscriptions a faster look up, and not have to do it manually each time
 *
 * Would it help with comments at all?
 *
 * I can write a shell script that will loop through ALL collectibles, add the entity type and then update the collectible with the id
 *
 * TODO: Update Collectible model so it belongs to an EntityType, and then in the beforeSave, make sure we are adding a new entity model
 *
 * Although it might have to be an afteSave because we will need the collectible id to be able to save the entity and then update the collectible
 *
 * It might be worth it in the end, for the speed of looking up comments as well, instead of me having to find the entity id,and then the comments, I could
 * grab the entity_type_id, and then look up all of the comments or subscriptions based on that.
 *
 * If I do this then I believe I can remove the type_id column and just keep the type column, so I can still go both ways.  This means
 */
class EntityType extends AppModel {
	public $name = 'EntityType';
	public $hasMany = array('Comment');
	//EntityType only has one and the id belongs in the stash/collectible
	public $hasOne = array('Stash', 'Collectible');
	public $actsAs = array('Containable');

	public function afterFind($results, $primary = false) {
		foreach ($results as $key => &$val) {
			if ($val['EntityType']['type'] === 'stash') {
				unset($results[$key]['Collectible']);
			}
		}
		return $results;
	}

	/**
	 * This will find an entity, if it doesn't exist, it will create one and return the one that was created
	 */
	public function getEntity($type_id, $type) {
		$retVal = $this -> find('first', array('contain' => false, 'conditions' => array('EntityType.type' => $type, 'EntityType.type_id' => $type_id)));

		if (empty($retVal)) {
			$entityTypeForSave = array();
			$entityTypeForSave['EntityType']['type'] = $type;
			$entityTypeForSave['EntityType']['type_id'] = $type_id;
			if ($this -> save($entityTypeForSave)) {
				$retVal['EntityType']['id'] = $this -> id;
			} else {
				$retVal = false;
			}
		}

		return $retVal;
	}

	//Lame ass name, this will get get the entity core and related type object
	public function getEntityCore($entityTypeId) {
		return $this -> find("first", array('contain' => array('Stash' => array('User'), 'Collectible'), 'conditions' => array('EntityType.id' => $entityTypeId)));
	}

	/**
	 * Get's the owner of this entity, based on the entity object
	 */
	public function getEntityOwner($entity) {
		$ownerId = null;
		if ($entity['EntityType']['type'] === 'stash') {
			$stash = $this -> Comment -> User -> Stash -> find("first", array('conditions' => array('Stash.entity_type_id' => $entity['EntityType']['id'])));
			if (!empty($stash)) {
				$ownerId = $stash['Stash']['user_id'];
			}
		}

		return $ownerId;
	}

}
