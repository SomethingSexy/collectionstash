<?php

class AttributeItem extends Object 
{
  public $name;
  
  public $id;
  
  public $children = array();
  
  
  public function hasChildren()
  {
    return !empty($children);
  }
  
}

?>
