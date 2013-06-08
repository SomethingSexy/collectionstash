<?php
if (!isset($showThumbnail)) {
	$showThumbnail = true;
}

echo '<table class="table stashable" data-toggle="modal-gallery" data-target="#modal-gallery">';
echo '<thead>';
echo '<tr>';
echo '<th>' . $this -> Paginator -> sort('active', 'Bought/Sold') . '</th>';
if ($showThumbnail) {
	echo '<th></th>';
}
echo '<th>' . $this -> Paginator -> sort('Collectible.name', 'Name') . '</th>';
echo '<th>' . $this -> Paginator -> sort('Collectible.manufacture_id', 'Manufacturer') . '</th>';
if ($stashType === 'default') {
	echo '<th>' . $this -> Paginator -> sort('edition_size', 'Edition Size') . '</th>';
	echo '<th>' . $this -> Paginator -> sort('cost', 'Price Paid') . '</th>';
	echo '<th>' . $this -> Paginator -> sort('purchased', 'Date Purchased') . '</th>';
}
echo '<th>' . $this -> Paginator -> sort('created', 'Date Added') . '</th>';
if (isset($myStash) && $myStash) {
	echo '<th>' . __('Actions') . '</th>';
}
echo '</tr>';

echo '</thead>';
foreach ($collectibles as $key => $myCollectible) {
	echo '<tr class="stash-item">';
	if ($myCollectible['CollectiblesUser']['active']) {
		echo '<td><i class="icon-plus"></i></td>';
	} else {
		echo '<td><i class="icon-minus"></i></td>';
	}

	if ($showThumbnail) {
		echo '<td><ul class="thumbnails"><li class="span1">';

		if (!empty($myCollectible['Collectible']['CollectiblesUpload'])) {
			foreach ($myCollectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
				if ($upload['primary']) {
					echo '<a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'uploadDir' => 'files')) . '</a>';
					break;
				}
			}

		} else {
			echo '<a class="thumbnail"><img alt="" src="/img/no-photo.png"></a>';
		}

		echo '</li></ul></td>';
	}
	echo '<td>' . $myCollectible['Collectible']['name'] . '</td>';
	if (!empty($myCollectible['Collectible']['Manufacture']['title'])) {
		echo '<td>' . $myCollectible['Collectible']['Manufacture']['title'] . '</td>';
	} else {
		echo '<td>N/A</td>';
	}

	if ($stashType === 'default') {
		if (empty($myCollectible['Collectible']['edition_size'])) {
			echo '<td> - </td>';
		} else if (empty($myCollectible['CollectiblesUser']['edition_size'])) {
			echo '<td>' . __('Not Recorded') . '</td>';
		} else {
			echo '<td>' . $myCollectible['CollectiblesUser']['edition_size'] . '/' . $myCollectible['Collectible']['edition_size'] . '</td>';
		}

		if (!empty($myCollectible['CollectiblesUser']['cost'])) {
			echo '<td>' . $myCollectible['CollectiblesUser']['cost'] . '</td>';
		} else {
			echo '<td>' . __('Not Recorded') . '</td>';
		}

		if (!empty($myCollectible['CollectiblesUser']['purchase_date'])) {
			echo '<td>' . $this -> Time -> format('F jS, Y', $myCollectible['CollectiblesUser']['purchase_date'], null) . '</td>';
		} else {
			echo '<td>' . __('Not Recorded') . '</td>';
		}
	}
	echo '<td>' . $this -> Time -> format('F jS, Y h:i A', $myCollectible['CollectiblesUser']['created'], null) . '</td>';

	if (isset($myStash) && $myStash) {
		echo '<td>';
		echo '<div class="btn-group">';
		echo '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>';
		echo '<ul class="dropdown-menu">';

		if ($stashType === 'default') {
			echo '<li><a href="/collectibles_users/edit/' . $myCollectible['CollectiblesUser']['id'] . '" title=' . __('Edit') . '>Edit</a></li>';
		}

		if ($stashType === 'default') {
			$prompt = 'data-prompt="true"';
		} else {
			$prompt = 'data-prompt="false"';
		}

		$collectibleJSON = json_encode($myCollectible['Collectible']);
		$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));

		$collectibleUserJSON = json_encode($myCollectible['CollectiblesUser']);
		$collectibleUserJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleUserJSON));

		echo '<li><a ' . $prompt . ' data-stash-type="' . $stashType . '" data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $myCollectible['CollectiblesUser']['id'] . '" class="remove-from-stash" title="Remove" href="#">Remove</a></li>';
		echo '</ul>';
		echo '</div>';
		echo '</td>';
	}

	echo '</tr>';
}
echo '</table>';
echo '<div class="paging">';
echo '<p>';
echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
echo '</p>';

$urlparams = $this -> request -> query;
unset($urlparams['url']);
$this -> Paginator -> options(array('url' => $this -> passedArgs));

echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));
echo $this -> Paginator -> numbers(array('separator' => false));
echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
echo '</div>';
?>