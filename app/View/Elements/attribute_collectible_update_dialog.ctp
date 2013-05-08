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
								<div class="select">
									<div class="label-wrapper ">
										<label for="AttributesCollectibleCount">Type</label>
									</div>
									<select id="AttributesCollectibleAttributeCollectibleTypeId" data-type="AttributesCollectible" data-name="attribute_collectible_type_id" name="data[AttributesCollectible][attribute_collectible_type_id]">
										<option value="1">Owned</option>
										<option value="2">Wanted</option>
										<option value="3">Preordered</option>
									</select>
								</div>
							</li>
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