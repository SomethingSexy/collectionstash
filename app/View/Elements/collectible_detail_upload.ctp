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
	<?php 
		if (!empty($collectibleCore['CollectiblesUpload']) && count($collectibleCore['CollectiblesUpload']) > 1) {	?>
	<div class="row spacer">
		<div class="col-md-12">
		<div id="carousel-example-generic" class="carousel slide">
			<!-- Indicators -->
			<ol class="carousel-indicators">
				<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
				<li data-target="#carousel-example-generic" data-slide-to="1"></li>
				<li data-target="#carousel-example-generic" data-slide-to="2"></li>
			</ol>

			<!-- Wrapper for slides -->
			<div class="carousel-inner">
				<?php
				$i = 0;
				foreach ($collectibleCore['CollectiblesUpload'] as $key => $upload) {
					if (!$upload['primary']) {
						if ($i % 3 == 0) {
							echo '<div class="item active"><div class="row">';
						}

						echo '<div class="col-sm-4"><a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $collectibleCore['Collectible']['descriptionTitle'], 'imagePathOnly' => false)) . '</a></div>';

						if ($i % 3 == 0) {
							echo '</div></div>';
						}
					}
				}
				?>
			</div>

			<!-- Controls -->
			<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev"> <span class="icon-prev"></span> </a>
			<a class="right carousel-control" href="#carousel-example-generic" data-slide="next"> <span class="icon-next"></span> </a>
		</div>
		</div>
	</div>
	<script>
		$(function(){
			$('#carousel-example-generic').carousel({wrap: true});
		});
	</script>
	<?php } ?>
</div>
