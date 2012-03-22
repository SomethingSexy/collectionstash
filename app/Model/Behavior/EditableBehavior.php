<?php
class EditableBehavior extends ModelBehavior {

    /**
     * Shadow table prefix
     * Only change this value if it causes table name crashes
     *
     * @access private
     * @var string
     */
    private $edit_suffix = '_edits';
    /**
     * Defaul setting values
     *
     * @access private
     * @var array
     */
    private $defaults = array('useDbConfig' => null, 'model' => null, 'modelAssociations' => null);

    /**
     * Configure the behavior through the Model::actsAs property
     *
     * @param object $Model
     * @param array $config
     */
    public function setup(&$Model, $settings = null) {
        if (is_array($settings)) {
            $this -> settings[$Model -> alias] = array_merge($this -> defaults, $settings);
        } else {
            $this -> settings[$Model -> alias] = $this -> defaults;
        }
        $this -> createShadowModel($Model);
        $Model -> Behaviors -> attach('Containable');
        if (isset($this -> settings[$Model -> alias]['behaviors'])) {
            foreach ($this -> settings[$Model -> alias]['behaviors'] as $key => $value) {
                $Model -> EditModel -> Behaviors -> load($key, $value);
            }
        }
    }

    /**
     * Returns a generic model that maps to the current $Model's shadow table.
     *
     * @param object $Model
     * @return boolean
     */
    private function createShadowModel(&$Model) {

        if (is_null($this -> settings[$Model -> alias]['useDbConfig'])) {
            $dbConfig = $Model -> useDbConfig;
        } else {
            $dbConfig = $this -> settings[$Model -> alias]['useDbConfig'];
        }
        $db = &ConnectionManager::getDataSource($dbConfig);
        if ($Model -> useTable) {
            $shadow_table = $Model -> useTable;
        } else {
            $shadow_table = Inflector::tableize($Model -> name);
        }
        $shadow_table = $shadow_table . $this -> edit_suffix;
        $prefix = $Model -> tablePrefix ? $Model -> tablePrefix : $db -> config['prefix'];
        $full_table_name = $prefix . $shadow_table;

        $existing_tables = $db -> listSources();

        if (!in_array($full_table_name, $existing_tables)) {
            $Model -> EditModel = false;
            return false;
        }

        $useShadowModel = $this -> settings[$Model -> alias]['model'];

        if (is_string($useShadowModel) && App::import('model', $useShadowModel)) {
            $Model -> EditModel = new $useShadowModel(false, $shadow_table, $dbConfig);
        } else {
            $Model -> EditModel = new Model(false, $shadow_table, $dbConfig);
        }
        if ($Model -> tablePrefix) {
            $Model -> EditModel -> tablePrefix = $Model -> tablePrefix;
        }

        /*
         * Updated 1/7/12 after cakephp 2.0 upgrade, this was done
         * because this shadow model gets added as an associated
         * model and the alias was the same name, so when validations
         * where being done and passing the validations to
         * the view, it was overwriting the validations (this is done
         * in controller when calling the render)
         *
         */
        $Model -> EditModel -> alias = $Model -> alias . 'Edit';
        $Model -> EditModel -> primaryKey = 'id';

        // debug($Model -> getAssociated('belongsTo'));
        // debug($Model -> EditModel);
        return true;
    }

