<?php if (!empty($errors)) { ?>
<div class="errors">
    <h3>There are <?php echo count($errors); ?> error(s) in your submission:</h3>
    
    <ul>
        <?php foreach ($errors as $field => $error) { ?>
        <li><?php echo $error; ?></li>
        <?php } ?>
    </ul>
</div>
<?php } ?>