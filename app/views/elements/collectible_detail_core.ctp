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
				<h3><?php __('Manufacture Details');?></h3>
				<?php
				if (isset($showEdit) && $showEdit) {
					echo '<div class="title link">';
					if ($adminMode) {
						echo '<a href="' . $editManufactureUrl . $collectibleCore['Collectible']['id'] . '/true' . '">Edit</a>';
					} else {
						echo '<a href="' . $editManufactureUrl . $collectibleCore['Collectible']['id'] . '/' . '">Edit</a>';
					}

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
					<?php __('Manufacture');?>
				</dt>
				<?php
				if ($showCompareFields && isset($collectibleCore['Collectible']['manufacture_id_changed']) && $collectibleCore['Collectible']['manufacture_id_changed']) {
					echo '<dd class="changed">';
				} else {
					echo '<dd>';
				}
				?>
				<a target="_blank" href="<?php echo $collectibleCore['Manufacture']['url'];?>"> <?php echo $collectibleCore['Manufacture']['title'];?></a>
				</dd> <?php
				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'License', 'Field' => 'name'), __('License', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectibletype', 'Field' => 'name'), __('Type', true), array('compare' => $showCompareFields));
				
				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'SpecializedType', 'Field' => 'name'), __('Manufacturer Type', true), array('compare' => $showCompareFields));

				if (isset($collectibleCore['Collectible']['exclusive']) && $collectibleCore['Collectible']['exclusive']) {
					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'name'), __('Name', true), array('compare' => $showCompareFields, 'postValue' => __(' - Exclusive', true)));
				} else {
					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'name'), __('Name', true), array('compare' => $showCompareFields));
				}

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'variant'), __('Variant', true), array('compare' => $showCompareFields, 'value' => __('Yes', true)));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Scale', 'Field' => 'name'), __('Scale', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'release'), __('Release Year', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'description'), __('Description', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'code'), __('Product code', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'upc'), __('Product UPC', true), array('compare' => $showCompareFields));

				if (strstr($collectibleCore['Collectible']['msrp'], '$') === false) {
					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'msrp'), __('Original Retail Price', true), array('compare' => $showCompareFields, 'preValue' => '$'));
				} else {
					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'msrp'), __('Original Retail Price', true), array('compare' => $showCompareFields));
				}

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'limited'), __('Limited Edition', true), array('compare' => $showCompareFields, 'value' => __('Yes', true)));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Retailer', 'Field' => 'name'), __('Exclusive Retailer', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'edition_size'), __('Edition Size', true), array('compare' => $showCompareFields));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_weight'), __('Weight', true), array('compare' => $showCompareFields, 'postValue' => ' lbs'));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_length'), __('Length', true), array('compare' => $showCompareFields, 'postValue' => '"'));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_width'), __('Width', true), array('compare' => $showCompareFields, 'postValue' => '"'));

				echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_depth'), __('Depth', true), array('compare' => $showCompareFields, 'postValue' => '"'));
				?>
			</dl>
		</div>
		<?php
		if ($showAttributes) {
			echo $this -> element('collectible_detail_attributes', array('collectibleCore' => $collectibleCore, 'showEdit' => $showEdit, 'adminMode' => $adminMode));
		}
		if (isset($showTags) && $showTags === true) {
			echo $this -> element('collectible_detail_tags', array('collectibleCore' => $collectibleCore));
		}
		?>
	</div>
</div>