<?php
App::uses('Sanitize', 'Utility');
class CollectiblesTagsController extends AppController {
	public $helpers = array('Html', 'Js', 'Minify');

	//WTF why do I have so many loops
	//Way too much other shit going on, this really needs to get refactored!!!!
	public function edit($collectible_id = null, $adminMode = false) {
		$this -> checkLogIn();
		debug($this -> request -> data);
		if (!isset($collectible_id) && !is_numeric($collectible_id)) {
			$this -> Session -> setFlash(__('Invalid request.', true), null, null, 'error');
			$this -> redirect($this -> referer(), null, true);
		}

		if ($this -> request -> is('post') || $this -> request -> is('put')) {
			$this -> request -> data = Sanitize::clean($this -> request -> data);
			debug($this -> request -> data);
			/*
			 * Check to make sure something was submitted first
			 */
			$success = true;
			$haveAction = false;
			foreach ($this -> request -> data['CollectiblesTag'] as $key => $value) {
				if (isset($value['action']) && $value['action'] !== '' && !$haveAction) {
					$haveAction = true;
				}
			}
			if (!$haveAction) {
				$success = false;
				$this -> Session -> setFlash(__('You should probably change something first before submitting.', true), null, null, 'error');
			}

			if ($success) {
				$tagsToProcess = array();
				$tagsToSave = array();
				$tagsToSave['CollectiblesTag'] = array();
				/*
				 * Find all adds first.  We need to process the tags, either add the tag
				 * or find the existing tag.
				 */
				foreach ($this -> request -> data['CollectiblesTag'] as $key => $value) {
					if (isset($value['action']) && $value['action'] === 'A') {
						array_push($tagsToProcess, $value);
					}
				}

				$newTags = $this -> CollectiblesTag -> Tag -> processAddTags($tagsToProcess);
				if (!empty($this -> CollectiblesTag -> Tag -> validationErrors)) {
					$this -> set('errors', $this -> CollectiblesTag -> Tag -> validationErrors['tag']);
					$success = false;
				} else {
					debug($tagsToSave);
					$confirmTags = array();
					//Save the deletes first, this is kind of a hack by it should allow
					//me to process any delete edits first then do the adds
					foreach ($this -> request -> data['CollectiblesTag'] as $key => $value) {
						if (isset($value['action']) && $value['action'] === 'D') {
							//TODO: Still need to put the tag id on here so we can use it for display purposes later
							$newtag = array();
							$newtag['base_id'] = $value['id'];
							$newtag['tag_id'] = $value['tag_id'];
							$newtag['Action']['action_type_id'] = 4;
							$newtag['collectible_id'] = $collectible_id;
							array_push($tagsToSave['CollectiblesTag'], $newtag);

							$confirmTag = array();
							$confirmTag['Tag']['tag'] = $value['tag'];
							$confirmTag['CollectiblesTag']['action'] = 'D';
							array_push($confirmTags, $confirmTag);
						}
					}
					debug($tagsToSave);
					//Now save all of the add edits
					foreach ($newTags as $key => $value) {
						$newtag = array();
						$newtag['collectible_id'] = $collectible_id;
						//Need to grab the tag id
						$newtag['tag_id'] = $value['Tag']['id'];
						$newtag['Action']['action_type_id'] = 1;
						array_push($tagsToSave['CollectiblesTag'], $newtag);

						$confirmTag = array();
						$confirmTag['Tag']['tag'] = $value['Tag']['tag'];
						$confirmTag['CollectiblesTag']['action'] = 'A';
						array_push($confirmTags, $confirmTag);
					}

					debug($tagsToSave);

					$adminMode = $this -> Session -> read('collectible.edit.admin-mode');
					if (Configure::read('Settings.Collectible.Edit.auto-approve') === true || (isset($adminMode) && $adminMode && $this -> isUserAdmin())) {
						foreach ($tagsToSave['CollectiblesTag'] as $key => $tag) {
							$tagToSave = array();
							$tagToSave['CollectiblesTag'] = $tag;
							$baseId = null;

							if (isset($tag['base_id'])) {
								$baseId = $tag['base_id'];
							}

							$success = $this -> CollectiblesTag -> save($tagToSave);
						}
					} else {

						//TODO: need to handle action on multiple
						foreach ($tagsToSave['CollectiblesTag'] as $key => $tag) {
							$tagToSave = array();
							$tagToSave['CollectiblesTag'] = $tag;
							$baseId = null;

							if (isset($tag['base_id'])) {
								$baseId = $tag['base_id'];
							}
							debug($tag['Action']);
							$success = $this -> CollectiblesTag -> saveEdit($tagToSave, $baseId, $this -> getUserId(), $tag);
						}

					}

				}
			}
			debug($success);
			if ($success || is_array($success)) {
				$this -> Session -> setFlash(__('Your edit has been successfully submitted!', true), null, null, 'success');
				//$this -> redirect(array('controller' => 'collectibles', 'action' => 'view', $collectible_id));
				debug($confirmTags);
				$this -> set('collectibleId', $collectible_id);
				$this -> set('tags', $confirmTags);
				$this -> render('confirm');
			} else {
				$errorTags['CollectiblesTag'] = array();
				foreach ($this -> request -> data['CollectiblesTag'] as $key => $value) {
					$errorTag = array();
					$errorTag['Tag']['tag'] = $value['tag'];

					if (isset($value['id'])) {
						$errorTag['CollectiblesTag']['id'] = $value['id'];
					}

					if (isset($value['action']) && $value['action'] !== '') {
						$errorTag['CollectiblesTag']['action'] = $value['action'];
					}
					array_push($errorTags['CollectiblesTag'], $errorTag);
				}
				debug($errorTags);
				$this -> request -> data = $errorTags['CollectiblesTag'];
			}

		} else {
			if ($adminMode === 'true') {
				if (!$this -> isUserAdmin()) {
					$this -> Session -> write('collectible.edit.admin-mode', false);
				} else {
					$this -> Session -> write('collectible.edit.admin-mode', true);
				}
			} else {
				$this -> Session -> write('collectible.edit.admin-mode', false);
			}
			debug($this -> Session -> read('collectible.edit.admin-mode'));
			$tags = $this -> CollectiblesTag -> find('all', array('conditions' => array('CollectiblesTag.collectible_id' => $collectible_id), 'contain' => array('Tag')));
			$this -> request -> data = $tags;
		}
	}

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