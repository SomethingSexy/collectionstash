<?php
//Setup defaults here if they are not defined.
if (!isset($showImage)) {
	$showImage = true;
}
if (!isset($showAttributes)) {
	$showAttributes = true;
}
if (!isset($showCompareFields)) {
	$showCompareFields = false;
}
if (!isset($showEdit)) {
	$showEdit = false;
}
if (!isset($adminMode)) {
	$adminMode = false;
}
?>

<div class="collectible item">
	<?php
	if ($showImage) {
		if (isset($showEdit) && $showEdit) {
			echo $this -> element('collectible_detail_upload', array('collectibleCore' => $collectibleCore, 'editImageUrl' => $editImageUrl, 'showEdit' => $showEdit));
		} else {
			echo $this -> element('collectible_detail_upload', array('collectibleCore' => $collectibleCore));
		}

	}
	?>
	<div class="collectible detail-wrapper">
		<div class="collectible detail">
			<div class="detail title">
				<h3><?php echo __('Collectible Details'); ?></h3>
				<?php
				if (isset($showEdit) && $showEdit) {
					echo '<div class="actions icon">';
					echo '<ul>';
					echo '<li>';
					if ($adminMode) {
						echo '<a href="' . $editManufactureUrl . $collectibleCore['Collectible']['id'] . '/true' . '"> <i class="icon-pencil icon-large"></i></a>';
					} else {
						echo '<a href="' . $editManufactureUrl . $collectibleCore['Collectible']['id'] . '/' . '"> <i class="icon-pencil icon-large"></i></a>';
					}
					echo '</li>';
					echo '</ul>';
					echo '</div>';
				}
				?>
			</div>
			<dl>
				<?php
				if (isset($showAddedBy) && $showAddedBy) {
					echo '<dt>';
					echo __('Added By');
					echo '</dt>';
					echo '<dd>';
					echo $collectibleCore['User']['username'];
					echo '</dd>';
				}
				?>
				<?php
				if (isset($showAddedDate) && $showAddedDate) {
					echo '<dt>';
					echo __('Date Added');
					echo '</dt>';
					echo '<dd>';
					$datetime = strtotime($collectibleCore['Collectible']['created']);
					$mysqldate = date("m/d/y g:i A", $datetime);
					echo $mysqldate;
					echo '</dd>';
				}
				?>
				<dt>
					<?php echo __('Manufacturer'); ?>
				</dt>
				<?php
				if ($showCompareFields && isset($collectibleCore['Collectible']['manufacture_id_changed']) && $collectibleCore['Collectible']['manufacture_id_changed']) {
					echo '<dd class="changed">';
				} else {
					echo '<dd>';
				}
				?>
				<a href="<?php echo '/manufactures/view/' . $collectibleCore['Manufacture']['id']; ?>"> <?php echo $collectibleCore['Manufacture']['title']; ?></a>
				</dd> <?php
				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'seriesPath'), __('Category', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'License', 'Field' => 'name'), __('Brand', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectibletype', 'Field' => 'name'), __('Type', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'SpecializedType', 'Field' => 'name'), __('Manufacturer Type', true), array('compare' => $showCompareFields));

				if (isset($collectibleCore['Collectible']['exclusive']) && $collectibleCore['Collectible']['exclusive']) {
					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'name'), __('Name', true), array('compare' => $showCompareFields, 'postValue' => __(' - Exclusive', true)));
				} else {
					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'name'), __('Name', true), array('compare' => $showCompareFields));
				}

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'variant'), __('Variant', true), array('compare' => $showCompareFields, 'value' => __('Yes', true)));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Scale', 'Field' => 'scale'), __('Scale', true), array('compare' => $showCompareFields));

				if (isset($collectibleCore['Collectible']['release']) && $collectibleCore['Collectible']['release'] !== '0000') {
					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'release'), __('Release Year', true), array('compare' => $showCompareFields));
				}

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'description'), __('Description', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'code'), __('Product code', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'upc'), __('Product UPC', true), array('compare' => $showCompareFields));

				//if (strstr($collectibleCore['Collectible']['msrp'], '$') === false) {
				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'msrp'), __('Original Retail Price', true), array('compare' => $showCompareFields, 'preValue' => $collectibleCore['Currency']['sign']));
				//} else {
				//echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'msrp'), __('Original Retail Price', true), array('compare' => $showCompareFields));
				//}

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'limited'), __('Limited Edition', true), array('compare' => $showCompareFields, 'value' => __('Yes', true)));

				if (isset($collectibleCore['Collectible']['retailer'])) {
					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'retailer'), __('Venue / Exclusive Retailer', true), array('compare' => $showCompareFields));
				} else {
					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Retailer', 'Field' => 'name'), __('Venue / Exclusive Retailer', true), array('compare' => $showCompareFields));
				}

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'edition_size'), __('Edition Size', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'numbered'), __('Numbered', true), array('compare' => $showCompareFields, 'value' => __('Yes', true)));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'pieces'), __('Number of Pieces', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_weight'), __('Weight', true), array('compare' => $showCompareFields, 'postValue' => ' lbs'));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_length'), __('Height', true), array('compare' => $showCompareFields, 'postValue' => '"'));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_width'), __('Width', true), array('compare' => $showCompareFields, 'postValue' => '"'));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_depth'), __('Depth', true), array('compare' => $showCompareFields, 'postValue' => '"'));
				?>
			</dl>
		</div>
		<?php ?>
	</div>
	
		<?php
				if (isset($showTags) && $showTags === true) {
			echo $this -> element('collectible_detail_tags', array('collectibleCore' => $collectibleCore, 'showEdit' => $showEdit, 'adminMode' => $adminMode));
		}
		
		if ($showAttributes) {
			echo $this -> element('collectible_detail_attributes', array('collectibleCore' => $collectibleCore, 'showEdit' => $showEdit, 'adminMode' => $adminMode));?>
			<script>
				$(function() {
					// If we are in admin mode, we need to pass that in to these methods so that they can 
					// do specific things based on that
					
					// We also need to update the attributes element so that if we are in admin mode and they are new attributes they
					// can be edited and removed..if they are not new then they cannot be edited automatically from here
					$('.standard-list.attributes.index').children('ul').children('li').children('div.attribute-collectibles').children('a').on('click', function() {
						$(this).parent().parent().children('.collectibles').toggle();
					});

					//$('.standard-list.attributes').attributes();
					var removeAttributes = new RemoveAttributes({
						<?php if($adminMode) {echo 'adminPage : true,';}?>
						$element : $('.standard-list.attributes')
					});

					removeAttributes.init();

					var removeAttributeLinks = new RemoveAttributeLinks({
						<?php if($adminMode) {echo 'adminPage : true,';}?>
						$element : $('.standard-list.attributes')
					});

					removeAttributeLinks.init();

					var addExistingCollectiblesAttributes = new AddExistingCollectibleAttributes({
						<?php if($adminMode) {echo 'adminPage : true,';}?>
						$element : $('.standard-list.attributes')
					});

					addExistingCollectiblesAttributes.init();

					var updateAttributes = new UpdateAttributes({
						<?php if($adminMode) {echo 'adminPage : true,';}?>
						$element : $('.standard-list.attributes')
					});

					updateAttributes.init();

					var updateCollectiblesAttributes = new UpdateCollectibleAttributes({
						<?php if($adminMode) {echo 'adminPage : true,';}?>
						$element : $('.standard-list.attributes')
					});

					updateCollectiblesAttributes.init();

					var addCollectiblesAttributes = new AddCollectibleAttributes({
						<?php if($adminMode) {echo 'adminPage : true,';}?>
						$element : $('.standard-list.attributes')
					});

					addCollectiblesAttributes.init();
					
					$('.standard-list.attributes > table > tbody> tr > td.popup').popover({
						placement : 'bottom',
						html : 'true',
						trigger : 'click'
					});

				}); 
</script>

<?php echo $this -> element('attribute_remove_dialog'); ?>
<?php echo $this -> element('attribute_update_dialog'); ?>
<?php echo $this -> element('attribute_remove_link_dialog'); ?>
<?php echo $this -> element('attribute_collectible_add_dialog'); ?>
<?php echo $this -> element('attribute_collectible_update_dialog'); ?>
<?php echo $this -> element('attribute_collectible_add_existing_dialog'); ?>
			
		<?php } ?>
</div>