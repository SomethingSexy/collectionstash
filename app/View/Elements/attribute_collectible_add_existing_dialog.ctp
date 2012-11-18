<div id="attribute-collectible-add-existing-dialog" class="dialog attribute" title="Add Existing Collectible Part">
	<div class="component component-dialog">
		<div class="inside" >
			<?php echo $this -> element('flash'); ?>
			<div class="component-info">
				<div>
					<p><?php echo __('Use this action to add an existing part to this collectible.  ')?></p>
				</div>
			</div>
			<div class='component-message error'>
				<span></span>
			</div>
			<div class="component-view">
				<div class="attribute-form">
					<?php echo $this -> Form -> create('AttributesCollectible', array('data-form-model' => 'AttributesCollectible', 'id' => 'AttributesCollectibleAddForm')); ?>
					<fieldset>
						<ul class="form-fields unstyled">
						<li class="attribute">
						   <div class="label-wrapper required">
		                        <label for=""> <?php echo __('Part')
		                            ?></label>
		                    </div>
		                    <?php
							echo '<a class="link" id="select-attribute-link">' . __('Select By Collectible') . '</a>';
							echo $this -> Form -> hidden('attribute_id');
							?>
		                    </li>  
		                    <li>
								<?php echo $this -> Form -> input('count', array('label' => __('Count'), 'before' => '<div class="label-wrapper required">', 'between' => '</div>')); ?>
							</li> 
						</ul>
					</fieldset>
					<?php echo $this -> Form -> end(); ?>
				</div>						
				<div class="item-search">
					<div class="directional-text">
						<?php echo __('Search for a collectible that has the part you want to add.  Use the search box to narrow your results.  Select the collectible to see the list of parts for that collectible to select from.'); ?>
					</div>
					<div class="search">
						<?php echo $this -> element('search_collectible', array()); ?>
					</div>
					<div class="items">
						<ul class="unstyled"></ul>
					</div>
					<div class="paging"></div>
				</div>
			</div>
		</div>
	</div>
</div>