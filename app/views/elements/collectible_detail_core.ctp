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
?>

<div class="collectible item">
	<?php
if($showImage) {
	?>
	<div class="collectible image">
		<?php
if (!empty($collectibleCore['Upload'])) {
		?>
		<?php echo $fileUpload -> image($collectibleCore['Upload'][0]['name'], array('width' => '100'));?>
		<div class="collectible image-fullsize hidden">
			<?php echo $fileUpload -> image($collectibleCore['Upload'][0]['name'], array('width' => 0));?>
		</div>
		<?php } else {?><img src="/img/silhouette_thumb.png"/>
		<?php }?>
		<?php
		if (isset($showEdit) && $showEdit) {
			echo '<div class="image link">';
			if (!empty($collectibleCore['Upload'])) {
				echo '<a href="' . $editImageUrl . $collectibleCore['Collectible']['id'] . '/' . $collectibleCore['Upload'][0]['id'] . '">' . __('Edit', true) . '</a>';
			} else {
				echo '<a href="' . $editImageUrl . $collectibleCore['Collectible']['id'] . '/' . '">' . __('Edit', true) . '</a>';
			}
			echo '</div>';
		}
		?>
	</div>
	<?php }?>
	<div class="collectible detail-wrapper">
		<div class="collectible detail">
			<div class="detail title">
				<h3><?php __('Manufacture Details');?></h3>
				<?php
				if (isset($showEdit) && $showEdit) {
					echo '<div class="title link">';
					echo '<a href="' . $editManufactureUrl . $collectibleCore['Collectible']['id'] . '/' . '">Edit</a>';
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

					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Retailer', 'Field' => 'name'), __('Exclusive Retailer', true),array('compare' => $showCompareFields));

					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'edition_size'), __('Edition Size', true), array('compare' => $showCompareFields));

					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_weight'), __('Weight', true), array('compare' => $showCompareFields, 'postValue' => ' lbs'));

					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_length'), __('Length', true), array('compare' => $showCompareFields, 'postValue' => '"'));

					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_width'), __('Width', true), array('compare' => $showCompareFields, 'postValue' => '"'));

					echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_depth'), __('Depth', true), array('compare' => $showCompareFields, 'postValue' => '"'));
				?>
			</dl>
		</div>
		<?php

if($showAttributes) {
		?>
		<div class="collectible detail">
			<div class="detail title">
				<h3><?php __('Accessories/Features');?></h3>
				<?php
				if (isset($showEdit) && $showEdit) {
					echo '<div class="title link">';
					echo '<a href="/attributes_collectibles_edits/edit/' . $collectibleCore['Collectible']['id'] . '/' . '">Edit</a>';
					echo '</div>';
				}
				?>
			</div>
			<?php
			$lastKey = 0;
			$attributeEmpty = empty($collectibleCore['AttributesCollectible']);
			if ($attributeEmpty) {
				echo '<div class="attributes-list empty">';
				echo '<ul>';
				echo '<li>No Accessories/Features have been added</li>';
				echo '</ul>';
				echo '</div>';
			} else {
				$outputAttribtes = '';
				$added = false;
				foreach ($collectibleCore['AttributesCollectible'] as $key => $attribute) {
					if ($attribute['variant'] !== '1') {
						$outputAttribtes .= '<li>' . '<span class="attribute-name">' . $attribute['Attribute']['name'] . '</span>' . '<span class="attribute-description">' . $attribute['description'] . '</span>' . '</li>';
						$added = true;
					}
				}

				if ($added) {
					echo '<div class="attributes-list">';
					echo '<ul>';
					echo '<li class="title">';
					echo '<span class="attribute-name">' . __('Part', true) . '</span>';
					echo '<span class="attribute-description">' . __('Description', true) . '</span>';
					echo '</li>';
					echo $outputAttribtes;
					echo '</ul>';
					echo '</div>';
				} else {
					echo '<div class="attributes-list empty">';
					echo '<ul>';
					echo '<li>No Accessories/Features have been added</li>';
					echo '</ul>';
					echo '</div>';
				}
			}
			?>
		</div>
		<?php if(isset($collectibleCore['Collectible']['variant']) && $collectibleCore['Collectible']['variant']) {
		?>
		<div class="collectible detail">
			<div class="detail title">
				<h3><?php __('Variant Accessories/Features');?></h3>
				<?php
				if (isset($showEdit) && $showEdit) {
					echo '<div class="title link">';
					echo '<a href="/collectibles/wizard/attributes">Edit</a>';
					echo '</div>';
				}
				?>
			</div>
			<?php
			$lastKey = 0;
			$attributeEmpty = empty($collectibleCore['AttributesCollectible']);
			if ($attributeEmpty) {
				echo '<div class="attributes-list empty">';
				echo '<ul>';
				echo '<li>No Accessories/Features have been added</li>';
				echo '</ul>';
				echo '</div>';
			} else {
				$outputAttribtes = '';
				$added = false;
				foreach ($collectibleCore['AttributesCollectible'] as $key => $attribute) {
					if ($attribute['variant'] === '1') {
						$outputAttribtes .= '<li>' . '<span class="attribute-name">' . $attribute['Attribute']['name'] . '</span>' . '<span class="attribute-description">' . $attribute['description'] . '</span>' . '</li>';
						$added = true;
					}
				}

				if ($added) {
					echo '<div class="attributes-list">';
					echo '<ul>';
					echo '<li class="title">';
					echo '<span class="attribute-name">' . __('Part', true) . '</span>';
					echo '<span class="attribute-description">' . __('Description', true) . '</span>';
					echo '</li>';
					echo $outputAttribtes;
					echo '</ul>';
					echo '</div>';
				} else {
					echo '<div class="attributes-list empty">';
					echo '<ul>';
					echo '<li>No Accessories/Features have been added</li>';
					echo '</ul>';
					echo '</div>';
				}
			}
			?>
		</div>
		<?php }
			if(isset($showTags) && $showTags === true) {
		?>

		<div class="collectible detail">
			<div class="detail title">
				<h3><?php __('Tags');?></h3>
			</div>
			<ul class="tag-list">
				<?php
				foreach ($collectibleCore['CollectiblesTag'] as $tag) {
					echo '<li class="tag">';
					echo $tag['Tag']['tag'];
					echo '</li>';
				}
				?>
			</ul>
		</div>
		<?php }

			}//show attribute end
		?>
		<?php
if(isset($showStatistics) && $showStatistics) {
		?>
		<div class="collectible detail statistics">
			<div class="detail title">
				<h3><?php __('Collectible Statistics');?></h3>
			</div>
			<dl>
				<dt>
					<?php __('Total owned: ');?>
				</dt>
				<dd>
					<?php echo $collectibleCount;?>
				</dd>
			</dl>
		</div>
		<?php }?>
	</div>
</div>