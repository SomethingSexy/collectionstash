<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2>
			    <h2><?php  echo __('Confirm Collectible Submission');?></h2>
			</h2>
		</div>
	    <?php echo $this->element('flash'); ?>
	    <div class="component-info">
	      	<div><?php echo __('You have submitted the following collectible to Collection Stash.  You will receive a confirmation e-mail once the submission has been approved.');?></div> 
	    </div>
		<div class="component-view">
			<div class="collectibles">
				<?php echo $this->element('collectible_detail_core', array(
					'collectibleCore' => $collectible,
					'showTags' => true
				));	?>	
			</div>
		</div>
	</div>
</div>


