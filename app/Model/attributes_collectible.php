<?php
class AttributesCollectible extends AppModel {
	var $name = 'AttributesCollectible';
	//var $useTable = 'accessories_collectibles';
	var $belongsTo = array('Attribute', 'Collectible');
	var $actsAs = array('Revision','Containable');
	// => array('ignore'=>array('active', 'modified', 'created'))
	var $validate = array (
      'description' => array(
              'rule' => 'notEmpty',
              'message' => 'Description is required.'
       )            
    );
	   
	function validateAttributeId($check) {
		debug($check['attribute_id']);
		$result = $this -> Attribute -> find('count', array('id'=> $check['attribute_id']));
		return $result > 0;
	}
}
?>
