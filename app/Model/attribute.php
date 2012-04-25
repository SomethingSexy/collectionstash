<?php
App::import('Vendor', 'attribute_item');
class Attribute extends AppModel {
    var $name = 'Attribute';
    var $actsAs = array('Tree','Containable');
	public $hasMany = array('AttributesCollectible');
    //public function get

    public function getAttributeList($id=null) {
        /*
         * What I will do. Use Ajax, display the first list of attributes.
         *
         * Then when they select an attribute, go grab the next list of attributes, if there are none
         * then we jsut present the description field and other info.  If there ia list we display
         * the new drop down until we are done.
         *
         * We will need to convert the current level list to a key/value pair list.
         */
        $allChildren = $this -> generateTreeList( array('Attribute.parent_id' => $id), null, null, '...');
        asort($allChildren);
		
        return $allChildren;

        // $data = $this->find('threaded');
        // $test = new AttributeItem();
        // debug($test);
        // //debug($data);
        // $result = array();
        // //array_push($result, array('test'=>array()));
        // foreach ($data as $attribute)
        // {
        //   //debug($attribute['Attribute']['name']);
        //   $this->_processAttributes($attribute, $result);
        //
        // }
        //
        // debug($result);
    }

    // function _processAttributes($attribute, &$result)
    // {
    //     debug($result);
    //    $attributeItem = new AttributeItem();
    //
    //    $attributeItem['name'] = $attribute['Attribute']['name'];
    //    $attributeItem['id'] = $attribute['Attribute']['id'];
    //
    //    if($attribute['Attribute']['parent_id'] == '')
    //    {
    //      debug($attribute['Attribute']);
    //      $key = $attribute['Attribute']['id'];
    //      $result[$key] = $attributeItem;
    //      debug($result);
    //    }
    //    else
    //    {
    //       $key = $attribute['Attribute']['parent_id'];
    //       $result[$key] = $attribute['Attribute']['name'];
    //    }
    //
    //    //print the name
    //    debug($attribute['Attribute']['name']);
    //    debug($attribute['Attribute']['id']);
    //    if(!empty($attribute['children']))
    //    {
    //       foreach ($attribute['children'] as $child)
    //       {
    //         $this->_processAttributes($child, $result);
    //       }
    //    }
    // }
}
?>
