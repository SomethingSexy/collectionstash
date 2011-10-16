<?php
class AttributesCollectiblesEditsController extends AppController {

	var $name = 'AttributesCollectiblesEdits';
	var $helpers = array('Html', 'Ajax', 'Minify.Minify');
	var $components = array('RequestHandler');

	/*
	 * In the future when doing this edits, we are going to have to make sure that these parts are not being
	 * used in a custom when we delete them
	 */
	function edit($id = null, $adminMode = false) {
		$this -> checkLogIn();

		if (!$id && !is_numeric($id) && empty($this -> data)) {
			$this -> Session -> setFlash(__('Invalid collectible', true));
			//TODO go somewhere
			$this -> redirect($this -> referer());
		}

		if (!empty($this -> data)) {
			debug($this -> data);
			if (isset($this -> data['AttributesCollectible'])) {
				$isValid = true;
				//TODO this does not seem right
				foreach ($this -> data['AttributesCollectible'] as $key => $attribue) {
					//If it is being deleted, I do not care to validate it.
					if ($attribue['action'] !== 'D') {
						$this -> AttributesCollectiblesEdit -> AttributesCollectible -> set($attribue);
						//debug($this -> AttributesCollectible);
						if ($this -> AttributesCollectiblesEdit -> AttributesCollectible -> validates()) {

						} else {
							//If one is invalid set it to false
							$isValid = false;
							debug($this -> AttributesCollectiblesEdit -> AttributesCollectible -> invalidFields());
							$this -> set('errors', $this -> AttributesCollectiblesEdit -> AttributesCollectible -> validationErrors);
						}
					}
				}

				//if everything is valid, then lets do our updates
				if ($isValid) {
					$adminMode = $this -> Session -> read('collectible.edit.admin-mode');
					if (Configure::read('Settings.Collectible.Edit.auto-approve') === true || (isset($adminMode) && $adminMode && $this -> isUserAdmin())) {
						//TODO move this to the model, validate we are removing the correct attributes?
						foreach ($this -> data['AttributesCollectible'] as $key => $attribue) {
							//debug($this -> AttributesCollectible);
							if ($attribue['action'] === 'D') {
								$this -> AttributesCollectiblesEdit -> AttributesCollectible -> id = $attribue['id'];
								//If we are deleting then set the active state to zero, I believe this is for history purposes.
								//however, it looks like it is changing the originally added one...need to verify this.
								if ($this -> AttributesCollectiblesEdit -> AttributesCollectible -> save(array('active' => 0), false, array('active'))) {

								}
							} else if ($attribue['action'] === 'A') {
								$attribue['collectible_id'] = $id;
								$this -> AttributesCollectiblesEdit -> AttributesCollectible -> create();
								$this -> AttributesCollectiblesEdit -> AttributesCollectible -> set($attribue);
								if ($this -> AttributesCollectiblesEdit -> AttributesCollectible -> save()) {

								}
							} else if ($attribue['action'] === 'E') {
								$this -> AttributesCollectiblesEdit -> AttributesCollectible -> id = $attribue['id'];
								if ($this -> AttributesCollectiblesEdit -> AttributesCollectible -> saveField('description', $attribue['description'], false)) {

								}
							}
						}
						$attributes = $this -> AttributesCollectiblesEdit -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.collectible_id' => $id, 'AttributesCollectible.active' => 1), 'contain' => 'Attribute'));
						$this -> data = $attributes;
						$this -> Session -> setFlash(__('You have succesfully updated the attributes.', true), null, null, 'success');
					} else {
						$updatedAttributes = array();
						foreach ($this -> data['AttributesCollectible'] as $key => $attribute) {
							if (isset($attribute['action']) && $attribute['action'] !== '') {
								array_push($updatedAttributes, $attribute);

								$attribute['edit_user_id'] = $this -> getUserId();
								$attribute['collectible_id'] = $id;
								if ($attribute['action'] === 'D') {
									$attribute['attributes_collectible_id'] = $attribute['id'];

								} else if ($attribute['action'] === 'A') {

								} else if ($attribute['action'] === 'E') {
									$attribute['attributes_collectible_id'] = $attribute['id'];
								}
								unset($attribute['id']);
								$this -> AttributesCollectiblesEdit -> create();
								$this -> AttributesCollectiblesEdit -> set($attribute);
								if ($this -> AttributesCollectiblesEdit -> save()) {
									$attrCollectibleEditId = $this -> AttributesCollectiblesEdit -> id;

									$edit = array();
									$edit['Edit'] = array();
									$edit['Edit']['user_id'] = $this -> getUserId();
									$edit['Edit']['attributes_collectibles_edit_id'] = $attrCollectibleEditId;
									$edit['Edit']['collectible_id'] = $id;

									if (isset($attribute['attributes_collectible_id'])) {
										$edit['Edit']['attributes_collectible_id'] = $attribute['attributes_collectible_id'];
									}

									$this -> loadModel('Edit');
									$this -> Edit -> create();
									if (!$this -> Edit -> save($edit)) {
										$this -> log('Failed to save the collectible edit into the edits table ' . $id . ' ' . date("Y-m-d H:i:s", time()), 'error');
									}
								}
							}
						}
						debug($updatedAttributes);
						$this -> set('collectibleId', $id);
						$this -> set(compact('updatedAttributes'));
						$this -> render('confirm');
						return;
					}
				} else {
					$errorAttributes = array();
					foreach ($this->data['AttributesCollectible'] as $key => $attribue) {
						$attributesCollectible = array();
						$attributesCollectible['AttributesCollectible'] = $attribue;
						$attributesCollectible['Attribute'] = array();
						$attributesCollectible['Attribute']['name'] = $attribue['name'];
						array_push($errorAttributes, $attributesCollectible);
					}
					debug($errorAttributes);
					$this -> data = $errorAttributes;
				}
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

			$this -> set('collectibleId', $id);
			$this -> Session -> write('collectible.edit-id', $id);
			//Submit the deletes as deletes....then loop throuh each one to either delete or add
			$attributes = $this -> AttributesCollectiblesEdit -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.collectible_id' => $id, 'AttributesCollectible.active' => 1), 'fields' => array('id', 'attribute_id', 'collectible_id', 'description', 'active'), 'contain' => array('Attribute' => array('fields' => array('name')))));
			debug($attributes);
			//$this -> set('attributes', $attributes);
			$this -> data = $attributes;
		}

		$collectibleId = $this -> Session -> read('collectible.edit-id');
		$this -> set(compact('collectibleId'));
	}

	function admin_approval($editId = null, $attributeEditId = null) {
		$this -> checkLogIn();
		$this -> checkAdmin();
		if ($editId && is_numeric($editId) && $attributeEditId && is_numeric($attributeEditId)) {
			$this -> set('attributeEditId', $attributeEditId);
			$this -> set('editId', $editId);
			if (empty($this -> data)) {
				$attributeEditVersion = $this -> AttributesCollectiblesEdit -> find("first", array('contain' => array('Attribute' => array('fields' => array('name'))), 'conditions' => array('AttributesCollectiblesEdit.id' => $attributeEditId)));
				debug($attributeEditVersion);
				if (!empty($attributeEditVersion)) {
					$attribute = array();
					$attribute['AttributesCollectible'] = $attributeEditVersion['AttributesCollectiblesEdit'];
					$attribute['Attribute']['name'] = $attributeEditVersion['Attribute']['name'];
					debug($attribute);
					$this -> set('attribute', $attribute);
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