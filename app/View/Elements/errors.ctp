<?php if (!empty($errors)) { ?>
<div class='component-message error'>
	<span>There are <?php echo count($errors); ?> error(s) in your submission:</span>
    <ul>
        <?php foreach ($errors as $field => $error) { ?>
        <li><?php echo $error; ?></li>
        <?php } ?>
    </ul>
</div>
<?php } ?>