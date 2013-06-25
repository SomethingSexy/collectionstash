<div id="update-attribute-collectible-dialog" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
			×
		</button>
		<h3 id="myModalLabel">Edit the Collectible's Part</h3>
	</div>
	<div class="modal-body">
		<div class='component-message error'>
			<span></span>
		</div>
		<?php echo $this -> Form -> create('AttributesCollectible', array('data-form-model' => 'AttributesCollectible', 'id' => 'AttributesCollectibleUpdateForm')); ?>
		<fieldset>
			<ul class="form-fields unstyled">
				<?php 
					if($collectible['Collectible']['custom']) {
				?>
				<li>
					<div class="select">
						<div class="label-wrapper ">
							<label for="AttributesCollectibleCount">Status</label>
						</div>
						<select id="AttributesCollectibleAttributeCollectibleTypeId" data-type="AttributesCollectible" data-name="attribute_collectible_type_id" name="data[AttributesCollectible][attribute_collectible_type_id]">
							<option value="1">Owned</option>
							<option value="2">Wanted</option>
							<option value="3">Preordered</option>
						</select>
					</div>
				</li>
				<?php } ?>
				<li>
					<?php echo $this -> Form -> input('count', array('label' => __('Count'), 'before' => '<div class="label-wrapper required">', 'between' => '</div>')); ?>
				</li>
			</ul>
		</fieldset>
		<?php echo $this -> Form -> end(); ?>
	</div>

	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">
			Close
		</button>
		<button class="btn btn-primary save" autocomplete="off">
			Submit
		</button>
	</div>
</div>