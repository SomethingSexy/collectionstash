<div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
	<div class="row">
		<div class="col-md-12">
			<?php
			if (!empty($collectibleCore['CollectiblesUpload'])) {
				foreach ($collectibleCore['CollectiblesUpload'] as $key => $upload) {
					if ($upload['primary']) {
						echo '<a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $collectibleCore['Collectible']['descriptionTitle'], 'imagePathOnly' => false)) . '</a>';
						break;
					}
				}
			} else {
				echo '<a class="thumbnail"><img alt="" src="/img/no-photo.png"></a>';
			}
			?>
		</div>
	</div>
	<div class="row spacer">

		<?php
		if (!empty($collectibleCore['CollectiblesUpload'])) {
			foreach ($collectibleCore['CollectiblesUpload'] as $key => $upload) {
				if (!$upload['primary']) {
					echo '<div class="col-sm-6 col-md-3"><a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $collectibleCore['Collectible']['descriptionTitle'], 'imagePathOnly' => false)) . '</a></div>';
				}
			}
		}
		?>
	</div>

</div>
