<div id="update-attribute-collectible-dialog" class="dialog attribute" title="Update Collectible Part">
	<div class="component component-dialog">
		<div class="inside" >
			<?php echo $this -> element('flash'); ?>
			<div class="component-info">
				<div>
					<?php echo __('Use this action to update the properties around the part that is added to this collectible.')
					?>
				</div>
			</div>
			<div class='component-message error'>
				<span></span>
			</div>
			<div class="component-view">
				<div class="attribute-form">
					<?php echo $this -> Form -> create('AttributesCollectible', array('data-form-model' => 'AttributesCollectible', 'id' => 'AttributesCollectibleUpdateForm')); ?>
					<fieldset>
						<ul class="form-fields unstyled">
		                    <li>
								<?php echo $this -> Form -> input('count', array('label' => __('Count'), 'before' => '<div class="label-wrapper required">', 'between' => '</div>')); ?>
							</li> 
						</ul>
					</fieldset>
					<?php echo $this -> Form -> end(); ?>
				</div>						
			</div>
		</div>
	</div>
</div>