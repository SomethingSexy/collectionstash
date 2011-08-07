<?php echo $this -> Html -> script('variant-add', array('inline' => false));?>
<?php if($this -> Session -> check('add.collectible.mode.variant')) { ?>
<?php      
	echo $this->element('collectible_detail', array(
		'title' => __('Base Collectible Details', true),
		'showStatistics' => false,
		'showWho' => false,
		'collectibleDetail' => $collectible
	));
?>
<?php } ?>
<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(array('manufacture'=>'Manufacturer Details', 'variantFeatures'=>'Variant Features', 'attributes'=>'Accessories/Features', 'tags'=>'Tags','image'=>'Image', 'review'=> 'Review')); ?>	
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo __('Collectible Attributes')?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				Fill out the following information to add the collectible.
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add">
				<?php echo $this -> Form -> create('Collectible', array('url' => $this->here)); ?>
						<fieldset>
						<legend>
							<?php __('Variant Details');?>
						</legend>
						<ul class="form-fields">
							<li>
								<div class="label-wrapper">
									<label for="CollectibleExclusive">
										<?php __('Exclusive') ?>
									</label>
								</div>
								<?php echo $this -> Form -> input('exclusive', array('div' => false, 'label' => false));?>
							</li>	
							<li>
								<div class="label-wrapper">
									<label for="">
										<?php __('Exclusive Retailer') ?>
									</label>
								</div>
								<?php echo $this -> Form -> input('retailer_id', array('div' => false, 'label' => false, 'empty' => true));?>
							</li>					
						</ul>
					</fieldset>	
					<fieldset>
						<legend>
							<?php __('Variant Features');?>
						</legend>
						<div id="variant-attributes-list" class="attributes-list">
							<ul>
								<?php
									echo '<li class="title">';
									echo '<span class="attribute-name">'.__('Part', true).'</span>';
									echo '<span class="attribute-description">'.__('Description', true).'</span>';
									echo '</li>';
								if(isset($this -> data['AttributesCollectible'])) {
									foreach($this->data['AttributesCollectible'] as $key => $attribue) {
										if($attribue['variant'] === '1') {
											echo '<li>';
											echo '<span class="attribute-name">';
											echo $attribue['name'];
											echo '</span>';
											echo '<span class="attribute-description">';
											echo $attribue['description'];
											echo '</span>';
											echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][attribute_id]" value="' . $attribue['attribute_id'] . '"/>';
											echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][description]" value="' . $attribue['description'] . '"/>';
											echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][name]" value="' . $attribue['name'] . '"/>';
											echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][variant]" value="' . $attribue['variant'] . '"/>';
											echo '</li>';
											$lastKey = $key;
										}
									}
									echo '<script>var lastAttributeKey =' . $lastKey . ';</script>';
								}
								?>
							</ul>
						</div>
						<div>
							<a class="add-variant-attribute">Add Feature</a>
						</div>
					</fieldset>						
					
				<?php echo $this -> Form -> end(__('Submit', true));?>
			</div>
		</div>
	</div>
</div>
<script>
	$(".ui-icon-info").tooltip({
		position: 'center right',
		opacity: 0.7
	});
</script>
<div id="add-attribute-dialog" class="dialog" title="Add Attribute">
	<div class="component">
		<div class="inside" >
			<div class="component-info">
				<div>
					<?php __('Fill out the information below to add an Attribute to this variant.') ?>
				</div>
			</div>
			<div class="component-view">
				<fieldset>
					<ul id="add-attribute-dialog-fields" class="form-fields">
						<li id="description-field">
							<div class="label-wrapper">
								<label for="CollectibleName">
									<?php __('Description') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('description', array('maxlength' => 50, 'id' => 'attributeDescription', 'div' => false, 'label' => false));?>
						</li>
					</ul>
				</fieldset>
			</div>
		</div>
	</div>
</div>