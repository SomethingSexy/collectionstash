<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Submit New Collectible - Mass Produced');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>
      	<p><?php echo __('Not all mass-produced collectibles are made the same. For some, the manufacturer of that collectible is the brand name.  An example of a collectible that might not be branded by manufacturer would be a poster or a print. These would be branded by the artist or the event.') ?></p>
      	<p><?php echo __('Here are some guidelines to determine your next steps:') ?></p>
      	<ul>
      		<li><?php echo __('Is the manufacturer the first thing you think of in regards to this collectible?') ?></li>
      		<li><?php echo __('Is the manufacturer the main brand for this collectible?') ?></li>
      	</ul>
      	<p><?php echo __('If you answer yes to either of these questions, then add this collectible by manufacturer.') ?></p>
	 </div> 
    </div>
    <div class="component-view">
    	<div class="question">
      		<p><?php echo __('Add by Manufacturer?') ?></p>     	
      	</div>
		<div class="links">
	      	<?php echo $this->Form->create('Collectible');?>
				<input type="hidden" name="data[Collectible][manufactured]" value="true" ?>
				<input type="submit" value="Yes" class="btn btn-primary"/>
	     	<?php echo $this->Form->end();?> 	 
	     	<?php echo $this->Form->create('Collectible');?>
				<input type="hidden" name="data[Collectible][manufactured]" value="false" ?>
				<input type="submit" value="No" class="btn" />
	     	<?php echo $this->Form->end();?>
		</div>

    </div>    
  </div>
</div>