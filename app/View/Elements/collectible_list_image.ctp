<div class="collectible image">
	<?php
	if (!empty($collectible['Upload'])) {
		echo '<a rel="gallery" href="' . $this -> FileUpload -> image($collectible['Upload'][0]['name'], array('imagePathOnly' => true,'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($collectible['Upload'][0]['name'], array('imagePathOnly' => false,'width' => 150, 'height' => 150)) . '</a>';
	} else {
		echo '<img src="/img/silhouette_thumb.png"/>';
	}
	?>
</div>