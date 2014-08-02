<div class="col-md-12">
	

<h2><?php echo __('User Gallery', true)?></h2>

	<?php
	if (isset($userUploads) && !empty($userUploads)) {
		echo '<div id="titles-nav" class="hidden">';
		echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
		echo '</div>';
		echo '<div class="tiles boxed-tiles" data-toggle="modal-gallery" data-target="#modal-gallery">';

		foreach ($userUploads as $key => $upload) {
			if (!empty($upload['UserUpload'])) {
				echo '<div class="tile">';
				echo '<div class="image">';
				$this -> FileUpload -> reset();
				echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['UserUpload']['name'], array('imagePathOnly' => true, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '">';
				$this -> FileUpload -> reset();
				echo $this -> FileUpload -> image($upload['UserUpload']['name'], array('resizeType' => 'adaptive', 'imagePathOnly' => false, 'width' => 400, 'height' => 400, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '</a>';
				echo '</div>';
				echo '<div class="header">';
				echo '<h2>';
				echo '<a class="title" href="/profile/' . $upload['User']['username'] . '/photos">' . $upload['User']['username'] . '</a>';
				echo '</h2>';				
				echo '</div>';
				echo '</div>';
			}

		}
		echo '</div>';
	}
	?>


</div>
<?php echo $this -> Minify -> script('jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('jquery.masonry.min', array('inline' => false)); ?>
<script>
	$(function() {
		var $container = $('div.tiles');
		$container.imagesLoaded(function() {
			$container.masonry({
				itemSelector : '.tile',
			});
		});

		$container.infinitescroll({
			nextSelector : "#titles-nav a",
			navSelector : "#titles-nav",
			itemSelector : ".tile",
			loading : {
				finishedMsg : "All photos have been loaded!",
				msgText : "<em>Loading the next set of photos.</em>",
			}
		}, function(newElements) {
			// hide new items while they are loading
			var $newElems = $(newElements).css({
				opacity : 0
			});
			// ensure that images load before adding to masonry layout
			$newElems.imagesLoaded(function() {
				// show elems now they're ready
				$newElems.animate({
					opacity : 1
				});
				$container.masonry('appended', $newElems, true);
			});
		});
	}); 
</script>