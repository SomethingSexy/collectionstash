<?php echo $this -> Minify -> script('js/cs.stash', array('inline' => false)); ?>
<div class="col-md-12">
	<h2><?php
	echo $stashUsername . '\'s';
	if ($stashType === 'default') {
		echo __(' stash', true);
	} else {
		echo __(' wishlist', true);
	}
	?></h2>
	<div id="my-stashes-component" class="widget widget-tabs">
					<ul class="nav nav-tabs widget-wide">
						<?php
						if ($stashType === 'default') {
							echo '<li class="active">';
						} else {
							echo '<li>';
						}
						?>
						
						<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						if ($stashType === 'wishlist') {
							echo '<li class="active">';
						} else {
							echo '<li>';
						}
						?>
						<?php echo '<a href="/wishlist/' . $stashUsername . '">' . __('Wishlist') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
						</li>
						<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
						<li><?php echo '<a href="/stashs/history/' . $stashUsername . '">' . __('History') . '</a>'; ?></li>
					</ul>	
		<div class="widget-content">
				<div class="clearfix">
					<div class="btn-group actions pull-left">
							<?php
							if (isset($myStash) && $myStash) {
								echo '<a title="Edit" class="btn" href="/stashs/edit/' . $stashUsername . '"><i class="icon-edit"></i></a>';
							}
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
				    <div class="btn-group views pull-right">
				    	<?php
						$currentStash = 'stash';
						if ($stashType === 'wishlist') {
							$currentStash = 'wishlist';
						}
	
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
					echo '<div class="image"><a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '"><img alt="" src="/img/no-photo.png"></a></div>';
				}
	
				echo '<div class="header"><h2>';
				if ($stashType === 'default') {
					if ($myCollectible['Collectible']['custom'] || $myCollectible['Collectible']['original']) {
						echo '<a class="title" href="/collectibles/view/' . $myCollectible['Collectible']['id'] . '">' . $myCollectible['Collectible']['displayTitle'] . '</a>';
					} else {
						echo '<a class="title" href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '">' . $myCollectible['Collectible']['displayTitle'] . '</a>';
					}
	
				} else {
					echo '<a class="title" href="/collectibles/view/' . $myCollectible['Collectible']['id'] . '">' . $myCollectible['Collectible']['displayTitle'] . '</a>';
				}
				echo '</h2></div>';
	
				echo '<div class="content">';
				echo '<p></p>';
				echo '</div>';
				echo '<div class="menu tile-links clearfix">';

				echo '<span><a class="" title="View Collectible Details" href="/collectibles/view/' . $myCollectible['Collectible']['id'] . '"><i class="icon-info"></i></a></span>';
				if (isset($myStash) && $myStash) {
					$collectibleJSON = json_encode($myCollectible['Collectible']);
					$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
					if ($stashType === 'default') {
						echo '<span><a class="" title="Edit" href="/collectibles_users/edit/' . $myCollectible['CollectiblesUser']['id'] . '"><i class="icon-edit"></i></a></span>';
					}
					if ($stashType === 'default') {
						$prompt = 'data-prompt="true"';
					} else {
						$prompt = 'data-prompt="false"';
					}
	
					$collectibleUserJSON = json_encode($myCollectible['CollectiblesUser']);
					$collectibleUserJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleUserJSON));
					echo '<span><a ' . $prompt . ' data-stash-type="' . $stashType . '" data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $myCollectible['CollectiblesUser']['id'] . '" class="remove-from-stash" title="Remove" href="#"><i class="icon-trash"></i></a></span>';
				}

				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
	
		} else {
			if ($stashType === 'default') {
				echo '<p class="empty clearfix">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
			} else {
				echo '<p class="empty clearfix">' . $stashUsername . __(' has no collectibles in their wishlist!', true) . '</p>';
			}
		}
		?>
		</div>
	</div>
</div>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.masonry.min', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/views/view.stash.remove', array('inline' => false)); ?>
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