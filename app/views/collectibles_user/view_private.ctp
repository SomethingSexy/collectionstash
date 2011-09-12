<div id="my-stashes-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo $stashUsername . '\'s' .__(' collectible', true) ?></h2> 
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-view">
			<?php echo '<p class="">'. $stashUsername . __('\'s stash is in private mode.', true) . '</p>';?>
    </div>    
  </div>
</div>
