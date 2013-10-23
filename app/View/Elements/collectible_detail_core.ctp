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
<?php
	$description = $collectibleCore['Collectible']['description'];
	$description = str_replace('\n', "\n", $description);
	$description = str_replace('\r', "\r", $description);
	$description = nl2br($description);
	$description = html_entity_decode($description);
	echo '<p class="lead">' . $description .'</p>';
?>
<h4>Collectible Details</h4>
<dl class="dl-horizontal">
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
	?>

	<?php

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'seriesPath'), __('Category', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'SpecializedType', 'Field' => 'name'), __('Manufacturer Platform', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Scale', 'Field' => 'scale'), __('Scale', true), array('compare' => $showCompareFields));

	if (isset($collectibleCore['Collectible']['release']) && $collectibleCore['Collectible']['release'] !== '0000') {
		$yearLabel = __('Release Year', true);
		if ($collectibleCore['Collectible']['custom'] || $collectibleCore['Collectible']['original']) {
			$yearLabel = __('Year Made', true);
		}

		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'release'), $yearLabel, array('compare' => $showCompareFields));
	}

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'code'), __('Product code', true), array('compare' => $showCompareFields));

	echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'upc'), __('Product UPC', true), array('compare' => $showCompareFields));
	if ($adminMode || $isLoggedIn || $collectibleCore['Collectible']['custom'] || $collectibleCore['Collectible']['original'] || $collectibleCore['Collectible']['official']) {

		$msrpLabel = __('Original Retail Price', true);
		if ($collectibleCore['Collectible']['custom'] || $collectibleCore['Collectible']['original']) {
			$msrpLabel = __('Cost', true);
		}

		echo $this -> CollectibleDetail -> field($collectibleCore, array('Model' => 'Collectible', 'Field' => 'msrp'), $msrpLabel, array('compare' => $showCompareFields, 'preValue' => $collectibleCore['Currency']['sign']));
	}
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

