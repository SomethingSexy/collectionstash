<?php
if ($this -> Session -> check('Message.error')) {
	echo '<div class=\'component-message error\'><span>';
	echo $this -> Session -> flash('error');
	echo '</span></div>';
}

if ($this -> Session -> check('Message.warn')) {
	echo '<div class=\'component-message warn\'><span>';
	echo $this -> Session -> flash('warn');
	echo '</span></div>';
}

if ($this -> Session -> check('Message.success')) {
	echo '<div class=\'component-message success\'><span>';
	echo $this -> Session -> flash('success');
	echo '</span></div>';
}
?>
