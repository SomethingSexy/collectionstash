    <?php      
		echo $this->element('search_collectible',
		array("searchUrl" => '/collectibles/addVariantSelectCollectible'));
	?>

<div class="component" id="collectibles-list-component">
  <div class="inside" >
     <div class="component-title">
      <h2><?php __('Add Collectible Variant');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>To add a variant to a collectible, first select a collectible below that you are adding a variant for.</div> 
    </div>    
    <div class="component-view">
      <div class="collectibles view">
        <?php  
        foreach ($collectibles as $collectible):
        ?>
        	<div class="collectible item">
          	<div class="collectible image"><?php echo $fileUpload->image($collectible['Upload'][0]['name'], array('width' => '100')); ?>
              <div class="collectible image-fullsize hidden"><?php echo $fileUpload->image($collectible['Upload'][0]['name'], array('width' => 0)); ?></div>
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
          	     <dt>Manufacture: </dt><dd><a target="_blank" href="<?php echo $collectible['Manufacture']['url']; ?>"><?php echo $collectible['Manufacture']['title']; ?></a></dd>
          	     <dt>Type: </dt><dd><?php echo $collectible['Collectibletype']['name']; ?></dd>
               </dl>
            	</div>
        	 <div class="links">
				<?php echo $html->link('Select', array('action' => 'addVariant', $collectible['Collectible']['id'])); ?>
        	 </div>
        	 <div class="collectible actions"><?php echo $html->link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'])); ?></div>
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