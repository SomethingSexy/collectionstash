<div class="component" id="manufactures-list-component">
  <div class="inside" >
     <div class="component-title">
      <h2><?php __('Contribute - Select Collectible Type');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>
      	<p><?php __('Select from the following collectible types for Manufacturer '); echo $manufacturer.'.'; ?></p>
      </div> 
    </div>    
    <div class="component-view">
      <div class="manufactures view">
        <?php  
        foreach ($collectibleTypes as $collectibleType):
        ?>
        	<div class="manufacture item">
				<div class="manufacture detail">
					<span class="manufacture name"><?php echo $collectibleType['Collectibletype']['name'] ?></span>
				</div>	
        		<div class="links">
					<?php echo $html->link('Select', array('action' => 'selectManufacturer',$collectibleType['Collectibletype']['id'])); ?>
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