<?php
class PostersUser extends AppModel {

	var $name = 'PostersUser';
	var $belongsTo = array('Stash', 'Poster', 'User');
	var $actsAs = array('Containable');
}

?>