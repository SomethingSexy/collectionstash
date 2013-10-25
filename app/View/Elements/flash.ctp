<?php
if ($this -> Session -> check('Message.error')) {
	echo '<div class=\'alert alert-danger\'><p>';
	echo $this -> Session -> flash('error');
	echo '</p></div>';
}

if ($this -> Session -> check('Message.warn')) {
	echo '<div class=\'alert alert-warning\'><p>';
	echo $this -> Session -> flash('warn');
	echo '</p></div>';
}

if ($this -> Session -> check('Message.success')) {
	echo '<div class=\'alert alert-success\'><p>';
	echo $this -> Session -> flash('success');
	echo '</p></div>';
}
?>
