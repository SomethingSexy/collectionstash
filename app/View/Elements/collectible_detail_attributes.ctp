<?php
if (!isset($adminMode)) {
	$adminMode = false;
}

$lastKey = 0;
$attributeEmpty = empty($collectibleCore['AttributesCollectible']);
?>

<div class="well">

	<h3><?php echo __('Parts and Accessories'); ?></h3>

	<?php
	// TO be able to handle editing both the attribute and the attributecollectible easier
	// I am going to put the JSON object as a data attribute on each row
	if ($attributeEmpty) {

		echo '<ul class="unstyled">';
		echo '<li>No Parts or Accessories have been added</li>';
		echo '</ul>';

	} else {
		$outputAttribtes = '';
		$added = false;
		foreach ($collectibleCore['AttributesCollectible'] as $key => $attribute) {
			// categoryId
			// categoryName
			// name
			// description
			// scaleId
			// id
			// manufacturerId
			$attributeJSON = '{';
			$attributeJSON .= '"categoryId" : "' . $attribute['Attribute']['AttributeCategory']['id'] . '",';
			$attributeJSON .= '"categoryName" : "' . $attribute['Attribute']['AttributeCategory']['path_name'] . '",';
			$attributeJSON .= '"name" : "' . $attribute['Attribute']['name'] . '",';
			$attributeJSON .= '"description" : "' . $attribute['Attribute']['description'] . '",';
			$attributeJSON .= '"scaleId" : ';
			if (isset($attribute['Attribute']['scale_id']) && !is_null($attribute['Attribute']['scale_id'])) {
				$attributeJSON .= '"' . $attribute['Attribute']['scale_id'] . '",';
			} else {
				$attributeJSON .= '"null" ,';
			}
			$attributeJSON .= '"manufacturerId" : "' . $attribute['Attribute']['manufacture_id'] . '",';
			$attributeJSON .= '"id" : "' . $attribute['Attribute']['id'] . '"';
			$attributeJSON .= '}';

			$attributeCollectibleJSON = '{';
			$attributeCollectibleJSON .= '"id" : "' . $attribute['id'] . '",';
			$attributeCollectibleJSON .= '"attributeId" : "' . $attribute['attribute_id'] . '",';
			$attributeCollectibleJSON .= '"categoryName" : "' . $attribute['Attribute']['AttributeCategory']['path_name'] . '",';
			$attributeCollectibleJSON .= '"count" : "' . $attribute['count'] . '"';
			$attributeCollectibleJSON .= '}';

			if (!empty($attribute['Attribute']['AttributesCollectible'])) {
				$popup = '<ul>';
				foreach ($attribute['Attribute']['AttributesCollectible'] as $key => $collectible) {
					if (!empty($collectible['Collectible']['name'])) {
						$popup .= '<li>';
						$popup .= "<a href='/collectibles/view/" . $collectible['Collectible']['id'] . "'>" . $collectible['Collectible']['name'] . "</a>";
						$popup .= '</li>';
					}

				}
				$popup .= '</ul>';
			} else {
				$popup = "<ul class='unstyled'>";
				$popup .= '<li>' . __('Not attached to any collectibles') . '</li>';
				$popup .= '</ul>';
			}

			$outputAttribtes .= '<tr data-attribute=\'' . $attributeJSON . '\' data-attribute-collectible=\'' . $attributeCollectibleJSON . '\' data-id="' . $attribute['Attribute']['id'] . '"  data-attached="true" data-attribute-collectible-id="' . $attribute['id'] . '">';
			// That means this is a new one, so we don't need the info icon
			if ($attribute['Attribute']['status_id'] === '2') {
				$outputAttribtes .= '<td><i class="icon-plus"></i></td>';
			} else {
				$outputAttribtes .= '<td><span title="' . __('Part Information') . '" data-content="' . $popup . '" class="popup"><i class="icon-info-sign"></i></span></td>';
				;
			}

			$outputAttribtes .= '<td><ul class="thumbnails"><li class="span1">';

			if (!empty($attribute['Attribute']['AttributesUpload'])) {
				foreach ($attribute['Attribute']['AttributesUpload'] as $key => $upload) {
					if ($upload['primary']) {
						$outputAttribtes .= '<a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false)) . '</a>';
						break;
					}
				}
			} else {
				$outputAttribtes .= '<a class="thumbnail"><img alt="" src="/img/no-photo.png"></a>';
			}

			$outputAttribtes .= '</li></ul></td>';
			$outputAttribtes .= '<td class="category">';

			$outputAttribtes .= $attribute['Attribute']['AttributeCategory']['path_name'] . '</td>';

			$outputAttribtes .= '<td>' . $attribute['Attribute']['name'] . '</td>';

			$outputAttribtes .= '<td>' . $attribute['Attribute']['description'] . '</td>';
			$outputAttribtes .= '<td>' . $attribute['Attribute']['Manufacture']['title'] . '</td>';

			if (isset($attribute['Attribute']['Scale']['scale'])) {
				$outputAttribtes .= '<td>' . $attribute['Attribute']['Scale']['scale'] . '</td>';
			} else {
				$outputAttribtes .= '<td> </td>';
			}

			$outputAttribtes .= '<td class="count">' . $attribute['count'] . '</td>';
			// Going to use the modified date and the last person on the revision who did something to it
			$outputAttribtes .= '<td class="user">' . $attribute['Revision']['User']['username'] . '</td>';
			$outputAttribtes .= '</tr>';
			$added = true;
		}

		if ($added) {
			echo '<div class="attributes collectible" data-collectible-id="' . $collectibleCore['Collectible']['id'] . '">';
			echo '<table class="table table-striped" data-toggle="modal-gallery" data-target="#modal-gallery">';
			echo '<thead><tr>';
			echo '<th></th>';
			echo '<th>Photo</td>';
			echo '<th class="category">' . __('Category') . '</th>';
			echo '<th>' . __('Name', true) . '</th>';
			echo '<th>' . __('Description', true) . '</th>';
			echo '<th>' . __('Manufacturer', true) . '</th>';
			echo '<th>' . __('Scale', true) . '</th>';
			echo '<th title="' . __('The amount of items of this type this collectible has.') . '" class="count">' . __('Count', true) . '</th>';
			echo '<th class="user" title="' . __('The user who performed the last action on this item.') . '">' . __('Added By') . '</th>';
			echo '</tr></thead>';
			echo '<tbody>';
			echo $outputAttribtes;
			echo '</tbody>';
			echo '</table>';
			echo '</div>';
		} else {
			echo '<div class="attributes-list empty" data-collectible-id="' . $collectibleCore['Collectible']['id'] . '">';
			echo '<ul>';
			echo '<li>No Accessories/Features have been added</li>';
			echo '</ul>';
			echo '</div>';
		}
	}
	?>
</div>