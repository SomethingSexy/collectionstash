<div id="attribute-remove-dialog" class="dialog hide attribute" title="Delete Part">
	<div class="component component-dialog">
		<div class="inside" >
			<?php echo $this -> element('flash'); ?>
			<div class='component-message error'>
				<span></span>
			</div>
			<div class="component-view">
				<div id="remove-attribute">
					<div class="component-info">
						<div class="attribute-attached">
							<?php echo __('This part is linked to other collectibles.  Deleting this part will also remove it from those collectibles that this part is attached too.'); ?>
						</div>
						<div class="attribute-not-attached">
							<?php echo __('This part is not linked to other collectibles.  Deleting this part  will remove it permanately.'); ?>
						</div>
					</div>
					<div class="attribute-form">
						<?php echo $this -> Form -> create('Attribute', array('data-form-model' => 'Attribute', 'id' => 'AttributeRemoveForm')); ?>
						<fieldset>
							<ul class="form-fields unstyled">
			                    <li>
			                    	<div class="input text required">
			                    	<div class="label-wrapper">
										<label for="AttributeReason"><?php echo __('Reason'); ?></label>
									</div>
									<?php echo $this -> Form -> textarea('reason', array()); ?> </div>
								</li> 
								<li class="directional-text">
									<?php echo __('If the reason you are deleting this part is because it is a duplicate, you can link this part to a different part.  This will replace the part you are removing with the part you will select and automatically attach it to the collectibles this part is attached too.'); ?>
								</li>
								<li class="link-item">
									<div class="label-wrapper">
										<label for="AttributeReason"><?php echo __('Link'); ?></label>
									</div>	
									<?php echo $this -> Form -> checkbox('link', array()); ?>	
								</li>
								<li class="how-link-item">
									<div class="label-wrapper">
										<label for="AttributeReason"><?php echo __('How would you like to find an item?'); ?></label>
									</div>	
									<a class="link collectible-search"><?php echo __('By Collectible'); ?></a>
								</li>
								<li class="replacement-item">
									<?php echo $this -> Form -> hidden('replace_attribute_id'); ?>
									<div class="label-wrapper">
										<label for=""><?php echo __('Replacement item'); ?></label>
									</div>	
									<div class="static-field">
										
									</div>
								</li>
							</ul>
						</fieldset>
						<?php echo $this -> Form -> end(); ?>
					</div>
					<div class="item-search">
						<div class="directional-text">
							<?php echo __('Search for a collectible that has the part you want to replace.  Use the search box to narrow your results.  Select the collectible to see the list of parts for that collectible to select from.'); ?>
						</div>
						<div class="search">
							<?php echo $this -> element('search_collectible', array()); ?>
						</div>
						<div class="items">
							<ul></ul>
						</div>
						<div class="paging"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>