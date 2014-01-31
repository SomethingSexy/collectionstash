<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<div class="col-md-12">
	<h2><?php
	echo $stashUsername . '\'s';
	echo __(' Wish List', true);
	?></h2>
	<div id="my-stashes-component" class="widget widget-tabs">
					<ul class="nav nav-tabs widget-wide">
						<?php
						echo '<li>';
						?>
						
						<?php echo '<a href="/user/' . $stashUsername . '/stash">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						echo '<li class="active">';
						?>
						<?php echo '<a href="/user/' . $stashUsername . '/wishlist">' . __('Wish List') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user/' . $stashUsername . '/sale">' . __('Sale/Trade List') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user/' . $stashUsername . '/photos">' . __('Photos') . '</a>'; ?>	
						</li>
						<li><?php echo '<a href="/user/' . $stashUsername . '/comments">' . __('Comments') . '</a>'; ?></li>
						<li><?php echo '<a href="/user/' . $stashUsername . '/history">' . __('History') . '</a>'; ?></li>
					</ul>	
		<div class="widget-content">
				<div class="clearfix">
				    <div class="btn-group views pull-right">
				    	<?php
						$currentStash = 'wishlist';
						echo '<a class="btn" href="/' . $currentStash . '/' . $stashUsername . '/tile"><i class="icon-th-large"></i></a>';
						echo '<a class="btn" href="/' . $currentStash . '/' . $stashUsername . '/list"><i class="icon-list"></i></a>';
	 					?>
				    </div>
				</div>			
		<?php
		if (isset($collectibles) && !empty($collectibles)) {

			echo '<div id="titles-nav" class="hidden">';
			echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
			echo '</div>';
			echo '<div class="tiles stashable row" data-username="' . $stashUsername . '" data-toggle="modal-gallery" data-target="#modal-gallery">';

			foreach ($collectibles as $key => $myCollectible) {
				echo '<div class="tile stash-item ">';
				if (!empty($myCollectible['Collectible']['CollectiblesUpload'])) {
					foreach ($myCollectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
						if ($upload['primary']) {
							echo '<div class="image">';
							$this -> FileUpload -> reset();
							echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files')) . '">';
							$this -> FileUpload -> reset();
							echo $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $myCollectible['Collectible']['descriptionTitle'], 'imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 400, 'height' => 400)) . '</a>';
							echo '</div>';
							break;
						}
					}

					//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
				} else {
					echo '<div class="image"><img alt="" src="/img/no-photo.png"></div>';
				}

				echo '<div class="header"><h2>';
				echo '<a class="title" target="_blank" href="/collectibles/view/' . $myCollectible['Collectible']['id'] . '">' . $myCollectible['Collectible']['displayTitle'] . '</a>';
				echo '</h2></div>';
				echo '<div class="content">';
				echo '<p></p>';
				echo '</div>';
				echo '<div class="menu tile-links clearfix">';

				echo '<span><a class="" title="View Collectible Details" href="/collectibles/view/' . $myCollectible['Collectible']['id'] . '"><i class="icon-info"></i></a></span>';
				if (isset($myStash) && $myStash) {
					$collectibleJSON = json_encode($myCollectible['Collectible']);
					$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
					$collectibleUserJSON = json_encode($myCollectible['CollectiblesWishList']);
					$collectibleUserJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleUserJSON));
					echo '<span><a data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $myCollectible['CollectiblesWishList']['id'] . '" class="remove-from-wishlist" title="Remove from Wish List" href="#"><i class="icon-trash"></i></a></span>';
				}

				echo '</div>';
				echo '</div>';
			}
			echo '</div>';

		} else {
			echo '<p class="empty clearfix">' . $stashUsername . __(' has no collectibles in their Wish List!', true) . '</p>';
		}
		?>
		</div>
	</div>
</div>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.masonry.min', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/models/model.collectible.user', array('inline' => false)); ?>

<script><?php
if (isset($reasons)) {
	echo 'var reasons = \'' . json_encode($reasons) . '\';';
}
	?>
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