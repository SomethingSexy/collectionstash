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
        foreach ($collectibleTypes as $collectibleType){
			if(in_array($collectibleType['Collectibletype']['id'], $manufacturerCollectibletypes)) {
        ?>
        	<div class="collectibletype item">
				<div class="collectibletype detail">
					<span class="collectibletype name"><?php echo $html->link($collectibleType['Collectibletype']['name'], array('action' => 'selectCollectibleType',$collectibleType['Collectibletype']['id'])); ?></span>
					<?php if(!empty($collectibleType['children'])){
					echo '<ul class="collectibletype specialized-types">';
					foreach ($collectibleType['children'] as $specializedType) {
						if(in_array($specializedType['Collectibletype']['id'], $manufacturerCollectibletypes)) { 
							echo '<li>';
							echo $html->link($specializedType['Collectibletype']['name'], array('action' => 'selectCollectibleType',$specializedType['Collectibletype']['id']));
							echo '</li>';	
						}
					}
					echo '</ul>';							
					}?>
				</div>	
         </div>
        <?php 
        	}
        } ?>
      </div>
    </div>
  </div>
</div>