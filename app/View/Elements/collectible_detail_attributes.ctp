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

			$outputAttribtes .= '<div class="row-fluid spacer"><div class="span12 attribute">';

			$outputAttribtes .= '<div class="row-fluid">';

			$outputAttribtes .= '<div class="span10">';

			// That means this is a new one, so we don't need the info icon
			if ($attribute['Attribute']['status_id'] === '2') {
				$outputAttribtes .= '<span><i class="icon-plus"></i></span><span class="path">' . $attribute['Attribute']['AttributeCategory']['path_name'] . '</span>';
			} else {
				$outputAttribtes .= '<span class="popup" data-trigger="manual" data-content="' . $popup . '" data-original-title="Part Information"><i class="icon-info-sign"></i></span><span class="path">' . $attribute['Attribute']['AttributeCategory']['path_name'] . '</span>';

			}

			$outputAttribtes .= '</div>';
			$outputAttribtes .= '<div class="span2 count">';
			if ($collectibleCore['Collectible']['custom']) {
				if ($attribute['attribute_collectible_type'] === 'added') {
					$outputAttribtes .= '<span class="label label-success">' . __('Owned') . '</span>';
				} else if ($attribute['attribute_collectible_type'] === 'wanted') {
					$outputAttribtes .= '<span class="label label-important">' . __('Wanted') . '</span>';
				} else if ($attribute['attribute_collectible_type'] === 'preorder') {
					$outputAttribtes .= '<span class="label label-warning">' . __('Preordered') . '</span>';
				}
			}

			$outputAttribtes .= '<span class="badge">' . $attribute['count'] . '</span>';
			$outputAttribtes .= '</div>';
			$outputAttribtes .= '</div>';

			$outputAttribtes .= '<div class="row-fluid">';
			$outputAttribtes .= '<div class="span1"><ul class="thumbnails"><li class="span12">';

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

			$outputAttribtes .= '</li></ul></div>';

			$outputAttribtes .= '<div class="span11">';

			$outputAttribtes .= '<div class="row-fluid"><div class="span12"><span class="name">' . $attribute['Attribute']['name'] . '</span></div></div>';
			if (isset($attribute['Attribute']['Scale']['scale'])) {
				$outputAttribtes .= '<div class="row-fluid"><div class="span12">' . $attribute['Attribute']['Scale']['scale'] . '</div></div>';
			}

			$outputAttribtes .= '<div class="row-fluid"><div class="span12">' . $attribute['Attribute']['description'] . '</div></div>';

			$outputAttribtes .= '<div class="row-fluid"><div class="span12">';

			if ($attribute['Attribute']['type'] === 'mass') {
				if (isset($attribute['Attribute']['artist_id']) || isset($attribute['Attribute']['manufacture_id'])) {
					if (isset($attribute['Attribute']['manufacture_id'])) {
						$outputAttribtes .= '<span class="label">' . $attribute['Attribute']['Manufacture']['title'] . '</span>';
					}
					if (isset($attribute['Attribute']['artist_id'])) {
						$outputAttribtes .= '<span class="label">' . $attribute['Attribute']['Artist']['name'] . '</span>';
					}
				} else {
					$outputAttribtes .= '<span class="label">Unknown</span>';
				}
			} else if ($attribute['Attribute']['type'] === 'custom') {
				$outputAttribtes .= '<span class="label">' . $attribute['Attribute']['User']['username'] . '</span>';
			} else if ($attribute['Attribute']['type'] === 'original') {
				if (isset($attribute['Attribute']['artist_id'])) {
					$outputAttribtes .= '<span class="label">' . $attribute['Attribute']['Artist']['name'] . '</span>';
				} else {
					$outputAttribtes .= '<span class="label">Unknown</span>';
				}
			} else {
				$outputAttribtes .= '<span class="label">Generic</span>';
			}

			$outputAttribtes .= '</div></div>';

			$outputAttribtes .= '</div>';
			// div class="span11"
			$outputAttribtes .= '</div>';
			// div class="row-fluid"
			$outputAttribtes .= '<div class="row-fluid">';
			$outputAttribtes .= '<div class="span12">';
			$outputAttribtes .= '<div class="pull-right">' . $attribute['Revision']['User']['username'] . ' - ' . $this -> Time -> format('F jS, Y h:i A', $attribute['Attribute']['modified'], null) . '</div>';
			$outputAttribtes .= '</div>';
			$outputAttribtes .= '</div>';
			$outputAttribtes .= '</div></div>';
			// main div class="span12"
			$added = true;
		}

		if ($added) {
			echo '<div class="row-fluid attributes-list" data-toggle="modal-gallery" data-target="#modal-gallery">';
			echo '<div class="span12">';
			echo $outputAttribtes;
			echo '</div>';
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