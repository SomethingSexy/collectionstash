<div class="collectibles form">
<?php echo $this->Form->create('Collectible');?>
	<fieldset>
 		<legend><?php __('Edit Collectible'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('manufacture_id');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Collectible.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Collectible.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Collectibles', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Manufactures', true), array('controller' => 'manufactures', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Manufacture', true), array('controller' => 'manufactures', 'action' => 'add')); ?> </li>
	</ul>
</div>