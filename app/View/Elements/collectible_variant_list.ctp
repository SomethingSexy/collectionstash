<?php if (!empty($variants)) { ?>
	<div class="component variant-list">
	  <div class="inside" >
	     <div class="component-title">
	      <h2><?php echo __('Variants'); ?></h2>
	    </div>
	    <div class="component-view">
	      <div class="collectibles view">
	        <?php  
	        foreach ($variants as $variant):
	        ?>
	        	<div class="collectible item">
	        		<div class="collectible image">
						<?php
						if (!empty($variant['Upload'])) {
							echo '<a href="/collectibles/view/' . $variant['Collectible']['id'] . '">';
							echo $this -> FileUpload -> image($variant['Upload'][0]['name'], array('escape' => false, 'width' => 150, 'height' => 150));
							echo '</a>';
						} else {
							echo '<img src="/img/silhouette_thumb.png"/>';
						}
					?>
					</div>
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
	      <h2><?php echo __('Variants'); ?></h2>
	    </div>
	    <div class="component-view">
	      <div class="collectibles view empty">
			<p><?php echo __('This collectible has no variants.'); ?></p>	
	      </div>
	    </div>
	  </div>
	</div>		
<?php	  }