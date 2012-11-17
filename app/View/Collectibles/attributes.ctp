<?php
echo $this -> Minify -> script('js/jquery.form', array('inline' => false));
echo $this -> Minify -> script('js/jquery.treeview', array('inline' => false));
echo $this -> Minify -> script('js/cs.core.tree', array('inline' => false));
echo $this -> Minify -> script('js/cs.attribute', array('inline' => false));
?>

<div id="bread-crumbs">
	<?php echo $this -> Wizard -> progressMenu(array('manufacture' => 'Manufacturer Details', 'attributes' => 'Accessories/Features', 'tags' => 'Tags', 'image' => 'Image', 'review' => 'Review')); ?>			
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo __('Submit New Collectible', true); ?>
			</h2>
			<?php if($this -> Session -> check('add.collectible.variant')) { ?>
			<div class="actions">
				<ul>
					<li>
						<a id="base-collectible-link" class="link"><?php echo __('View Base Collectible'); ?></a>
					</li>
				</ul>
			</div>
			<?php } ?>
		</div>
		<?php echo $this -> element('flash'); ?>
		<?php
		if (isset($errors)) {
			echo $this -> element('errors', array('errors' => $errors));
		}
		?>
		<div class="component-info">
			<div>
				<p><?php echo __('Here you can break the collectible down into it\'s individual parts.  Each collectible can be made up into lot\'s of small parts and we should try to accurately capture that here.')?></p>
				<p><?php echo __('When adding parts to this collectible, you have two options.  You can either add a part from an existing collectible or you can create a new one.'); ?></p>
				<p><?php echo __('In most cases, you will be adding new parts but if you know that this collectible might share a similar part to another collectible, please try and use the Add Existing Part option.'); ?></p>
				<p><b><?php echo __('Part\'s now have the following properties: '); ?></b></p>
				<dl>
			    	<dt><?php echo __('Category'); ?></dt>
			    	<dd><?php echo __('Each part has a category.  This allows us to better organize specific collectible parts.  If you don\'t know what category to put the part in, please use the generic "part" category.'); ?></dd>
			    	<dt><?php echo __('Name'); ?></dt>
			    	<dd><?php echo __('This is a short description for the part.'); ?></dd>
			    	<dt><?php echo __('Description'); ?></dt>
			    	<dd><?php echo __('Use this to add any additional details for the part.'); ?></dd>
			        <dt><?php echo __('Manufacturer'); ?></dt>
			    	<dd><?php echo __('This indicates who made the part.  A majority of the time this will be the same as the manufacturer who made the collectible.'); ?></dd>
			        <dt><?php echo __('Scale'); ?></dt>
			    	<dd><?php echo __('Scale of the part.'); ?></dd>
			        <dt><?php echo __('Count'); ?></dt>
			    	<dd><?php echo __('This indicates how many of the same part this collectible has.  Please note that this is not a property of the part itself but a property of the part being adding to the collectible.'); ?></dd>
			    </dl>
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add">
				<?php echo $this -> Form -> create('Collectible', array('id' => 'add-attributes-form', 'url' => '/' . $this -> params['controller'] . '/' . $this -> action . '/attributes', )); ?>
				<fieldset>
					<legend>
						<?php echo __('Collectible Part Breakdown'); ?>
					</legend>
					<div class="btn-group">
					    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
					    Action
					    <span class="caret"></span>
					    </a>
					    <ul class="dropdown-menu">
							<li><a id="add-new-item-link" class="link" title=' . __('') . '><?php echo __('Add New Part'); ?></a>
							</li>
							<li><a id="add-existing-item-link" class="link" title=' . __('') . '><?php echo __('Add Existing Part'); ?></a>
							</li>
					    </ul>
   					 </div>
					<div id="collectible-attributes-list" class="standard-list attributes">
						<table class="table table-striped">
							<?php
							$lastKey = 0;
							echo '<thead><tr>';
							echo '<th class="category">' . __('Category') . '</th>';
							echo '<th class="name">' . __('Name') . '</th>';
							echo '<th class="description">' . __('Description') . '</th>';
							echo '<th class="manufacturer">' . __('Manufacturer') . '</th>';
							echo '<th class="scale">' . __('Scale') . '</th>';
							echo '<th class="created">' . __('Count') . '</th>';
							echo '<th class="actions"> </th>';
							echo '</tr></thead><tbody>';
							if (isset($this -> data['AttributesCollectible'])) {
								foreach ($this->data['AttributesCollectible'] as $key => $attribute) {
									echo '<tr>';
									echo '<td class="category">' . $attribute['Attribute']['AttributeCategory']['path_name'] . '</td>';
									echo '<td>' . $attribute['Attribute']['name'] . '</td>';

									echo '<td>' . $attribute['Attribute']['description'] . '</td>';
									echo '<td>' . $attribute['Attribute']['Manufacture']['title'] . '</td>';
									echo '<td>' . $attribute['Attribute']['Scale']['scale'] . '</td>';
									echo '<td>' . $attribute['count'] . '</td>';
									echo '<td class="attribute-action">';
									echo '<div class="btn-group">';
									echo '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Action<span class="caret"></span></a>';
									echo '<ul class="dropdown-menu">';
									echo '<li><a class="link remove-attribute" title=' . __('Remove Part') . '>Remove Part</a>';
									echo '</li>';
									echo '</ul>';
									echo '</div>';
									echo '</td>';
									if (isset($attribute['attribute_id']) && !empty($attribute['attribute_id'])) {
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][count]" value="' . $attribute['count'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][attribute_id]" value="' . $attribute['attribute_id'] . '"/>';
									} else {
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][Attribute][attribute_category_id]" value="' . $attribute['Attribute']['attribute_category_id'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][Attribute][description]" value="' . $attribute['Attribute']['description'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][Attribute][name]" value="' . $attribute['Attribute']['name'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][Attribute][manufacture_id]" value="' . $attribute['Attribute']['manufacture_id'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][Attribute][scale_id]" value="' . $attribute['Attribute']['scale_id'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][count]" value="' . $attribute['count'] . '"/>';
									}

									echo '</tr>';
									$lastKey = $key;

								}
								echo '<script>var lastAttributeKey =' . $lastKey . ';</script>';
							}
							?>
						</table>
					</div>
				</fieldset>	
				<input type="hidden" name="data[dummy]" value="" />
				<?php echo $this -> Form -> end(); ?>
				
				<?php echo $this -> Form -> create('Collectible', array('url' => '/' . $this -> params['controller'] . '/' . $this -> action . '/attributes', 'id' => 'skip-attributes-form')); ?>
					<input type="hidden" name="data[skip]" value="true" />
				</form>
				<div class="links">
					<button id="add-attributes-button" class="btn btn-primary"><?php echo __('Submit'); ?></button>
					<button id="skip-attributes-button" class="btn btn-primary"><?php echo __('Skip'); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if($this -> Session -> check('add.collectible.variant')) { ?>
<div id="base-collectible" class="dialog">
<?php
echo $this -> element('collectible_detail_attributes', array('collectibleCore' => $collectible));
?>
</div>
<?php } ?>
<script>
	var attributeNumber = 0;

	$(function() {
		//Eh move this out of here
		$(".ui-icon-info").tooltip({
			position : 'center right',
			opacity : 0.7
		});
		$('#add-attributes-button').click(function() {
			$('#add-attributes-form').submit();
		});
		$('#skip-attributes-button').click(function() {
			$('#skip-attributes-form').submit();
		});
		$('#base-collectible').dialog({
			'autoOpen' : false,
			'width' : 525,
			'autoHeight' : true,
			'resizable' : false,
			'modal' : true
		});
		$('#base-collectible-link').click(function() {
			$('#base-collectible').dialog('open');
		});

		if ( typeof lastAttributeKey !== "undefined") {
			//If there is at least one added already then we will want to take that one and +1 for the next.
			attributeNumber = ++lastAttributeKey;
		}

		var addExistingCollectiblesAttributes = new ContributeAddExistingCollectibleAttributes({
			$element : $('.standard-list.attributes')
		});

		addExistingCollectiblesAttributes.init();

		var addCollectiblesAttributes = new ContributeAddCollectibleAttributes({
			$element : $('.standard-list.attributes')
		});

		addCollectiblesAttributes.init();

		var removeAttribute = new ContributeRemoveAttribute({
			$element : $('.standard-list.attributes')
		});

		removeAttribute.init();

	});

</script>

<?php echo $this -> element('attribute_collectible_add_dialog'); ?>
<?php echo $this -> element('attribute_collectible_add_existing_dialog'); ?>
