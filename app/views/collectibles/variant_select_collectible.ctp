    <?php      
		echo $this->element('search_collectible',
		array("searchUrl" => '/collectibles/variantSelectCollectible'));
	?>
<div class="component" id="collectibles-list-component">
  <div class="inside" >
     <div class="component-title">
      <h2><?php __('Contribute Variant - Select Collectible');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div><p><?php __('To add a variant, you need to find the collectible this will be a variant of.  Browser through the list or use the search to narrow the results.  Click select to continue.');?></p></div> 
    </div>    
    <div class="component-view">
      <div class="collectibles view">
        <?php  
        foreach ($collectibles as $collectible):
        ?>
        	<div class="collectible item">
				<?php echo $this -> element('collectible_list_image', array(
					'collectible' => $collectible
				));?>
				<?php echo $this -> element('collectible_list_detail', array(
					'collectible' => $collectible['Collectible'],
					'manufacture' => $collectible['Manufacture'],
					'license' => $collectible['License'],
					'collectibletype' => $collectible['Collectibletype']
				));?>
        	 <div class="links">
				<?php echo $html->link('Select', array('action' => 'variantSelectCollectible', $collectible['Collectible']['id'])); ?>
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