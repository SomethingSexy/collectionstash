<?php
/**
 * this Fact table is probably going to hold more than it needs to but I am going to contain
 * it to just price information for that stash
 * 
 */
class StashFact extends AppModel {
    var $name = 'StashFact';
    var $actsAs = array('Containable');
	var $belongsTo = array('Stash');
}
?>
