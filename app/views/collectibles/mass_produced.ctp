<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('Contribute - Mass Produced');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>
      	<p><?php __('Not all Mass-Produced collectibles are made the same.  For some, mass-produced, collectibles the manufacture of that collectible is the brand name.') ?></p>
      	<p><?php __('An example of a collectible that might not be branded by Manufacture would be a poster or a print.  These would be branded by the artist or the event.') ?></p>
      	<p><?php __('Here are some guidelines to determine your next steps:') ?></p>
      	<ul>
      		<li><?php __('Is the manufacture the first thing you think of with this collectible?') ?></li>
      		<li><?php __('Is the manufacture the main brand for this collectible?') ?></li>
      	</ul>
      	<p><?php __('If you answer Yes to any of these questions, then add this collectible by manufacture.') ?></p>
	 </div> 
    </div>
    <div class="component-view">
      	<p><?php __('Add by Manufacture?') ?></p>
		<div class="links">
	      	<?php echo $this->Form->create('Collectible');?>
				<input type="hidden" name="data[Collectible][manufactured]" value="true" ?>
	     	<?php echo $this->Form->end('Yes');?> 	 
	     	<?php echo $this->Form->create('Collectible');?>
				<input type="hidden" name="data[Collectible][manufactured]" value="false" ?>
	     	<?php echo $this->Form->end('No');?>
		</div>

    </div>    
  </div>
</div>