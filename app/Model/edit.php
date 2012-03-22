<?php
/**
 * The AttributesCollectible and Upload need to on here so that the save automatically works.  I am not sure
 * how I like them on there but it works for now.
 */
class Edit extends AppModel {
    public $name = 'Edit';
    public $actsAs = array('Containable');
    public $belongsTo = array('User' => array('counterCache' => true), 'Collectible');

    /**
     * After every find, lets clean up some of the data that we do not need for
     * each specific edit.
     */
    public function getEditDetail($edit_id) {
        $results = $this -> findById($edit_id);
        $edits = array();

        $collectibleEdit = $this -> Collectible -> findEditsByEditId($results['Edit']['id']);
        debug($collectibleEdit);
        if (!empty($collectibleEdit)) {
            //Since there is only 1 allowed, just assume for now
            $collectibleEdit[0]['CollectibleEdit']['edit_type'] = 'Collectible';
            array_push($edits, $collectibleEdit[0]['CollectibleEdit']);

        }

        /*
         * Unfortunately with this method of doing it, we have to bind and unbind the models
         * for the finds.  This is because we have the id on the shadow edit model and not the
         * actual model.
         */
        //TODO: Could turn this into a series of joings and then find by that
        //Or I could merge these together so they are just one array
        $this -> bindModel(array('belongsTo' => array('AttributesCollectible')));
        $attributes = $this -> AttributesCollectible -> findEditsByEditId($results['Edit']['id']);
        $this -> unbindModel(array('belongsTo' => array('AttributesCollectible')));
        if (!empty($attributes)) {

            //Only allowing one per for attributes right now, if we tie them together
            //the we will need to update this.
            $attributes[0]['AttributesCollectibleEdit']['edit_type'] = 'Attribute';
            //Since there is only 1 allowed, just assume for now
            array_push($edits, $attributes[0]['AttributesCollectibleEdit']);
        }

        $this -> bindModel(array('belongsTo' => array('Upload')));
        $upload = $this -> Upload -> findEditsByEditId($results['Edit']['id']);
        $this -> unbindModel(array('belongsTo' => array('Upload')));
        debug($upload);
        if (!empty($upload)) {
            $upload[0]['UploadEdit']['edit_type'] = 'Upload';
            //Since there is only 1 allowed, just assume for now
            array_push($edits, $upload[0]['UploadEdit']);
        }

        $this -> bindModel(array('belongsTo' => array('CollectiblesTag')));
        $tags = $this -> CollectiblesTag -> findEditsByEditId($results['Edit']['id']);
        $this -> unbindModel(array('belongsTo' => array('CollectiblesTag')));
        if (!empty($tags)) {
            $tags[0]['CollectiblesTagEdit']['edit_type'] = 'Tag';
            //Since there is only 1 allowed, just assume for now
            array_push($edits, $tags[0]['CollectiblesTagEdit']);
        }
        $results['Edits'] = $edits;
        debug($results);

        return $results;
    }

}
?>
