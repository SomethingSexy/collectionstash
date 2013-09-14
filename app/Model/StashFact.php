<?php
/**
 * this Fact table is probably going to hold more than it needs to but I am going to contain
 * it to just price information for that stash.  This will contain current time data, not
 * data over time but it could be turned into something that contains stats over time
 * 
 * If we want that to happen we can add a date, time field and then the last one will basically
 * be the first one at that time
 * 
 */
class StashFact extends AppModel {
    var $name = 'StashFact';
    var $actsAs = array('Containable');
	var $belongsTo = array('Stash');
}
?>
