<div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
	<ul id="upload-link" class="thumbnails">
		<li class="span12">
			<?php
			if (!empty($collectibleCore['CollectiblesUpload'])) {
				foreach ($collectibleCore['CollectiblesUpload'] as $key => $upload) {
					if ($upload['primary']) {
						echo '<a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false)) . '</a>';
						break;
					}
				}
			} else {
				echo '<a class="thumbnail"><img alt="" src="/img/no-photo.png"></a>';
			}
			?>
		</li>
		<?php
		if (!empty($collectibleCore['CollectiblesUpload'])) {
			foreach ($collectibleCore['CollectiblesUpload'] as $key => $upload) {
				if (!$upload['primary']) {
					echo '<li class="span2"><a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false)) . '</a></li>';
				}
			}
		}
		?>
	</ul>
</div>
