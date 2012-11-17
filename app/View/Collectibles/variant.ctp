<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Submit New Collectible - Variant');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>
      	<p><?php echo __('Some collectibles are variants of other.  In these collectibles we want to link this new one to an existing collectible so we can accurately keep track of which have variants or not.') ?></p>
      	<p><?php echo __('A collectible is a variant if: ');?></p>
      	<ul>
      		<li><?php echo __('The collectible is the same as another but with an additional feature of accessory'); ?></li>
      		<li><?php echo __('The collectible is an exclusive version of another collectible.'); ?></li>
      	</ul>
      </div> 
    </div>
    <div class="component-view">
    	<div class="question">
      		<p><?php echo __('Is the collectible you are adding a variant?') ?></p>
      	</div>
		<div class="links">
	      	<?php echo $this->Form->create('Collectible');?>
				<input type="hidden" name="data[Collectible][variant]" value="true" ?>
				<input type="submit" value="Yes" class="btn btn-primary">
	     	<?php echo $this->Form->end();?> 	 
	     	<?php echo $this->Form->create('Collectible');?>
				<input type="hidden" name="data[Collectible][variant]" value="false" ?>
				<input type="submit" value="No" class="btn" />
	     	<?php echo $this->Form->end();?>
		</div>
    </div>    
  </div>
</div>