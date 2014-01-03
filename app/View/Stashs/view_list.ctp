<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<div class="col-md-12">
	<h2><?php
	echo $stashUsername . '\'s';
	echo __(' Stash', true);
	?></h2>
	<div id="my-stashes-component" class="widget widget-tabs">
	
			<?php echo $this -> element('flash'); ?>
	
					<ul class="nav nav-tabs widget-wide">
						<?php
						echo '<li class="active">';
						?>
						
						<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						echo '<li>';
						?>
						<?php echo '<a href="/wishlist/' . $stashUsername . '">' . __('Wish List') . '</a>'; ?>
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
								echo '<a title="Edit" class="btn" href="/stashs/edit/' . $stashUsername . '"><i class="icon-edit"></i> Edit</a>';
							}
							if (isset($isLoggedIn) && $isLoggedIn === true && !$myStash) {
								$userSubscribed = 'false';
								if (array_key_exists($stash['entity_type_id'], $subscriptions)) {
									$userSubscribed = 'true';
								}
								echo '<a  id="subscribe"  data-subscribed="' . $userSubscribed . '" data-entity-type="stash" data-entity-type-id="' . $stash['entity_type_id'] . '" class="btn" href="#"><i class="icon-heart"></i></a>';
							}
							?>
					</div>
				    <div class="btn-group views pull-right">
				    	<?php
						$currentStash = 'stash';
						echo '<a class="btn" href="/' . $currentStash . '/' . $stashUsername . '/tile"><i class="icon-th-large"></i></a>';
						echo '<a class="btn" href="/' . $currentStash . '/' . $stashUsername . '/list"><i class="icon-list"></i></a>';
	 					?>
				    </div>
				</div>
						<?php
						if (isset($collectibles) && !empty($collectibles)) {
							echo $this -> element('stash_table_list', array('collectibles' => $collectibles));
						} else {
							echo '<p class="empty">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
						}
		?>
		</div>

	</div>
</div>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.masonry.min', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/views/view.stash.sell', array('inline' => false)); ?>
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