<?php
class Poll extends AppModel {
    var $name = 'Poll';
    var $actsAs = array('Containable');
	var $hasMany = array('PollOption', 'Vote');
}
?>
