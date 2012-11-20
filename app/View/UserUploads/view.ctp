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
					<li>
					<?php echo '<a href="/stashs/view/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
					</li>
					<li class="selected">
					<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
					</li>
					<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
				</ul>	
			</div>
			<div class="title">
				<h3><?php echo __('Photos'); ?></h3>
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
				<?php
				if (isset($myStash) && $myStash) {
					if (Configure::read('Settings.User.uploads.allowed')) {
						echo '<a title="Upload Photos" class="btn" href="/user_uploads/uploads"><i class="icon-camera"></i></a>';
					}
				}
				?>
			</div>
	<?php
	if (isset($userUploads) && !empty($userUploads)) {
		echo '<div id="titles-nav" class="hidden">';
		echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
		echo '</div>';
		echo '<div class="tiles" data-username="' . $stashUsername . '" data-toggle="modal-gallery" data-target="#modal-gallery">';

		foreach ($userUploads as $key => $upload) {

			if (!empty($upload['UserUpload'])) {
				echo '<div class="tile photo">';
				echo '<div class="image">';
				echo '<a rel="gallery" href="' . $this -> FileUpload -> image($upload['UserUpload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '">' . $this -> FileUpload -> image($upload['UserUpload']['name'], array('imagePathOnly' => false, 'width' => 150, 'height' => 225, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '</a>';
				echo '</div>';
				echo '<div class="description">';
				echo '<span>' . $upload['UserUpload']['title'] . '</span><span>' . $upload['UserUpload']['description'] . '</span>';
				echo '</div>';
				echo '<div class="user-detail">';
				echo '<div class="date">';
				echo $this -> Time -> format('F jS, Y', $upload['UserUpload']['created'], null);
				echo '</div>';
				echo '</div>';
				echo '</div>';
			}

		}
		echo '</div>';
	} else {
		echo '<div class="empty">' . $stashUsername . __(' has no photos in their stash!', true) . '</div>';
	}
	?>
		</div>
	</div>
</div>
<?php echo $this -> Minify -> script('js/jquery.comments', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<script>
	$(function() {
		$('.tiles').infinitescroll({
			nextSelector : "#titles-nav a",
			navSelector : "#titles-nav",
			itemSelector : ".tile",
			loading : {
				finishedMsg : "All photos have been loaded!",
				msgText : "<em>Loading the next set of photos.</em>",
			}
		});

		$('#comments').comments();

	});
</script>