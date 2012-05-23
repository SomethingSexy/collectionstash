<?php if (!empty($variants)) { ?>
	<div class="component variant-list">
	  <div class="inside" >
	     <div class="component-title">
	      <h2><?php echo __('Variants');?></h2>
	    </div>
	    <div class="component-view">
	      <div class="collectibles view">
	        <?php  
	        foreach ($variants as $variant):
	        ?>
	        	<div class="collectible item">
	            	<?php echo $this -> element('collectible_list_image', array(
						'collectible' => $variant
					));?>
					<?php /*echo $this -> element('collectible_list_detail', array(
						'collectible' => $variant['Collectible'],
						'manufacture' => $variant['Manufacture'],
						'license' => $variant['License'],
						'collectibletype' => $variant['Collectibletype']
					));*/ ?>
	        	 <div class="collectible actions"><?php echo $this -> Html ->link('Details', array('controller' => 'collectibles', 'action' => 'view', $variant['Collectible']['id'])); ?></div>
	          </div>
	        <?php endforeach; ?>      
	      </div>
	    </div>
	  </div>
	</div>	
<?php } else { ?>
	<div class="component variant-list" id="collectibles-list-component">
	  <div class="inside" >
	     <div class="component-title">
	      <h2><?php echo __('Variants');?></h2>
	    </div>
	    <div class="component-view">
	      <div class="collectibles view empty">
			<p><?php echo __('This collectible has no variants.'); ?></p>	
	      </div>
	    </div>
	  </div>
	</div>		
<?php	  }