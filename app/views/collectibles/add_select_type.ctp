<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('Contribute');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>
      	<p><?php __('Collection Stash is a community support database that gives its user\'s the ability to contribute to the growing collectible database.  By helping maintain this database you can add those collectibles to your personal stash.') ?></p>
      	<p><?php __('We want to keep our data as complete an accurate as possible.  To accomplish this goal, we will give you the tools to be able to add as much information as you can.  Each collectible you submit will require approval by one of our administrators before you can add it to your stash.  This will help make sure that each collectible being added is unqiue and accurate.') ?></p>
      	<p><?php __('To get started, we will need to know a little bit about the collectible you are trying to add.')?></p>
      	
      </div> 
    </div>
    <div class="component-view">
      	<p><?php __('Is the collectible you are adding Mass-Produced?') ?></p>
		<dl>
			<dt><?php __('Mass-Produced: '); ?></dt>
			<dd><?php __('A mass-produced collectible is a collectible that is produced in large amounts for sale (i.e., more than one).'); ?></dd>
		</dl>
		<div class="links">
	      	<?php echo $this->Form->create('Collectible');?>
				<input type="hidden" name="data[Collectible][massProduced]" value="true" ?>
	     	<?php echo $this->Form->end('Yes');?> 	 
	     	<?php echo $this->Form->create('Collectible');?>
				<input type="hidden" name="data[Collectible][massProduced]" value="false" ?>
	     	<?php echo $this->Form->end('No');?>
		</div>

    </div>    
  </div>
</div>