    /**
     * If successful, it will return the edit data in the main model form
     */
    public function saveEdit(Model $Model, $editData, $editId, $userId, $collectibleId) {
        $multipleSave = false;

        /*
         *
         */
        if (isset($editData[$Model -> alias])) {
            $editData[$Model -> alias]['edit_user_id'] = $userId;

            $saveEdit = array();
            debug($editData);
            $editData = $Model -> beforeSaveEdit($editData);
            debug($editData);
            $saveEdit[$Model -> EditModel -> alias] = $editData[$Model -> alias];
            $saveEdit[$Model -> EditModel -> alias]['base_id'] = $editId;
            //If it is a one model save then we can just do the edit here
            $saveEdit['Edit']['user_id'] = $userId;
            //$saveEdit['type_edit_id'] = $id;
            //this will be from the settings
            // $saveEdit['Edit']['type'] = $this -> settings[$Model -> alias]['type'];
            $saveEdit['Edit']['collectible_id'] = $collectibleId;
            // $saveEdit['Edit']['type_id'] = $editId;

        } else {
            $multipleSave = true;
            $saveEdit = array();
            foreach ($editData as $key => $value) {
                $editItem = $Model -> beforeSaveEdit($value);
                $editItem['edit_user_id'] = $userId;
                // $editItem['action'] = $action;
                array_push($saveEdit, $editItem);
            }
        }
        debug($saveEdit);
        $succesful = true;
        $retVal = array();
        $Model -> EditModel -> bindModel(array('belongsTo' => array('Edit')));
        if (!$multipleSave) {
            $Model -> EditModel -> create();
            if ($Model -> EditModel -> saveAll($saveEdit, array('validate' => false))) {
                $id = $Model -> EditModel -> id;
                //Grab the one we just submitted
                $savedEdit = $Model -> findEdit($id);
                // //Now find the base because the base as all of the core associations
                // //TODO: We could eventually figure out a way to copy associations to the edit model.
                // if ($mergeBase) {
                // $base = $Model -> findById($savedEdit[$Model -> EditModel -> alias]['base_id']);
                // $returnEdit = $base;
                // }
                $returnEdit = $savedEdit;
                $returnEdit[$Model -> alias] = $savedEdit[$Model -> EditModel -> alias];
                unset($returnEdit[$Model -> EditModel -> alias]);
                unset($returnEdit[$Model -> alias . 'Edit']);
                $retVal = $returnEdit;
            } else {
                $succesful = false;
            }
        } else {
            $edit = array();
            $edit['Edit']['user_id'] = $userId;
            $edit['Edit']['collectible_id'] = $collectibleId;
            $Model -> EditModel -> Edit -> create();
            if ($Model -> EditModel -> Edit -> save($edit)) {
                $editId = $Model -> EditModel -> Edit -> id;
                foreach ($saveEdit as $key => &$value) {
                    $value['edit_id'] = $editId;
                }
                if ($Model -> EditModel -> saveAll($saveEdit, array('validate' => false))) {

                } else {
                    $Model -> EditModel -> Edit -> delete($editId);
                    $succesful = false;
                }

            } else {
                $succesful = false;
            }
        }

        $Model -> EditModel -> unbindModel(array('belongsTo' => array('Edit')));

        if ($succesful) {
            return $retVal;
        } else {
            return false;
        }
    }

    public function findEdit(Model $Model, $id) {
        $associations = $this -> settings[$Model -> alias]['modelAssociations'];
        if (isset($associations) && is_array($associations)) {
            foreach ($associations as $type => $listOfAss) {
                foreach ($listOfAss as $key => $ass) {
                    $Model -> EditModel -> bindModel(array($type => array($ass)));
                }
            }
        }

        return $Model -> EditModel -> find("first", array('conditions' => array($Model -> EditModel -> alias . '.id' => $id)));
    }

    public function findEditsByEditId(Model $Model, $edit_id) {
        return $Model -> EditModel -> find("all", array('contain' => false, 'conditions' => array($Model -> EditModel -> alias . '.edit_id' => $edit_id)));
    }

    /**
     * This function will compare to versions of the collectible, the edit version
     * and the current version of the collectible.
     *
     * Future Enhancements
     *  - Make this more automated...calls to the DB
     *  - Store the list of fields that we want to compare against somewhere
     *  - Behavior
     *  - At some point, this is going to have to be based on collectible type...gonna have to roll that beast out
     */
    public function compareEdit(Model $Model, $edit, $base) {
        $returnCompare = $edit;
        $returnCompare[$Model -> alias] = $base[$Model -> alias];
        //TODO update this so that the changes are in their own array and not mixed in.
        foreach ($this -> settings[$Model -> alias]['compare'] as $field) {
            $returnCompare[$Model -> alias][$field] = $edit[$Model -> EditModel -> alias][$field];
            $editFieldValue = $edit[$Model -> EditModel -> alias][$field];
            $currentFieldValue = $base[$Model -> alias][$field];
            if ($editFieldValue !== $currentFieldValue) {
                $returnCompare[$Model -> alias][$field . '_changed'] = true;
            }
        }

        return $returnCompare;
    }

    /**
     * This could get rolled up into a more generic method with options at some point
     */
    public function getEditForApproval(Model $Model, $editId) {
        $editVersion = $this -> findEdit($Model, $editId);
        debug($editVersion);
        //So what happens when the associated models have changed because the edit is different?
        $current = $Model -> find("first", array('contain' => false, 'conditions' => array($Model -> alias . '.id' => $editVersion[$Model -> EditModel -> alias]['base_id'])));
        debug($current);

        if (!empty($editVersion)) {
            $processedCurent = $this -> compareEdit($Model, $editVersion, $current);
            debug($processedCurent);
            return $processedCurent;
        } else {
            return false;
        }
    }

    public function getUpdateFields($collectibleEditId, $includeChanges = false, $notes = null) {
    }

    public function beforeSaveEdit(Model $Model, $editData) {
        return $editData;
    }

}
?>