<div id="my-stashes-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $stashUsername . '\'s' .__(' stash', true)
			?></h2>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-view">
			<div class="actions stash">
				<ul class="nav nav-pills">
					<li class="selected">
					<?php echo '<a href="/stashs/view/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
					</li>
					<li>
					<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
					</li>
					<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
				</ul>	
			</div>
			<div class="title">
				<h3><?php echo __('Collectibles'); ?></h3>
				<div class="btn-group actions">
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
			    <div class="btn-group views">
			    	<?php echo '<a class="btn" href="/stashs/view/' . $stashUsername . '/tile"><i class="icon-th-large"></i></a>'; ?>
			    	<?php echo '<a class="btn" href="/stashs/view/' . $stashUsername . '/list"><i class="icon-list"></i></a>'; ?>
			    </div>
			</div>
	<?php
	if (isset($collectibles) && !empty($collectibles)) {
		echo '<div id="titles-nav" class="hidden">';
		echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
		echo '</div>';
		echo '<div class="tiles" data-username="' . $stashUsername . '" data-toggle="modal-gallery" data-target="#modal-gallery">';

		foreach ($collectibles as $key => $myCollectible) {
			echo '<div class="tile">';
			if (!empty($myCollectible['Collectible']['CollectiblesUpload'])) {
				foreach ($myCollectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
					if ($upload['primary']) {
						echo '<div class="image">';
						echo '<a rel="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 200, 'height' => 200)) . '</a>';
						echo '</div>';
						break;
					}
				}

				//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
			} else {
				echo '<div class="image"><a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '"><img src="/img/silhouette_thumb.png"/></a></div>';
			}
			echo '<div class="header">';
			echo '<a  href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '">' . $myCollectible['Collectible']['name'] . ' By ' . $myCollectible['Collectible']['Manufacture']['title'] . '</a>';
			echo '</div>';

			$detail = '';

			$editionSize = $myCollectible['Collectible']['edition_size'];
			if ($myCollectible['Collectible']['showUserEditionSize'] && isset($myCollectible['CollectiblesUser']['edition_size']) && !empty($myCollectible['CollectiblesUser']['edition_size'])) {
				$detail .= '<li>' . $myCollectible['CollectiblesUser']['edition_size'] . '/' . $myCollectible['Collectible']['edition_size'] . '</li>';

			} else if (isset($myCollectible['CollectiblesUser']['artist_proof'])) {
				if ($myCollectible['CollectiblesUser']['artist_proof']) {
					$detail .= '<li>' . __('Artist\'s Proof') . '</li>';
				}
			}
			echo '<ul class="user-detail">';
			echo '<li class="date">';
			echo $this -> Time -> format('F jS, Y', $myCollectible['CollectiblesUser']['created'], null);
			echo '</li>';
			echo '</ul>';
			echo '</div>';
		}
		echo '</div>';
	} else {
		echo '<div class="empty">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</div>';
	}
	?>
		</div>
	</div>
</div>
<?php echo $this -> Minify -> script('js/jquery.comments', array('inline' => false)); ?>
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

		$('#comments').comments();

	});
</script>