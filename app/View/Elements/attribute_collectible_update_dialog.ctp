<div id="update-attribute-collectible-dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h4 id="myModalLabel">Edit the Collectible's Part</h4>
			</div>
			<div class="modal-body">
				<div class='component-message error'>
					<span></span>
				</div>
				<?php echo $this -> Form -> create('AttributesCollectible', array('data-form-model' => 'AttributesCollectible', 'id' => 'AttributesCollectibleUpdateForm')); ?>
				<fieldset>
					<?php 
						if($collectible['Collectible']['custom']) {
					?>
						<div class="select">
							<div class="label-wrapper ">
								<label for="AttributesCollectibleCount">Status</label>
							</div>
							<select id="AttributesCollectibleAttributeCollectibleTypeId" class="form-control" data-type="AttributesCollectible" data-name="attribute_collectible_type_id" name="data[AttributesCollectible][attribute_collectible_type_id]">
								<option value="1">Owned</option>
								<option value="2">Wanted</option>
								<option value="3">Preordered</option>
							</select>
						</div>
					<?php } ?>
					<div class="form-group">
						<label class="control-label" for="AttributesCollectibleCount">Description</label>
						<input name="data[AttributesCollectible][count]" type="number" id="AttributesCollectibleCount" class="form-control" required="required">
					</div>
				</fieldset>
				<?php echo $this -> Form -> end(); ?>
			</div>
		
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
					Close
				</button>
				<button class="btn btn-primary save" autocomplete="off">
					Submit
				</button>
			</div>
		</div>
	</div>
</div>