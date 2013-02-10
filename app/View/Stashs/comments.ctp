<div id="my-stashes-component" class="well">
	<h2><?php echo $stashUsername . '\'s' .__(' stash', true)
	?></h2>
	<?php echo $this -> element('flash'); ?>
	<div class="actions stash">
		<ul class="nav nav-pills">
			<li>
			<?php echo '<a href="/stash/' . $stashUsername . '">' .__('Collectibles') . '</a>'; ?>
			</li>
			<li><?php echo '<a href="/wishlist/' . $stashUsername . '">' . __('Wishlist') . '</a>'; ?></li>
			<li>
			<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' .__('Photos') . '</a>'; ?>	
			</li>
			<li class="selected"><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' .__('Comments') . '</a>'; ?></li>
		</ul>	
	</div>
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

<?php echo $this -> Minify -> script('js/jquery.comments', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<script>
	$(function() {

		$('#comments').comments();
	});
</script>
