<div class="collectible image">
	<?php
	if (!empty($collectible['CollectiblesUpload'])) {
		foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
			if($upload['primary']){
				echo '<a rel="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true,'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false,'width' => 150, 'height' => 150)) . '</a>';
				break;
			}
		}
		
	} else {
		echo '<img src="/img/silhouette_thumb.png"/>';
	}
	?>
</div>