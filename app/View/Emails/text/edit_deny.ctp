
<?php echo $username ?>, we have denied the following collectible you submitted a change to: <?php echo $collectibleName ?>

<?php if(isset($notes) && !empty($notes)){
	echo 'The reason the change was denied:';
	echo $notes;
} ?>

You submitted changes to the following collectible: <?php echo $collectible_url ?>
