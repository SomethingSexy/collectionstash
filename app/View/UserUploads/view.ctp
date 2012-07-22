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
						echo '<a title="Edit" class="link glimpse-link" href="/stashs/edit/' . $stashUsername . '"><img src="/img/icon/pencil-gray.png"/></a>';
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
							echo '<li><a title="Upload Photos" class="link upload-link" href="/user_uploads/uploads"><img src="/img/icon/upload_photo-gray.png"/></a></li>';
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
					<?php echo '<a href="/stashs/view/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
					</li>
					<li class="selected">
					<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
					</li>
					<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
				</ul>	
			</div>
			<div class="title">
			<h3><?php echo __('Photos');?></h3>
			</div>
	<?php
	if (isset($userUploads) && !empty($userUploads)) {
		echo '<div id="titles-nav" class="hidden">';
		echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
		echo '</div>';
		echo '<div class="tiles" data-username="' . $stashUsername . '">';

		foreach ($userUploads as $key => $upload) {

			if (!empty($upload['UserUpload'])) {
				echo '<div class="tile photo">';
				echo '<div class="image">';
				echo '<a rel="gallery" href="' . $this -> FileUpload -> image($upload['UserUpload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '">' . $this -> FileUpload -> image($upload['UserUpload']['name'], array('imagePathOnly' => false, 'width' => 150, 'height'=> 225, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $upload['UserUpload']['user_id'])) . '</a>';
				echo '</div>';

				echo '<div class="description">';
				echo '<span>' . $upload['UserUpload']['title'] . '</span><span>' . $upload['UserUpload']['description'] . '</span>';
				echo '</div>';
				$detail = '';

				$datetime = strtotime($upload['UserUpload']['created']);
				$mysqldate = date("m/d/y g:i A", $datetime);
				$detail .= '<div class="date">' . $mysqldate . '</div>';

				echo '<div class="user-detail">';
				echo $detail;
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
<?php echo $this -> Html -> script('jquery.comments', array('inline' => false)); ?>
<?php echo $this -> Html -> script('cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Html -> script('jquery.infinitescroll', array('inline' => false)); ?>
<script>
	$(function() {
		$('.tiles .image a').fancybox();
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