<?php if (!empty($errors)) { ?>
<div class='alert alert-danger'>
	<span>There are <?php echo count($errors); ?> error(s) in your submission:</span>
    <ul>
        <?php foreach ($errors as $field => $error) { ?>
        <li><?php echo $error; ?></li>
        <?php } ?>
    </ul>
</div>
<?php } ?>