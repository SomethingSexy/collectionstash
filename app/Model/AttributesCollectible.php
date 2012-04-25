<?php
class AttributesCollectible extends AppModel {
    public $name = 'AttributesCollectible';
    //var $useTable = 'accessories_collectibles';
    public $belongsTo = array('Attribute', 'Collectible');
    public $actsAs = array('Revision', 'Containable', 'Editable' => array('modelAssociations' => array('belongsTo' => array('Attribute')), 'type' => 'attribute', 'model' => 'AttributesCollectiblesEdit'));
    // => array('ignore'=>array('active', 'modified', 'created'))
    public $validate = array('description' => array('rule' => 'notEmpty', 'message' => 'Description is required.'));

    function validateAttributeId($check) {
        debug($check['attribute_id']);
        $result = $this -> Attribute -> find('count', array('id' => $check['attribute_id']));
        return $result > 0;
    }

    function getUpdateFields($attributeEditId, $notes = null) {
        //Grab out edit collectible
        $attributeEditVersion = $this -> findEdit($attributeEditId);
        //reformat it for us, unsetting some stuff we do not need
        debug($attributeEditVersion);
        $attributeFields = array();
        if ($attributeEditVersion['AttributesCollectibleEdit']['action'] === 'A') {
            $attributeFields['AttributesCollectible']['description'] = $attributeEditVersion['AttributesCollectibleEdit']['description'];
            $attributeFields['AttributesCollectible']['active'] = 1;
            $attributeFields['AttributesCollectible']['attribute_id'] = $attributeEditVersion['AttributesCollectibleEdit']['attribute_id'];
            $attributeFields['AttributesCollectible']['collectible_id'] = $attributeEditVersion['AttributesCollectibleEdit']['collectible_id'];
            $attributeFields['Revision']['action'] = 'A';
        } else if ($attributeEditVersion['AttributesCollectibleEdit']['action'] === 'D') {
            //For deletes, lets set the status to 0, that means it is not active
            $attributeFields['AttributesCollectible']['active'] = 0;
            $attributeFields['AttributesCollectible']['id'] = $attributeEditVersion['AttributesCollectibleEdit']['base_id'];
            $attributeFields['Revision']['action'] = 'D';
        } else if ($attributeEditVersion['AttributesCollectibleEdit']['action'] === 'E') {
            //The only thing we can edit right now is the description
            $attributeFields['AttributesCollectible']['description'] = $attributeEditVersion['AttributesCollectibleEdit']['description'];
            $attributeFields['AttributesCollectible']['id'] = $attributeEditVersion['AttributesCollectibleEdit']['base_id'];
            $attributeFields['Revision']['action'] = 'E';
        }

        if (!is_null($notes)) {
            $attributeFields['Revision']['notes'] = $notes;
        }
        //Make sure I grab the user id that did this edit
        $attributeFields['Revision']['user_id'] = $attributeEditVersion['AttributesCollectibleEdit']['edit_user_id'];
        return $attributeFields;
    }

}
?>
