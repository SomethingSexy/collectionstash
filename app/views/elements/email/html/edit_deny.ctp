
<?php echo $username ?>, we have denied the following collectible you submitted a change to: <?php echo $collectibleName ?>
<br><br>
<?php if(isset($notes) && !empty($notes)){
	echo 'The reason the change was denied:<br>';
	echo $notes.'<br><br>';
} ?>

You submitted changes to the following collectible: <?php echo $collectible_url ?>
