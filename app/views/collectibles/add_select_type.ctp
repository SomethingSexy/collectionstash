<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('Contribute');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>
      	<p><?php __('Collection Stash is a community supported database that gives it\'s users the ability to contribute to a growing compilation of collectibles. By helping maintain this database you can add those collectibles to your personal stash.  To ensure our data is as complete and accurate as possible, we will give you the tools to be able to add as much information as you can about your collectible. Each item submitted will require approval by one of our administrators before it can be added to your stash. This will help make sure that each collectible added is unique and accurate.') ?></p>
      	<p><?php __('To get started, we will need to know information about the collectible you are trying to add.')?></p>
      	
      </div> 
    </div>
    <div class="component-view">
      	<div class="question">
      		<p><?php __('Is the collectible you are adding mass-produced?') ?></p>
			<dl>
				<dt><?php __('Mass-Produced: '); ?></dt>
				<dd><?php __('A mass-produced collectible is a collectible that is produced in large amounts for sale (i.e. more than one is made).'); ?></dd>
			</dl>      	
      	</div>

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