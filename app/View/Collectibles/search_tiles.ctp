<?php
echo $this -> Html -> script('/bower_components/select2/select2', array('inline' => false));
echo $this -> Html -> css('/bower_components/select2/select2', array('inline' => false));
echo $this -> Html -> script('views/view.stash.add', array('inline' => false));
echo $this -> Html -> script('cs.stash', array('inline' => false));
echo $this -> Html -> script('models/model.collectible.user', array('inline' => false));
?>
<div class="col-md-12">
<h3><?php echo __('Collectibles Catalog'); ?></h3>
	<?php echo $this -> element('flash'); ?>
	<div class="row">
		<div class="col-md-9 filterable-list">
			<div class="row spacer">
				<div class="col-md-12">
					<div class="btn-group pull-right">
						<?php echo '<a class="btn" href="/collectibles/searchTiles' . $searchQueryString . '"><i class="fa fa-th-large"></i></a>'; ?>
						<?php echo '<a class="btn" href="/collectibles/search' . $searchQueryString . '"><i class="fa fa-list"></i></a>'; ?>
					</div>
				</div>
			</div>	
			<?php
			$url = '/collectibles/search/list';
			if ($viewType === 'tiles') {
				$url = '/collectibles/searchTiles/';
			}
			echo $this -> element('selected_search_filters');
			?>
			<div class="row spacer">
				<div class="col-md-12">	
					<div data-toggle="modal-gallery" data-target="#modal-gallery">
							<?php
							echo '<div id="titles-nav" class="hidden">';
							echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
							echo '</div>';
							echo '<div class="tiles stashable" data-toggle="modal-gallery" data-target="#modal-gallery">';

							foreach ($collectibles as $collectible) {
								$collectibleJSON = htmlspecialchars(json_encode($collectible['Collectible']), ENT_QUOTES, 'UTF-8');
								$stashCount = isset($collectible['Collectible']['userCounts']['stashCount']) ? $collectible['Collectible']['userCounts']['stashCount'] : 0;
								$wishlistCount = isset($collectible['Collectible']['userCounts']['wishListCount']) ? $collectible['Collectible']['userCounts']['wishListCount'] : 0;
								
								echo '<div class="tile" data-collectible=\'' . $collectibleJSON . '\'>';
								if (!empty($collectible['CollectiblesUpload'])) {
									foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
										if ($upload['primary']) {
											echo '<div class="image">';
											echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files')) . '">';
											$this -> FileUpload -> reset();
											echo $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $collectible['Collectible']['descriptionTitle'], 'imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 400, 'height' => 400)) . '</a>';
											$this -> FileUpload -> reset();
											echo '</div>';
											break;
										}
									}

									//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
								} else {
									echo '<div class="image"><img alt="" src="/img/no-photo.png"></div>';
								}

								echo '<div class="header"><h2>';
								echo '<a href="/collectibles/view/'.$collectible['Collectible']['id'].'/' . $collectible['Collectible']['slugField'] . '">';
					            if (!empty($collectible['Manufacture']['title'])) {
                               		echo $collectible['Manufacture']['title'] . ' - ';
                                }
								echo $collectible['Collectible']['displayTitle'] . '</a>';
								echo '</h2></div>';
								echo '<div class="content">';

								$description = str_replace('\n', "\n", $collectible['Collectible']['description']);
								$description = str_replace('\r', "\r", $description);

								echo '<p>' . $description . '</p>';
								echo '</div>';
								echo '<div class="menu tile-links clearfix adjust">';
								
								if ($isLoggedIn && $collectible['Collectible']['status_id'] === '4') {
									echo '<span><a data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-wishlist btn" title="Add to Wish List" href="#"><i class="fa fa-star"></i></a></span>';
									echo '<span><a class="add-full-to-stash btn" data-stash-count="' . $stashCount .'" data-wishlist-count="' . $wishlistCount .'" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectible['Collectible']['id'] . '"  href="javascript:void(0)" title="Add to Stash">';
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
		<div class="col-md-3">
			<div class="">
				<?php echo $this -> element('search_filters'); ?>
			</div>
		</div>
	</div>
</div>

<?php echo $this -> Minify -> script('jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('jquery.masonry.min', array('inline' => false)); ?>
<?php echo $this -> Html -> script('pages/page.collectible.search', array('inline' => true));?>

<script>
	$(function() {
		var $container = $('div.tiles');
		$container.imagesLoaded(function() {
			$container.masonry({
				itemSelector : '.tile',
				columnWidth : 422,
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