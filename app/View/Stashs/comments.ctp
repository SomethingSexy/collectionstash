<div class="col-md-12">
	<h2><?php echo $stashUsername . '\'s' .__(' stash', true)?></h2>
	<div id="my-stashes-component" class="widget widget-tabs">
		<?php echo $this -> element('flash'); ?>
			<ul class="nav nav-tabs widget-wide">
				<li>
				<?php echo '<a href="/user/' . $stashUsername . '/stash">' . __('Collectibles') . '</a>'; ?>
				</li>
				<li>
				<?php echo '<a href="/user/' . $stashUsername . '/wishlist">' . __('Wish List') . '</a>'; ?>
				</li>
				<li>
				<?php echo '<a href="/user/' . $stashUsername . '/sale">' . __('Sale/Trade List') . '</a>'; ?>
				</li>
				<li>
				<?php echo '<a href="/user/' . $stashUsername . '/photos">' . __('Photos') . '</a>'; ?>	
				</li>
				<li class="active"><?php echo '<a href="/user/' . $stashUsername . '/comments">' . __('Comments') . '</a>'; ?></li>
				<li><?php echo '<a href="/user/' . $stashUsername . '/history">' . __('History') . '</a>'; ?></li>
			</ul>	
		<div class="widget-content">
			<div class="btn-group actions">
				<?php
				if (isset($isLoggedIn) && $isLoggedIn === true && !$myStash) {
					$userSubscribed = 'false';
					if (array_key_exists($stash['entity_type_id'], $subscriptions)) {
						$userSubscribed = 'true';
					}
					echo '<a  id="subscribe"  data-subscribed="' . $userSubscribed . '" data-entity-type="stash" data-entity-type-id="' . $stash['entity_type_id'] . '" class="btn" href="#"><i class="icon-heart"></i></a>';
				}
				?>
			</div>
			<div id="comments" class="comments-container" data-entity-type-id="<?php echo $stash['entity_type_id']; ?>" data-type="stash" data-typeID="<?php echo $stash['id']; ?>"></div>
		</div>
	</div>
</div>
<?php echo $this -> Minify -> script('jquery.comments', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('cs.subscribe', array('inline' => false)); ?>
<script>
	$(function() {

		$('#comments').comments();
	});
</script>
