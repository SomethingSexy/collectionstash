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
					<ul class="form-fields unstyled">
						<li>
							<div class="input text required">
								<div class="label-wrapper">
									<label for="AttributeReason"><?php echo __('Reason'); ?></label>
								</div>
								<?php echo $this -> Form -> textarea('reason', array()); ?>
							</div>
						</li>
						<li>
							<div class="input">
								<div class="label-wrapper">
									<label for="CollectibleLimited"><?php echo __('Remove?'); ?></label>
								</div>
								<?php echo $this -> Form -> checkbox('remove', array('label' => false, 'div' => false)); ?>
							</div>
						</li>
					</ul>
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

