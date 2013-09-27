<div class="span12">
	

<h2><?php echo __('User Gallery', true)?></h2>

	<?php
	if (isset($userUploads) && !empty($userUploads)) {
		echo '<div id="titles-nav" class="hidden">';
		echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
		echo '</div>';
		echo '<div class="tiles" data-toggle="modal-gallery" data-target="#modal-gallery">';

		foreach ($userUploads as $key => $upload) {
			if (!empty($upload['UserUpload'])) {
				echo '<div class="tile">';
				echo '<div class="image">';
				echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['UserUpload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '">';
				$this -> FileUpload -> reset();
				echo $this -> FileUpload -> image($upload['UserUpload']['name'], array('resizeType' => 'adaptive', 'imagePathOnly' => false, 'width' => 400, 'height' => 400, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '</a>';
				echo '</div>';
				echo '<div class="meta">';
				echo '<div class="title">';
				echo '<a class="title" href="/user_uploads/view/' . $upload['User']['username'] . '">' . $upload['User']['username'] . '</a>';
				echo '</div>';				
				
				echo '</div>';
				// echo '<div class="header">';
				// echo '<a  href="/user_uploads/view/' . $upload['User']['username'] . '">' . $upload['User']['username'] . '</a>';
				// echo '</div>';
				// echo '<div class="user-detail">';
				// echo '<div class="date">';
				// echo $this -> Time -> format('F jS, Y', $upload['UserUpload']['created'], null);
				// echo '</div>';
				// echo '</div>';
				echo '</div>';
			}

		}
		echo '</div>';
	}
	?>


</div>
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.masonry.min', array('inline' => false)); ?>
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