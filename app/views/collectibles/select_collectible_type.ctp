<div class="component" id="collectibletypes-list-component">
  <div class="inside" >
     <div class="component-title">
      <h2><?php __('Contribute - Select Collectible Type');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>
      	<p><?php __('Select from the following collectible types for Manufacturer '); echo $manufacturer.'.'; ?></p>
      	<p><?php __('Depending on the type selected, there might be different details you can add.'); ?></p>
      </div> 
    </div>    
    <div class="component-view">
      <div class="manufactures view">
        <?php  
        foreach ($collectibleTypes as $collectibleType):
        ?>
        	<div class="collectibletype item">
				<div class="collectibletype detail">
					<span class="collectibletype name"><?php echo $html->link($collectibleType['Collectibletype']['name'], array('action' => 'selectCollectibleType',$collectibleType['Collectibletype']['id'])); ?></span>
					<ul class="collectibletype specialized-types">
					<?php foreach ($collectibleType['CollectibletypesManufactureSpecializedType'] as $specializedType): 
						echo '<li>';
						echo $specializedType['SpecializedType']['name'];		
						echo '</li>';	
					endforeach; ?>
					</ul>
				</div>	
        		<div class="links">
					
        	 	</div>
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