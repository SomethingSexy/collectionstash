<div id="attribute-remove-link-dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h4 id="myModalLabel">Remove Part From Collectible</h4>
			</div>
			<div class="modal-body">
				<div class='component-message error'>
					<span></span>
				</div>
		
				<p>
					<?php echo __('Removing the part from the collectible will unlink the relationship.  The part will still exist and can be added to other collectibles unless this is the only collectible that this part is linked to, then it will be deleted.'); ?>
				</p>
		
				<?php echo $this -> Form -> create('AttributesCollectible', array('data-form-model' => 'Attribute', 'id' => 'AttributeCollectibleRemoveForm')); ?>
				<fieldset>
					<div class="form-group">
						<label for="AttributeReason"><?php echo __('Reason'); ?></label>
						<textarea name="data[AttributesCollectible][reason]" id="AttributesCollectibleReason" class="form-control"></textarea>
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

