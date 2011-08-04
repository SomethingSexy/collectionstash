<div class="component" id="manufactures-list-component">
  <div class="inside" >
     <div class="component-title">
      <h2><?php __('Contribute - Select Manufacture');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>
      	<p><?php __('') ?></p>
      </div> 
    </div>    
    <div class="component-view">
      <div class="manufactures view">
        <?php  
        foreach ($manufactures as $manufacture):
        ?>
        	<div class="manufacture item">
				<div class="manufacture detail">
					<span class="manufacture name"><?php echo $manufacture['Manufacture']['title'] ?></span>
					<ul class="manufacture collectibletypes">
					<?php foreach ($manufacture['CollectibletypesManufacture'] as $collectibleTypes): 
						echo '<li>';
						echo $collectibleTypes['Collectibletype']['name'];		
						echo '</li>';	
					endforeach; ?>
					</ul>
				</div>	
        		<div class="links">
					<?php echo $html->link('Select', array('action' => 'selectManufacturer',$manufacture['Manufacture']['id'])); ?>
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