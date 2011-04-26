<?php
  class Poster extends AppModel
  {
    var $name = 'Poster'; 

    var $hasMany = array('PostersUser');    

    var $actsAs = array('ExtendAssociations','Containable');

  }
?>
