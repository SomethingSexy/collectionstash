<div class="collectible image">
	<div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
		<div>
		<?php
		if (!empty($collectibleCore['CollectiblesUpload'])) {
			foreach ($collectibleCore['CollectiblesUpload'] as $key => $upload) {
				if ($upload['primary']) {
					echo '<a rel="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'width' => 400, 'height' => 400)) . '</a>';
					break;
				}
			}
		} else {
			echo '<img src="/img/silhouette_thumb.png"/>';
		}
		?>
		</div>
		<?php
		if (!empty($collectibleCore['CollectiblesUpload'])) {
			foreach ($collectibleCore['CollectiblesUpload'] as $key => $upload) {
				if (!$upload['primary']) {
					echo '<a rel="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'width' => 80, 'height' => 120)) . '</a>';
				}
			}
		}
		?>
	</div>
	<?php
	if (isset($showEdit) && $showEdit) {
		echo '<div class="image link">';
		echo '<a id="upload-link" href="#">' . __('Edit', true) . '</a>';
		echo '</div>';

		echo $this -> element('upload_dialog', array('uploadName' => 'data[CollectiblesUpload][collectible_id]', 'uploadId' => $collectibleCore['Collectible']['id']));
	}
	?>
</div>