<div id="my-stashes-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Stash', true) ?></h2> 
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-view">
			<?php echo '<p class="">'. __('Nice try but you can\'t edit someone else\'s stash!', true) . '</p>';?>
    </div>    
  </div>
</div>