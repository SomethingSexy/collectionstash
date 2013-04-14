<?php
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
<div class="well">	
<h3><?php echo __('Collectible Details'); ?></h3>
<dl class="">
	<?php
	if (isset($showAddedBy) && $showAddedBy) {
		echo '<dt>';
		echo __('Added By');
		echo '</dt>';
		echo '<dd>';
		if (!$collectibleCore['User']['admin']) {
			echo $this -> Html -> link($collectibleCore['User']['username'], array('admin' => false, 'controller' => 'stashs', 'action' => 'view', $collectibleCore['User']['username']));
		} else {
			echo $collectibleCore['User']['username'];
		}
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

	if (isset($collectibleCore['Collectible']['custom']) && $collectibleCore['Collectible']['custom']) {
		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'CustomStatus', 'Field' => 'status'), __('Status', true), array('compare' => $showCompareFields));
	} else {
		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Status', 'Field' => 'status'), __('Status', true), array('compare' => $showCompareFields));
	}

	if (isset($collectibleCore['Collectible']['exclusive']) && $collectibleCore['Collectible']['exclusive']) {
		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'name'), __('Name', true), array('compare' => $showCompareFields, 'postValue' => __(' - Exclusive', true)));
	} else {
		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'name'), __('Name', true), array('compare' => $showCompareFields));
	}
	?>
	
	
	 <?php
	if (!empty($collectibleCore['Collectible']['manufacture_id'])) {
		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Manufacture', 'Field' => 'title'), __('Manufacturer ', true), array('compare' => $showCompareFields, 'value' => '<a href="/manufacturer/' . $collectibleCore['Manufacture']['id'] . '/' . $collectibleCore['Manufacture']['slug'] . '">' . $collectibleCore['Manufacture']['title'] . '</a>'));
	}

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'seriesPath'), __('Category', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'License', 'Field' => 'name'), __('Brand', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectibletype', 'Field' => 'name'), __('Platform', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'SpecializedType', 'Field' => 'name'), __('Manufacturer Platform', true), array('compare' => $showCompareFields));

	if (isset($collectibleCore['Collectible']['variant']) && $collectibleCore['Collectible']['variant']) {
		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'variant'), __('Variant', true), array('compare' => $showCompareFields, 'value' => '<a href="/collectibles/view/' . $collectibleCore['Collectible']['variant_collectible_id'] . '">Yes</a>'));
	}
	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Scale', 'Field' => 'scale'), __('Scale', true), array('compare' => $showCompareFields));

	if (isset($collectibleCore['Collectible']['release']) && $collectibleCore['Collectible']['release'] !== '0000') {
		$yearLabel = __('Release Year', true);
		if ($collectibleCore['Collectible']['custom'] || $collectibleCore['Collectible']['original']) {
			$yearLabel = __('Year Made', true);
		}

		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'release'), $yearLabel, array('compare' => $showCompareFields));
	}

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'description'), __('Description', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'code'), __('Product code', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'upc'), __('Product UPC', true), array('compare' => $showCompareFields));

	$msrpLabel = __('Original Retail Price', true);
	if ($collectibleCore['Collectible']['custom'] || $collectibleCore['Collectible']['original']) {
		$msrpLabel = __('Cost', true);
	}

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'msrp'), $msrpLabel, array('compare' => $showCompareFields, 'preValue' => $collectibleCore['Currency']['sign']));

	if ($adminMode) {
		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'official'), __('Official', true), array('compare' => $showCompareFields, 'value' => __('Yes', true)));
	}

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'limited'), __('Limited Edition', true), array('compare' => $showCompareFields, 'value' => __('Yes', true)));

	if (isset($collectibleCore['Collectible']['retailer'])) {
		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'retailer'), __('Venue / Exclusive Retailer', true), array('compare' => $showCompareFields));
	} else {
		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Retailer', 'Field' => 'name'), __('Venue / Exclusive Retailer', true), array('compare' => $showCompareFields));
	}

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'edition_size'), __('Edition Size', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'numbered'), __('Numbered', true), array('compare' => $showCompareFields, 'value' => __('Yes', true)));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'signed'), __('Signed', true), array('compare' => $showCompareFields, 'value' => __('Yes', true)));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'pieces'), __('Number of Pieces', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_weight'), __('Weight', true), array('compare' => $showCompareFields, 'postValue' => ' lbs'));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_length'), __('Height', true), array('compare' => $showCompareFields, 'postValue' => '"'));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_width'), __('Width', true), array('compare' => $showCompareFields, 'postValue' => '"'));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'product_depth'), __('Depth', true), array('compare' => $showCompareFields, 'postValue' => '"'));
	?>
</dl>
</div>	

