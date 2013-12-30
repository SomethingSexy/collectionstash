<?php
if (!isset($showThumbnail)) {
	$showThumbnail = true;
}
if (!isset($history)) {
	$history = false;
}

echo '<div class="table-responsive"><table class="table stashable" data-toggle="modal-gallery" data-target="#modal-gallery"';

if ($history) {
	echo 'data-history="true"';
}

echo '>';
echo '<thead>';
echo '<tr>';
if ($history) {
	echo '<th>' . $this -> Paginator -> sort('active', 'Bought/Sold') . '</th>';
}

if ($showThumbnail) {
	echo '<th></th>';
}
echo '<th>' . $this -> Paginator -> sort('Collectible.name', 'Name') . '</th>';
echo '<th>' . $this -> Paginator -> sort('Collectible.manufacture_id', 'Manufacturer') . '</th>';
if ($stashType === 'default') {
	echo '<th>' . $this -> Paginator -> sort('edition_size', 'Edition Size') . '</th>';
	echo '<th>' . $this -> Paginator -> sort('cost', 'Price Paid') . '</th>';
	echo '<th>' . $this -> Paginator -> sort('Collectible.average_price', 'Collection Stash Value') . '</th>';
	echo '<th>' . $this -> Paginator -> sort('purchased', 'Date Purchased') . '</th>';
	if ($history) {
		echo '<th>' . __('Sold For') . '</th>';
		echo '<th>' . __('Date Sold') . '</th>';
	}
}
echo '<th>' . $this -> Paginator -> sort('created', 'Date Added') . '</th>';
if (isset($myStash) && $myStash) {
	echo '<th>' . __('Actions') . '</th>';
}
echo '</tr>';

echo '</thead>';
foreach ($collectibles as $key => $myCollectible) {
	echo '<tr class="stash-item">';
	if ($history) {
		if ($myCollectible['CollectiblesUser']['active']) {
			echo '<td class="bought-sold-icon"><i class="icon-plus"></i></td>';
		} else {
			echo '<td><i class="icon-minus"></i></td>';
		}
	}

	if ($showThumbnail) {
		echo '<td style="min-width: 100px; max-width: 100px;">';

		if (!empty($myCollectible['Collectible']['CollectiblesUpload'])) {
			foreach ($myCollectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
				if ($upload['primary']) {
					echo '<a class="thumbnail col-md-6" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $myCollectible['Collectible']['descriptionTitle'], 'imagePathOnly' => false, 'uploadDir' => 'files')) . '</a>';
					break;
				}
			}

		} else {
			echo '<a class="thumbnail"><img alt="" src="/img/no-photo.png"></a>';
		}

		echo '</td>';
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

		if (isset($myCollectible['Collectible']['CollectiblePriceFact'])) {
			echo '<td>' . $myCollectible['Collectible']['CollectiblePriceFact']['average_price'] . '</td>';
		} else {
			echo '<td> - </td>';
		}

		if (!empty($myCollectible['CollectiblesUser']['purchase_date'])) {
			echo '<td>' . $this -> Time -> format('F jS, Y', $myCollectible['CollectiblesUser']['purchase_date'], null) . '</td>';
		} else {
			echo '<td>' . __('Not Recorded') . '</td>';
		}

		if ($history) {
			if ($myCollectible['CollectiblesUser']['active']) {
				echo '<td> - </td>';
				echo '<td> - </td>';
			} else if (isset($myCollectible['Listing'])) {
				echo '<td>' . $myCollectible['Listing']['Transaction'][0]['sale_price'] . '</td>';
				echo '<td>' . $this -> Time -> format('F jS, Y', $myCollectible['Listing']['Transaction'][0]['sale_date'], null) . '</td>';
			} else {
				echo '<td> - </td>';
				echo '<td> - </td>';
			}
		}

	}

	echo '<td>' . $this -> Time -> format('F jS, Y h:i A', $myCollectible['CollectiblesUser']['created'], null) . '</td>';

	if (isset($myStash) && $myStash) {
		echo '<td>';
		echo '<div class="btn-group">';
		echo '<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>';
		echo '<ul class="dropdown-menu">';

		if ($stashType === 'default') {
			echo '<li><a href="/collectibles_users/edit/' . $myCollectible['CollectiblesUser']['id'] . '" title=' . __('Edit') . '><i class="icon-edit"></i>  Edit</a></li>';
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

		echo '<li><a ' . $prompt . ' data-stash-type="' . $stashType . '" data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $myCollectible['CollectiblesUser']['id'] . '" class="remove-from-stash" title="Remove" href="#"><i class="icon-trash"></i> Remove</a></li>';

		// these should already be filtered by active
		if ($myCollectible['CollectiblesUser']['sale']) {
			// this will bring up the remove from stash modal
			echo '<li><a href="stash-mark-as-sold" title=' . __('Mark as Sold') . '><i class="icon-dollar"></i>  ' . __('Mark as Sold') . '</a></li>';
			// this will remove the listing completely and mark it as unsold
			echo '<li><a href="stash-remove-listing" title=' . __('Remove Listing') . '><i class="icon-dollar"></i>  '. __('Remove Listing') . '</a></li>';
			//
			echo '<li><a href="stash-edit-listing" title=' . __('Edit Listing') . '><i class="icon-dollar"></i>  '. __('Edit Listing') . '</a></li>';
		} else {
			echo '<li><a href="" class="stash-sell" title=' . __('Sell') . '><i class="icon-dollar"></i>  ' . __('Sell') . '</a></li>';
		}

		echo '</ul>';
		echo '</div>';
		echo '</td>';
	}

	echo '</tr>';
}
echo '</table></div>';

echo '<div class="pagination-container">';
echo '<p>';
echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
echo '</p>';
$urlparams = $this -> request -> query;
unset($urlparams['url']);
$this -> Paginator -> options(array('url' => $this -> passedArgs));

echo '<ul class="pagination">';
echo $this -> Paginator -> prev(__('previous', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled'));
echo $this -> Paginator -> numbers(array('separator' => false, 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a'));
echo $this -> Paginator -> next(__('next', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled'));
echo '</ul>';
echo '</div>';
?>