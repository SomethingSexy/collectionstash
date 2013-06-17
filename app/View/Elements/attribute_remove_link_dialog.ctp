<div id="attribute-remove-link-dialog" class="dialog attribute hide" title="Remove Part From Collectible">
	<div class="component component-dialog">
		<div class="inside" >
			<?php echo $this -> element('flash'); ?>
			<div class='component-message error'>
				<span></span>
			</div>
			<div class="component-view">
				<div id="remove-attribute-link">
					<div class="component-info">
						<p><?php echo __('Removing the part from the collectible will unlink the relationship.  The part will still exist and can be added to other collectibles unless this is the only collectible that this part is linked to, then it will be deleted.'); ?></p>
					</div>	
					<?php echo $this -> Form -> create('AttributesCollectible', array('data-form-model' => 'Attribute', 'id' => 'AttributeCollectibleRemoveForm')); ?>
						<fieldset>
							<ul class="form-fields unstyled">
			                    <li>
			                    	<div class="input text required">
			                    	<div class="label-wrapper">
										<label for="AttributeReason"><?php echo __('Reason'); ?></label>
									</div>
									<?php echo $this -> Form -> textarea('reason', array()); ?> </div>
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
			</div>
		</div>
	</div>
</div>