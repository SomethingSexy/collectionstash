<?php echo $this -> Html -> script('attributes-add', array('inline' => false));?>
<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(); ?>	
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo $collectible_title ?>
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
				<?php echo $this -> Form -> create('Collectible', array('url' => '/'.$this->params['controller']. '/'.$this->action.'/attributes', )); ?>
				<fieldset>
					<legend>
						<?php __('Part Break Down');?>
					</legend>
					<ul class="form-fields">
						<li>
							<div class="label-wrapper">
								<label for="">
									<?php __('Features') ?>
								</label>
								<a class="ui-icon ui-icon-info" title="<?php echo __('Select add, to add an feature for this collectible.  An feature is a way to define what makes this collectible an exclusive or variant.', true) ?>" alt="info"></a>
							</div>
							<div id="collectible-attributes-list" class="attributes-list">
								<ul>
									<?php
									$lastKey = 0;
									if(isset($this -> data['AttributesCollectible'])) {
										foreach($this->data['AttributesCollectible'] as $key => $attribue) {
											if($attribue['variant'] !== '1') {
												echo '<li>';
												echo '<span class="attribute-label">Part: </span>';
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
								<a class="add-attribute">Add Attribute</a>
							</div>
						</li>
					</ul>
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