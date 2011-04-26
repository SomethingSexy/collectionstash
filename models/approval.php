<?php
  //TODO this is really a submission class, maybe change name eventually?
  class Approval extends AppModel {
    var $name = 'Approval';
    var $belongsTo = array ('User'=> array('counterCache' => true));
    //This should probably be a hasOne but makes my output cleaner for now.
    var $hasMany = array('Collectible');
	var $actsAs = array('Containable');
  }
?>
