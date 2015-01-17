<div id="gallery" data-toggle="modal-gallery" data-target="#modal-gallery">
	<div class="row">
		<div class="col-md-12">
			<?php
			if (!empty($collectibleCore['CollectiblesUpload'])) {
				foreach ($collectibleCore['CollectiblesUpload'] as $key => $upload) {
					if ($upload['primary']) {
						$this -> set('og_image_url', 'http://' . env('SERVER_NAME') . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true)));
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
	<?php
if (!empty($collectibleCore['CollectiblesUpload']) && count($collectibleCore['CollectiblesUpload']) > 1) {
	?>
	<div class="row spacer">
		<div class="col-md-12">
			<div id="carousel-example-generic" class="carousel slide">
				<!-- Indicators
				<ol class="carousel-indicators">
					<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
					<li data-target="#carousel-example-generic" data-slide-to="1"></li>
					<li data-target="#carousel-example-generic" data-slide-to="2"></li>
				</ol> -->

				<!-- Wrapper for slides -->
				<div class="carousel-inner">
					<?php
					$i = 1;
					foreach ($collectibleCore['CollectiblesUpload'] as $key => $upload) {
						if (!$upload['primary']) {
							if ($i % 3 == 1) {
								echo '<div class="item ';
								if ($i === 1) {
									echo 'active';
								}

								echo '"><div class="row">';
							}

							echo '<div class="col-sm-4"><a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $collectibleCore['Collectible']['descriptionTitle'], 'imagePathOnly' => false)) . '</a></div>';

							if ($i % 3 == 0) {
								echo '</div></div>';
							}
							$i++;
						}
					}
					if ($i % 3 != 1) {
						echo '</div></div>';
					}
					?>
				</div>
					<?php
				if (count($collectibleCore['CollectiblesUpload']) > 4) {
					?>
				<!-- Controls -->
				<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
				<a class="right carousel-control" href="#carousel-example-generic" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></a>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
	<?php
	if (!empty($userUploads)) {
	?>
	<div class="row spacer">
		<div class="col-md-12">
			<h4>User photos</h4>
			<div id="carousel-example-generic" class="carousel slide">
				<!-- Indicators
				<ol class="carousel-indicators">
					<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
					<li data-target="#carousel-example-generic" data-slide-to="1"></li>
					<li data-target="#carousel-example-generic" data-slide-to="2"></li>
				</ol> -->

				<!-- Wrapper for slides -->
				<div class="carousel-inner">
					<?php
					$i = 1;
					foreach ($userUploads as $key => $upload) {
							if ($i % 3 == 1) {
								echo '<div class="item ';
								if ($i === 1) {
									echo 'active';
								}

								echo '"><div class="row">';
							}

							echo '<div class="col-sm-4"><a class="thumbnail" data-gallery="gallery" href="' . $upload['imagePath']. '"><img src="' . $upload['imagePath'] . '"></a></div>';

							if ($i % 3 == 0) {
								echo '</div></div>';
							}
							$i++;
					}
					if ($i % 3 != 1) {
						echo '</div></div>';
					}
					?>
				</div>
					<?php
				if (count($userUploads) > 4) {
					?>
				<!-- Controls -->
				<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
				<a class="right carousel-control" href="#carousel-example-generic" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></a>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
