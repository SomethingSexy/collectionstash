<?php
/**
 * TODO: This edit code should be moved to AttributesCollectiblesController.php
 */
class AttributesCollectiblesEditsController extends AppController {
    public $helpers = array('Html', 'Js', 'Minify');

    /*
     * In the future when doing this edits, we are going to have to make sure that these parts are not being
     * used in a custom when we delete them
     */
    function edit($id = null, $adminMode = false) {
        $this -> checkLogIn();

        if (!$id && !is_numeric($id) && empty($this -> request -> data)) {
            $this -> Session -> setFlash(__('Invalid collectible', true));
            //TODO go somewhere
            $this -> redirect($this -> referer());
        }
        $this -> loadModel('AttributesCollectible');
        if (!empty($this -> request -> data)) {
            debug($this -> request -> data);
            if (isset($this -> request -> data['AttributesCollectible'])) {
                $isValid = true;
                //TODO this does not seem right
                foreach ($this->request->data['AttributesCollectible'] as $key => $attribue) {
                    //If it is being deleted, I do not care to validate it.
                    if ($attribue['action'] !== 'D') {
                        $this -> AttributesCollectible -> set($attribue);
                        //debug($this -> AttributesCollectible);
                        if ($this -> AttributesCollectible -> validates()) {

                        } else {
                            //If one is invalid set it to false
                            $isValid = false;
                            debug($this -> AttributesCollectible -> invalidFields());
                            $this -> set('errors', $this -> AttributesCollectible -> validationErrors);
                        }
                    }
                }

                //if everything is valid, then lets do our updates
                if ($isValid) {
                    $adminMode = $this -> Session -> read('collectible.edit.admin-mode');
                    if (Configure::read('Settings.Collectible.Edit.auto-approve') === true || (isset($adminMode) && $adminMode && $this -> isUserAdmin())) {
                        //TODO move this to the model, validate we are removing the correct attributes?
                        foreach ($this->request->data['AttributesCollectible'] as $key => $attribue) {
                            //debug($this -> AttributesCollectible);
                            if ($attribue['action'] === 'D') {
                                $this -> AttributesCollectible -> id = $attribue['id'];
                                //If we are deleting then set the active state to zero, I believe this is for history purposes.
                                //however, it looks like it is changing the originally added one...need to verify this.
                                if ($this -> AttributesCollectible -> save(array('active' => 0), false, array('active'))) {

                                }
                            } else if ($attribue['action'] === 'A') {
                                $attribue['collectible_id'] = $id;
                                $this -> AttributesCollectible -> create();
                                $this -> AttributesCollectible -> set($attribue);
                                if ($this -> AttributesCollectible -> save()) {

                                }
                            } else if ($attribue['action'] === 'E') {
                                $this -> AttributesCollectible -> id = $attribue['id'];
                                if ($this -> AttributesCollectible -> saveField('description', $attribue['description'], false)) {

                                }
                            }
                        }
                        $attributes = $this -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.collectible_id' => $id, 'AttributesCollectible.active' => 1), 'contain' => 'Attribute'));
                        $this -> request -> data = $attributes;
                        $this -> Session -> setFlash(__('You have succesfully updated the attributes.', true), null, null, 'success');
                    } else {
                        $updatedAttributes = array();
                        /*
                         * Still doing this one by one because it is not as necessary to update this with as many at
                         * once.
                         */
                        foreach ($this->request->data['AttributesCollectible'] as $key => $attribute) {
                            if (isset($attribute['action']) && $attribute['action'] !== '') {
                                debug($attribute);
                                array_push($updatedAttributes, $attribute);
                                $saveAttribute = array();
                                $saveAttribute['AttributesCollectible'] = $attribute;
                                $saveAttribute['AttributesCollectible']['edit_user_id'] = $this -> getUserId();
                                $saveAttribute['AttributesCollectible']['collectible_id'] = $id;
                                $baseId = null;
                                if ($attribute['action'] === 'D') {
                                    $baseId = $attribute['id'];

                                } else if ($attribute['action'] === 'A') {

                                } else if ($attribute['action'] === 'E') {
                                    $baseId = $attribute['id'];
                                }
                                unset($saveAttribute['AttributesCollectible']['id']);

                                debug($saveAttribute);
                                debug($baseId);
                                $returnData = $this -> AttributesCollectible -> saveEdit($saveAttribute, $baseId, $this -> getUserId(), $id);

                                if ($returnData) {

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
                    $this -> request -> data = $errorAttributes;
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
            $attributes = $this -> AttributesCollectible -> find('all', array('conditions' => array('AttributesCollectible.collectible_id' => $id, 'AttributesCollectible.active' => 1), 'fields' => array('id', 'attribute_id', 'collectible_id', 'description', 'active'), 'contain' => array('Attribute' => array('fields' => array('name')))));
            debug($attributes);
            //$this -> set('attributes', $attributes);
            $this -> request -> data = $attributes;
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
            if (empty($this -> request -> data)) {
                $this -> loadModel('AttributesCollectible');
                $attributeEditVersion = $this -> AttributesCollectible -> findEdit($attributeEditId);
                // $attributeEditVersion = $this -> AttributesCollectiblesEdit -> find("first", array('contain' => array('Attribute' => array('fields' => array('name'))), 'conditions' => array('AttributesCollectiblesEdit.id' => $attributeEditId)));
                debug($attributeEditVersion);
                if (!empty($attributeEditVersion)) {
                    $attribute = array();
                    $attribute['AttributesCollectible'] = $attributeEditVersion['AttributesCollectibleEdit'];
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