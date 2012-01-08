
<?php echo $username ?>, we have denied the following collectible you submitted to Collection Stash : <?php echo $collectibleName ?>
<br><br>
<?php if(isset($notes) && !empty($notes)){
	echo 'The reason the submission was denied:<br>';
	echo $notes;
} ?>

