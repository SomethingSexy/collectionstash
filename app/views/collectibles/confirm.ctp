<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2>
			    <h2><?php  __('Confirm Collectible Update');?></h2>
			</h2>
		</div>
	    <?php echo $this->element('flash'); ?>
	    <div class="component-info">
	      	<div><?php __('You have submitted the following collectible to be updated.  You will receive a confirmation e-mail once the update has been approved.');?></div> 
	    </div>
		<div class="component-view">
			<div class="collectibles">
				<?php echo $this->element('collectible_detail_core', array(
					'collectibleCore' => $collectible
				));	?>	
			</div>
		</div>
	</div>
</div>


