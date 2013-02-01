<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<div id="my-stashes-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php
			echo $stashUsername . '\'s';
			if ($stashType === 'default') {
				echo __(' stash', true);
			} else {
				echo __(' wishlist', true);
			}
			?></h2>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-view">
			<div class="actions stash">
				<ul class="nav nav-pills">
					<?php
					if ($stashType === 'default') {
						echo '<li class="selected">';
					} else {
						echo '<li>';
					}
					?>
					
					<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
					</li>
					<?php
					if ($stashType === 'wishlist') {
						echo '<li class="selected">';
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
		echo '<table class="table stashable" data-toggle="modal-gallery" data-target="#modal-gallery">';
		echo '<thead>';
		echo '<tr>';
		echo '<th></th>';
		echo '<th>' . $this -> Paginator -> sort('Collectible.manufacture_id', 'Manufacturer') . '</th>';
		echo '<th>' . $this -> Paginator -> sort('Collectible.name', 'Name') . '</th>';
		if ($stashType === 'default') {
			echo '<th>' . $this -> Paginator -> sort('edition_size', 'Edition Size') . '</th>';
			echo '<th>' . $this -> Paginator -> sort('cost', 'Price Paid') . '</th>';
			echo '<th>' . $this -> Paginator -> sort('purchased', 'Date Purchased') . '</th>';
		}
		echo '<th>' . $this -> Paginator -> sort('created', 'Date Added') . '</th>';
		if (isset($myStash) && $myStash) {
			echo '<th>' . __('Actions') . '</th>';
		}
		echo '</tr>';

		echo '</thead>';
		foreach ($collectibles as $key => $myCollectible) {
			echo '<tr class="stash-item">';
			if (!empty($myCollectible['Collectible']['CollectiblesUpload'])) {
				foreach ($myCollectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
					if ($upload['primary']) {
						echo '<td>';
						echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 50, 'height' => 50)) . '</a>';
						echo '</td>';
						break;
					}
				}

			} else {
				echo '<td> </td>';
			}

			echo '<td>' . $myCollectible['Collectible']['Manufacture']['title'] . '</td>';
			echo '<td>' . $myCollectible['Collectible']['name'] . '</td>';
			if ($stashType === 'default') {
				if (empty($myCollectible['Collectible']['edition_size'])) {
					echo '<td> - </td>';
				} else if (empty($myCollectible['CollectiblesUser']['edition_size'])) {
					echo '<td>' . __('Not Recorded') . '</td>';
				} else {
					echo '<td>' . $myCollectible['CollectiblesUser']['edition_size'] . '/' . $myCollectible['Collectible']['edition_size'] . '</td>';
				}

				if (!empty($myCollectible['CollectiblesUser']['cost'])) {
					echo '<td>' . $myCollectible['CollectiblesUser']['cost'] . '</td>';
				} else {
					echo '<td>' . __('Not Recorded') . '</td>';
				}

				if (!empty($myCollectible['CollectiblesUser']['purchase_date'])) {
					echo '<td>' . $this -> Time -> format('F jS, Y', $myCollectible['CollectiblesUser']['purchase_date'], null) . '</td>';
				} else {
					echo '<td>' . __('Not Recorded') . '</td>';
				}
			}
			echo '<td>' . $this -> Time -> format('F jS, Y h:i A', $myCollectible['CollectiblesUser']['created'], null) . '</td>';

			if (isset($myStash) && $myStash) {
				echo '<td>';
				echo '<div class="btn-group">';
				echo '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>';
				echo '<ul class="dropdown-menu">';
				if ($stashType === 'default') {
					echo '<li><a href="/collectibles_users/edit/' . $myCollectible['CollectiblesUser']['id'] . '" title=' . __('Edit') . '>Edit</a></li>';
				}
				echo '<li><a data-stash-type="' . $stashType . '" data-collectible-user-id="' . $myCollectible['CollectiblesUser']['id'] . '" class="remove-from-stash" title="Remove" href="#">Remove</a></li>';
				echo '</ul>';
				echo '</div>';
				echo '</td>';
			}

			echo '</tr>';
		}
		echo '</table>';
		echo '<div class="paging">';
		echo '<p>';
		echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
		echo '</p>';

		$urlparams = $this -> request -> query;
		unset($urlparams['url']);
		$this -> Paginator -> options(array('url' => $this -> passedArgs));

		echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));
		echo $this -> Paginator -> numbers(array('separator' => false));
		echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
		echo '</div>';
	} else {
		if ($stashType === 'default') {
			echo '<div class="empty">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</div>';
		} else {
			echo '<div class="empty">' . $stashUsername . __(' has no collectibles in their wishlist!', true) . '</div>';
		}
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