<?php
echo $this -> Html -> script('/bower_components/select2/select2', array('inline' => false));
echo $this -> Html -> css('/bower_components/select2/select2', array('inline' => false));
echo $this -> Html -> script('views/view.stash.add', array('inline' => false));
echo $this -> Html -> script('models/model.collectible.user', array('inline' => false));
echo $this -> Html -> script('cs.stash', array('inline' => false));
?>
<div class="col-md-12">
	<h2><?php echo $manufacture['Manufacture']['title']?> <small><?php echo $manufacture['Manufacture']['url']?></small></h2>
	
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
			echo '<div class="tiles boxed-tiles stashable" data-toggle="modal-gallery" data-target="#modal-gallery">';

			foreach ($collectibles as $collectible) {
				$collectibleJSON = json_encode($collectible['Collectible']);
				$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
				echo '<div class="tile photo">';
				echo '<div class="image">';
				echo '<span class="label label-info">'. $this -> Html -> link($collectible['Collectible']['displayTitle'], array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']), array('title' => $collectible['Collectible']['displayTitle'])). '</span>';
				if (!empty($collectible['CollectiblesUpload'])) {
					foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
						if ($upload['primary']) {
							echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files')) . '">';
							$this -> FileUpload -> reset();
							echo $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $collectible['Collectible']['descriptionTitle'], 'imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 200, 'height' => 200, 'resizeType' => 'adaptive')) . '</a>';
							$this -> FileUpload -> reset();
							break;
						}
					}
				} else {
					echo '<div class="image"><img src="/img/no-photo.png"/></div>';
				}
				if ($isLoggedIn) {
					echo '<div class="tile-actions bottom-right">';
					echo '<div class="btn-group">';
					echo '<a data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-wishlist btn btn-default" title="Add to Wish List" href="#"><i class="fa fa-star"></i></a></span>';
					echo '<a class="add-full-to-stash btn btn-default" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectible['Collectible']['id'] . '"  href="javascript:void(0)" title="Add to Stash">';
					echo '<img src="/img/icon/add_stash_link_16x16.png">';
					echo '</a>';
					echo '</div>';
					echo '</div>';
				}
				echo '</div>'; //image
				echo '</div>';//tile photo
			}
			echo '</div>';
			     ?>
			</div>					
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this -> Minify -> script('jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('jquery.masonry.min', array('inline' => false)); ?>

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




