<?php
/**
 * This will do updates to entited for existing rows
 * 
 * TODO: Need to update this to handle updating attributes after they have been converted over
 */
class UpdateEntitiesShell extends AppShell {
	public $uses = array('Collectible', 'Stash');

	public function main() {
		$collectibles = $this -> Collectible -> find("all", array('contain' => false, 'conditions' => array('Collectible.entity_type_id' => 0)));

		foreach ($collectibles as $key => $value) {
			$entity = array();
			$entity['EntityType']['type'] = 'collectible';
			$this -> Collectible -> EntityType -> create();
			if ($this -> Collectible -> EntityType -> save($entity)) {
				$entityTypeId = $this -> Collectible -> EntityType -> id;
				$this -> Collectible -> id = $value['Collectible']['id'];
				$this -> Collectible -> saveField('entity_type_id', $entityTypeId, false);
			} else {

			}

		}

		$stashes = $this -> Stash -> find("all", array('contain' => false, 'conditions' => array('Stash.entity_type_id' => 0)));

		foreach ($stashes as $key => $value) {
			$alreadyExist = $this -> Stash -> EntityType -> find("first", array('conditions' => array('EntityType.type' => 'stash', 'EntityType.type_id' => $value['Stash']['id'])));
			debug($alreadyExist);
			if (!is_null($alreadyExist) && !empty($alreadyExist)) {
				$this -> Stash -> id = $value['Stash']['id'];
				$this -> Stash -> saveField('entity_type_id', $alreadyExist['EntityType']['id'], false);
			} else {
				$entity['EntityType']['type'] = 'stash';
				$this -> Stash -> EntityType -> create();
				if ($this -> Stash -> EntityType -> save($entity)) {
					$entityTypeId = $this -> Stash -> EntityType -> id;
					$this -> Stash -> id = $value['Stash']['id'];
					$this -> Stash -> saveField('entity_type_id', $entityTypeId, false);
				} else {

				}
			}
		}

	}

}
?>