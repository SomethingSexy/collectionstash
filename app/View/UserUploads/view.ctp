<h2><?php echo $stashUsername . '\'s' .__(' stash', true)?></h2>
<div id="my-stashes-component" class="widget widget-tabs">
	<?php echo $this -> element('flash'); ?>
	<ul class="nav nav-tabs widget-wide">
		<li>
		<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
		</li>
		<li><?php echo '<a href="/wishlist/' . $stashUsername . '">' . __('Wishlist') . '</a>'; ?></li>
		<li class="active">
		<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
		</li>
		<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
		<li><?php echo '<a href="/stashs/history/' . $stashUsername . '">' . __('History') . '</a>'; ?></li>
	</ul>	
	<div class="widget-content">
		<div class="btn-group actions">
			<?php
			if (isset($isLoggedIn) && $isLoggedIn === true && !$myStash) {
				$userSubscribed = 'false';
				if (array_key_exists($stash['entity_type_id'], $subscriptions)) {
					$userSubscribed = 'true';
				}
				echo '<a  id="subscribe"  data-subscribed="' . $userSubscribed . '" data-entity-type="stash" data-entity-type-id="' . $stash['entity_type_id'] . '" class="btn" href="#"><i class="icon-heart"></i></a>';
			}
			?>
			<?php
			if (isset($myStash) && $myStash) {
				if (Configure::read('Settings.User.uploads.allowed')) {
					echo '<a title="Upload Photos" class="btn" href="/user_uploads/uploads"><i class="icon-camera"></i></a>';
				}
			}
			?>
		</div>
		<?php
		if (isset($userUploads) && !empty($userUploads)) {
			echo '<div class="row">';
			echo '<div class="span12">';
			echo '<div id="titles-nav" class="hidden">';
			echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
			echo '</div>';
			echo '<div class="tiles" data-username="' . $stashUsername . '" data-toggle="modal-gallery" data-target="#modal-gallery">';

			foreach ($userUploads as $key => $upload) {

				if (!empty($upload['UserUpload'])) {
					echo '<div class="tile photo">';
					echo '<div class="image">';
					$this -> FileUpload -> reset();
					echo '<a title="' . $upload['UserUpload']['title'].' - ' . $upload['UserUpload']['description'] . '" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['UserUpload']['name'], array('imagePathOnly' => true, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '">';
					$this -> FileUpload -> reset();
					echo $this -> FileUpload -> image($upload['UserUpload']['name'], array('resizeType' => 'adaptive', 'imagePathOnly' => false, 'width' => 400, 'height' => 400, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '</a>';
					echo '</div>';
					echo '</div>';
				}

			}
			echo '</div>';
			echo '</div>';
			echo '</div>';
		} else {
			echo '<div class="empty">' . $stashUsername . __(' has no photos in their stash!', true) . '</div>';
		}
		?>
	</div>
</div>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.masonry.min', array('inline' => false)); ?>
<script>
	$(function() {
		var $container = $('div.tiles');
		$container.imagesLoaded(function() {
			$container.masonry({
				itemSelector : '.tile'
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