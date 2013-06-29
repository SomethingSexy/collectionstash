<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<h2><?php
echo $stashUsername . '\'s';
if ($stashType === 'default') {
	echo __(' stash', true);
} else {
	echo __(' wishlist', true);
}
?></h2>
<div id="my-stashes-component" class="widget widget-tabs">

		<?php echo $this -> element('flash'); ?>

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
	</div>
	<?php
	if (isset($collectibles) && !empty($collectibles)) {
		echo $this -> element('stash_table_list', array('collectibles' => $collectibles));
	} else {
		if ($stashType === 'default') {
			echo '<p class="empty">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
		} else {
			echo '<p class="empty">' . $stashUsername . __(' has no collectibles in their wishlist!', true) . '</p>';
		}
	}
	?>
</div>
<?php echo $this -> Minify -> script('js/jquery.comments', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.masonry.min', array('inline' => false)); ?>
<?php echo $this -> Html -> script('views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Html -> script('models/model.collectible.user', array('inline' => false)); ?>

<script>
	<?php 
		if(isset($reasons)) {
			echo 'var reasons = \'' . json_encode($reasons) . '\';';
		}
	?>
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