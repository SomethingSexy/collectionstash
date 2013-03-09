<div class="well" id="collectibletypes-list-component">
	<h2><?php echo __('Submit New Collectible'); ?></h2>
    <?php echo $this -> element('flash'); ?>
    <div class="alert alert-info">
    <p><?php echo __('Before you can submit a new collectible to Collection Stash you have to know what you want to add!  Think of a collectible as product that is made and sold.'); ?></p>
    <p><?php echo __('Below is a list of collectible types that we currently support.  Find the one that best matches what you want to submit.  We are constantly adding new types so stay tuned for more information!');?></p>
    </div>
   	<?php echo $this -> Tree -> generate($collectibleTypes, array('id' => 'tree', 'model' => 'Collectibletype', 'element' => 'tree_create_collectibletype_node')); ?>
</div>