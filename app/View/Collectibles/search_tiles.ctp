<?php
echo $this -> Html -> script('views/view.stash.add', array('inline' => false));
echo $this -> Html -> script('models/model.collectible.user', array('inline' => false));
echo $this -> Html -> script('cs.stash', array('inline' => false));
?>
<?php
$urlparams = $this -> request -> query;
unset($urlparams['url']);
?>
<h3><?php echo __('Collectibles Catalog'); ?></h3>


	<?php echo $this -> element('flash'); ?>

		<div class="btn-group pull-right">
			<?php echo '<a class="btn" href="/collectibles/searchTiles?' . http_build_query($urlparams) . '"><i class="icon-th-large"></i></a>'; ?>
			<?php echo '<a class="btn" href="/collectibles/search?' . http_build_query($urlparams) . '"><i class="icon-list"></i></a>'; ?>
		</div>
	<?php
	$url = '/collectibles/search/list';
	if ($viewType === 'tiles') {
		$url = '/collectibles/searchTiles/';
	}
	echo $this -> element('search_filters', array('searchUrl' => $url . $viewType));
	?>
			<div data-toggle="modal-gallery" data-target="#modal-gallery">
			<?php
			echo '<div id="titles-nav" class="hidden">';
			echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
			echo '</div>';
			echo '<div class="tiles stashable" data-toggle="modal-gallery" data-target="#modal-gallery">';

			foreach ($collectibles as $collectible) {
				$collectibleJSON = json_encode($collectible['Collectible']);
				$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
				echo '<div class="tile" data-collectible=\'' . $collectibleJSON . '\'>';
				if (!empty($collectible['CollectiblesUpload'])) {
					foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
						if ($upload['primary']) {
							echo '<div class="image">';
							echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">';
							$this -> FileUpload -> reset();
							echo $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 400, 'height' => 400)) . '</a>';
							echo '</div>';
							break;
						}
					}

					//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
				} else {
					echo '<div class="image"><img src="/img/silhouette_thumb.png"/></div>';
				}
				
				
				echo '<div class="header"><h2>';
				echo $this -> Html -> link($collectible['Collectible']['displayTitle'], array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']));
				echo '</h2></div>';
				echo '<div class="content">';
				echo '<p>' . $collectible['Collectible']['description'] . '</p>';
				echo '</div>';
			 	echo '<div class="menu tile-links clearfix">';
				echo '<ul class="unstyled">';
				if ($isLoggedIn) {
					echo '<li><a data-stash-type="Wishlist" data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash" title="Add to Wishlist" href="#"><i class="icon-star"></i></a></li>';
					echo '<li><a class="add-full-to-stash" data-stash-type="Default" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectible['Collectible']['id'] . '"  href="javascript:void(0)" title="Add to Stash">';
					echo '<img src="/img/icon/add_stash_link_25x25.png">';
					echo '</a></li>';
				}
				echo '</ul>';
				echo '</div>';
				echo '</div>';

			}
			echo '</div>';
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
				columnWidth : 420,
				isAnimated : true
			});
		});

		$container.infinitescroll({
			nextSelector : "#titles-nav a",
			navSelector : "#titles-nav",
			itemSelector : ".tile",
			loading : {
				finishedMsg : "All collectibles have been loaded!",
				msgText : "<em>Loading the next set of collectibles.</em>",
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
