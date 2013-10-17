<?php
echo $this -> Html -> script('views/view.stash.add', array('inline' => false));
echo $this -> Html -> script('models/model.collectible.user', array('inline' => false));
echo $this -> Html -> script('cs.stash', array('inline' => false));
?>

	<div class="page-header">
		<h2><?php echo $artist['Artist']['name']; ?></h2>
	</div>	
	<div class="row">
		<div class="col-md-12">
			<div class="widget manufacturer-collectibles">
				<div class="widget-header">
					<h3>Collectibles</h3>
				</div>
				
				<div class="widget-content">
					<div data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php
				echo '<div id="titles-nav" class="hidden">';
				echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
				echo '</div>';
				echo '<div class="tiles stashable" data-toggle="modal-gallery" data-target="#modal-gallery">';
	
				foreach ($collectibles as $collectible) {
					$collectibleJSON = json_encode($collectible['Collectible']);
					$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
					echo '<div class="tile">';
					if (!empty($collectible['CollectiblesUpload'])) {
						foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
							if ($upload['primary']) {
							echo '<div class="image">';
							echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files')) . '">';
							$this -> FileUpload -> reset();
							echo $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $collectible['Collectible']['descriptionTitle'], 'imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 400, 'height' => 400)) . '</a>';
							echo '</div>';
							break;
							}
						}
	
						//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
					} else {
						echo '<div class="image"><img src="/img/silhouette_thumb.png"/></div>';
					}
					echo '<div class="header">';
					echo '<h2><a href="/collectibles/view/' . $collectible['Collectible']['id'] . '/' . $collectible['Collectible']['slugField'] . '">' . $collectible['Collectible']['displayTitle'] . '</a></h2>';
					echo '</div>';
					echo '<div class="menu tile-links clearfix">';
					if ($isLoggedIn) {
						echo '<span><a data-stash-type="Wishlist" data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash btn" title="Add to Wishlist" href="#"><i class="icon-star"></i></a></span>';
						echo '<span><a class="add-full-to-stash btn" data-stash-type="Default" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectible['Collectible']['id'] . '"  href="javascript:void(0)" title="Add to Stash">';
						echo '<img src="/img/icon/add_stash_link_25x25.png">';
						echo '</a></span>';
						
					}
					echo '</div>';
					echo '</div>';
				}
				echo '</div>';
				     ?>
				</div>					
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




