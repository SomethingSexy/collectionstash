<div id="my-stashes-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Stash', true) ?></h2> 
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-view">
			<?php echo '<p class="">'. __('No stash for that user exists!', true) . '</p>';?>
    </div>    
  </div>
</div>
