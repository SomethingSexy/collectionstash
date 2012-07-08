<div id="my-stashes-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $stashUsername . '\'s' .__(' stash', true)
			?></h2>
			<div class="actions icon">
				<ul>
					<?php
					if (isset($myStash) && $myStash) {
						echo '<li><a title="Add Collectibles" class="link add-stash-link" href="/collectibles/search"><img src="/img/icon/add_stash_link_25x25.png"/></a></li>';
						echo '<li>';
						echo '<a title="Edit" class="link glimpse-link" href="/stashs/edit/' . $stashUsername . '"><img src="/img/icon/pencil.png"/></a>';
						echo '</li>';
					}
					if (isset($isLoggedIn) && $isLoggedIn === true && !$myStash) {
						$userSubscribed = 'false';
						if (array_key_exists($stash['entity_type_id'], $subscriptions)) {
							$userSubscribed = 'true';
						}
						echo '<li><a id="subscribe" data-subscribed="' . $userSubscribed . '" data-entity-type="stash" data-entity-type-id="' . $stash['entity_type_id'] . '" class="link add-stash-link"></a></li>';
					}
					?>
					<?php
					if (isset($myStash) && $myStash) {
						if (Configure::read('Settings.User.uploads.allowed')) {
							echo '<li><a title="Upload Photos" class="link upload-link" href="/user_uploads/uploads"><img src="/img/icon/upload_photo.png"/></a></li>';
						}
					}
					?>
				</ul>
			</div>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-view">
			<div class="actions stash">
				<ul class="nav">
					<li>
					<?php echo '<a href="/stashs/view/' . $stashUsername . '">' .__('Collectibles') . '</a>'; ?>
					</li>
					<li>
					<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' .__('Photos') . '</a>'; ?>	
					</li>
					<li class="selected"><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' .__('Comments') . '</a>'; ?></li>
				</ul>	
			</div>
			<div id="comments" class="comments-container" data-entity-type-id="<?php echo $stash['entity_type_id']; ?>" data-type="stash" data-typeID="<?php echo $stash['id']; ?>"></div>
		</div>
	</div>
</div>

<?php echo $this -> Html -> script('jquery.comments', array('inline' => false)); ?>
<?php echo $this -> Html -> script('cs.subscribe', array('inline' => false)); ?>
<script>



	$(function() {

		$('#comments').comments();
	});

</script>
