<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<?php
$urlparams = $this -> request -> query;
unset($urlparams['url']);
?>
<div class="component" id="collectibles-list-component">
<div class="inside" >
<?php echo $this -> element('flash'); ?>
<div class="component-view">
<div class="title">
<h3><?php echo __('Search Results'); ?></h3>
<div class="btn-group views">
<?php echo '<a class="btn" href="/collectibles/searchTiles?' . http_build_query($urlparams) . '"><i class="icon-th-large"></i></a>'; ?>
<?php echo '<a class="btn" href="/collectibles/search?' . http_build_query($urlparams) . '"><i class="icon-list"></i></a>'; ?>
</div>
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
					echo '<div class="tile">';
					if (!empty($collectible['CollectiblesUpload'])) {
						foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
							if ($upload['primary']) {
								echo '<div class="image">';
								echo '<a rel="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 200, 'height' => 200)) . '</a>';
								echo '</div>';
								break;
							}
						}

						//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
					} else {
						echo '<div class="image"><img src="/img/silhouette_thumb.png"/></div>';
					}
					echo '<div class="header">';
					echo $this -> Html -> link($collectible['Collectible']['name'], array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']));
					echo '</div>';

					echo '<ul class="user-detail">';
					echo '<li>';
					echo '</ul>';
					echo '<a class="link add-stash-link" href="/collectibles_users/add/' .$collectible['Collectible']['id'] .'" title="Add to Stash">';
					echo '<img src="/img/icon/add_stash_link_25x25.png">';
					echo  '</a>';
					echo '<a data-stash-type="Wishlist" data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash btn" title="Add to Wishlist" href="#"><i class="icon-star"></i></a>';
					echo '</li>';
					echo '</div>';
				}
				echo '</div>';
			     ?>
			</div>
		</div>
	</div>
</div>

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
