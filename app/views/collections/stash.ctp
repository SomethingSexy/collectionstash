<?php echo $this->Html->script('collectible-list',array('inline'=>false)); ?>
<div class="container2">
<div class="info">
	<h2><?php __('My Stash');?></h2>
	<div>You have <?php echo $collectibleCount ?> collectibles in this stash.</div>
</div>

<div class="collectibles view">
	<div>
	  <?php echo $html->link('Add to Stash', array('action'=>'add', 'stashId' => $stashId)); ?>
	  </div>
	<?php  
	foreach ($collectibles as $collectible):
	?>
	<div class="collectible item">  
	  <div class="collectible image"><?php echo $fileUpload->image($collectible['Collectible']['Upload'][0]['name'], array('width' => 100)); ?>
    <div class="collectible image-fullsize hidden"><?php echo $fileUpload->image($collectible['Collectible']['Upload'][0]['name'], array('width' => 0)); ?></div>
    </div>
	  <div class="collectible detail">
	   <dl>
	     <dt>Name: </dt><dd><?php echo $collectible['Collectible']['name']; ?></dd>
	     <dt>Manufacture: </dt><dd><a target="_blank" href="<?php echo $collectible['Collectible']['Manufacture']['url']; ?>"><?php echo $collectible['Collectible']['Manufacture']['title']; ?></a></dd>
	     <dt>Type: </dt><dd><?php echo $collectible['Collectible']['Collectibletype']['name']; ?></dd>
       <dt>Description: </dt><dd><?php echo $collectible['Collectible']['description']; ?></dd>
       <dt><a href="<?php echo $collectible['Collectible']['url']; ?>" target="_blank">Product Link</a></dd>
     </dl>
	 </div>
	 <div class="links">
	    <ul>
	     <li><?php echo $html->link('Remove', array('controller' => 'collections', 'action' => 'remove', $collectible['CollectiblesStash']['id'])); ?></li>
	     <li><?php echo $html->link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'])); ?></li>
	     <li><?php echo $html->link('Edit', array('controller' => 'collections', 'action' => 'editcollectible', $collectible['Collectible']['id'])); ?></li>	    
	    </ul>
	 </div>
  </div>
<?php endforeach; ?>

</div>
</div>