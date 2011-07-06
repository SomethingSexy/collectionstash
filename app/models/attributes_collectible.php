<?php
class AttributesCollectible extends AppModel {
	var $name = 'AttributesCollectible';
	//var $useTable = 'accessories_collectibles';
	var $belongsTo = array('Attribute', 'Collectible');
	var $actsAs = array('Containable');
	
	var $validate = array (
      'attribute_id' => array(
           'rule' => array('validateAttributeId'),
           'required' => true,
           'message' => 'Must be a valid attribute.'
       ),
      'description' => array(
           'minLength' => array(
              'rule' => 'notEmpty',
              'message' => 'Description is required.'
            ),
            'maxLength' => array(
              'rule' => array('maxLength', 255),
              'message' => 'Invalid length.'
            )
       )            
    );
	   
	function validateAttributeId($check) {
		$result = $this -> Attribute -> find('count', array('id'=> $check['attribute_id']));
		return $result > 0;
	}
}
?>
