<?php
class Vote extends AppModel {
    var $name = 'Vote';
    var $actsAs = array('Containable');
	var $belongsTo = array('User', 'Poll', 'PollOption' => array('counterCache' => true));
}
?>
