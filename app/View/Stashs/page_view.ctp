<?php

echo '<div class="glimpse" data-current="' . $this -> Paginator -> current() . '"';
if ($this -> Paginator -> hasPrev()) {
	echo 'data-hasprev="true"';
} else {
	echo 'data-hasprev="false"';
}
if ($this -> Paginator -> hasNext()) {
	echo 'data-hasnext="true"';
} else {
	echo 'data-hasnext="false"';
}

echo '>';
$count = 0;
$collectiblesCount = count($collectibles) - 1;
foreach ($collectibles as $key => $myCollectible) {
	$newline = false;
	$endline = false;
	//First chec to see if we are starting over
	if ($count === 0) {
		$newline = true;
		$count += $count + 1;
	} else {
		//If not first, check to see if we are on the 5th one or not
		if ($count % 4 != 0) {
			//if we are not, lets increase
			$count += $count + 1;
		} else {
			//if we are, then lets start over and make sure we close out the div.
			$count = 0;
			$endline = true;
		}
	}

	if (!$endline) {
		if (($collectiblesCount) === $key) {
			$endline = true;
		}
	}

	if ($newline) {
		echo '<div class="line">';
	}
	if (!empty($myCollectible['Collectible']['Upload'])) {
		echo '<div class="image">';
		echo '<a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '">' . $this -> FileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('uploadDir' => 'files', 'width' => 150, 'height' => 150)) . '</a>';
		echo '</div>';
		//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
	} else {
		echo '<div class="image"><a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '"><img src="/img/silhouette_thumb.png"/></a></div>';
	}
	if ($endline) {
		echo '</div>';
	}
}
echo '</div>';
echo '<div class="links">';
if ($this -> Paginator -> hasPrev()) {
	echo '<div id="previous" class="tn3e-prev" title="Previous Image"></div>';
}
if ($this -> Paginator -> hasNext()) {
	echo '<div id="next" class="tn3e-next" title="Next Image"></div>';
}
echo '</div>';
?>