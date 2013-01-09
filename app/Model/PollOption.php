<?php
class PollOption extends AppModel {
    var $name = 'PollOption';
    var $actsAs = array('Containable');
	var $belongsTo = array('Poll');
}
?>
