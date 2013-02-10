<div id="my-stashes-component" class="well">
    <h2><?php echo $stashUsername . '\'s' .__(' collectible', true) ?></h2> 
    <?php echo $this->element('flash'); ?>
	<?php echo '<p class="">'. $stashUsername . __('\'s stash is in private mode.', true) . '</p>';?> 
</div>
