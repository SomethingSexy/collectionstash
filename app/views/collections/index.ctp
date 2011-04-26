<?php echo $this->Html->script('collectible-list',array('inline'=>false)); ?>
<div class="component" id="collectibles-list-component">
  <div class="inside" >
     <div class="component-title">
      <h2><?php __('My Stash');?></h2>
    </div>
    <div class="component-info">
      <div>You have <?php echo $collectibleCount ?> collectibles in this stash. <?php echo $html->link('Add to Stash', array('action'=>'addSearch', 'stashId' => $this->Session->read('stashId'))); ?></div> 
    </div>
    <div class="component-view">
      <div class="collectibles view">
        <?php  
        foreach ($collectibles as $collectible):
        ?>
        	<div class="collectible item">
          	<div class="collectible image"><?php echo $fileUpload->image($collectible['Collectible']['Upload'][0]['name'], array('width' => '100')); ?>
              <div class="collectible image-fullsize hidden"><?php echo $fileUpload->image($collectible['Collectible']['Upload'][0]['name'], array('width' => 0)); ?></div>
              </div>
          	  <div class="collectible detail">
          	   <dl>
          	     <dt>Name: </dt><dd><?php echo $collectible['Collectible']['name']; ?><?php if($collectible['Collectible']['exclusive']){ __(' - Exclusive'); } ?> </dd>
          	     <?php
          	       if ($collectible['Collectible']['variant'])
                   {
                     echo '<dt>';
                     __('Variant:');
                     echo '</dt><dd>';
                     __('Yes');
                     echo '</dd>';
                   }
                 ?>  	     
          	     <dt>Manufacture: </dt><dd><a target="_blank" href="<?php echo $collectible['Collectible']['Manufacture']['url']; ?>"><?php echo $collectible['Collectible']['Manufacture']['title']; ?></a></dd>
          	     <dt>Type: </dt><dd><?php echo $collectible['Collectible']['Collectibletype']['name']; ?></dd>
               </dl>
            	</div>
        	 <div class="links">
        	  
        	 </div>
        	 <div class="collectible actions">
        		<?php echo $html->link('Remove', array('controller' => 'collections', 'action' => 'remove', $collectible['id'])); ?>
	     		<?php echo $html->link('Details', array('action' => 'viewCollectible', $collectible['id'])); ?>
	     		<?php echo $html->link('Edit', array('controller' => 'collections', 'action' => 'editCollectible', $collectible['id'])); ?>  </div>
          </div>
        <?php endforeach; ?>
        <div class="paging">
          <p>
          <?php
           echo $this->Paginator->counter(array(
           'format' => __('Page %page% of %pages%, showing %current% collectibles out of %count% total.', true)
           ));
          ?>  </p>
          <?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
          <?php echo $this->Paginator->numbers();?>
          <?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
        </div>
      
      </div>
</div>
</div>
</div>
