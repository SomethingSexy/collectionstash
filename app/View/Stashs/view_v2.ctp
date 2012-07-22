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
					<li class="selected">
					<?php echo '<a href="/stashs/view/' . $stashUsername . '">' .__('Collectibles') . '</a>'; ?>
					</li>
					<li>
					<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' .__('Photos') . '</a>'; ?>	
					</li>
					<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' .__('Comments') . '</a>'; ?></li>
				</ul>	
			</div>
			<div class="title">
			<h3><?php echo __('Collectibles');?></h3>
			</div>
	<?php
	if (isset($collectibles) && !empty($collectibles)) {
		echo '<div id="titles-nav" class="hidden">';
		echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
		echo '</div>';
		echo '<div class="tiles" data-username="' . $stashUsername . '">';

		foreach ($collectibles as $key => $myCollectible) {
			echo '<div class="tile">';
			if (!empty($myCollectible['Collectible']['Upload'])) {
				echo '<div class="image">';
				echo '<a rel="gallery" href="'. $this -> FileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('imagePathOnly' => true,'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('imagePathOnly' => false,'uploadDir' => 'files', 'width' => 150, 'height' => 150)) . '</a>';
				echo '</div>';
				//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
			} else {
				echo '<div class="image"><a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '"><img src="/img/silhouette_thumb.png"/></a></div>';
			}
			echo '<div class="description">';
			echo '<span>' . $myCollectible['Collectible']['Collectibletype']['name'] . ' </span> <span>' . $myCollectible['Collectible']['Manufacture']['title'] . '</span>';
			echo '</div>';

			$detail = '';

			$editionSize = $myCollectible['Collectible']['edition_size'];
			if ($myCollectible['Collectible']['showUserEditionSize'] && isset($myCollectible['CollectiblesUser']['edition_size']) && !empty($myCollectible['CollectiblesUser']['edition_size'])) {
				$detail .= $myCollectible['CollectiblesUser']['edition_size'] . '/' . $myCollectible['Collectible']['edition_size'];

			} else if (isset($myCollectible['CollectiblesUser']['artist_proof'])) {
				if ($myCollectible['CollectiblesUser']['artist_proof']) {
					$detail .= __('Artist\'s Proof');
				}
			}
			$datetime = strtotime($myCollectible['CollectiblesUser']['created']);
			$mysqldate = date("m/d/y g:i A", $datetime);
			$detail .= '<div class="date">' . $mysqldate . '</div>';
			$detail .= '<div class="link"><a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '">Details</a></div>';

			echo '<div class="user-detail">';
			echo $detail;
			echo '</div>';
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
				finishedMsg : "All collectibles have been loaded!",
				msgText : "<em>Loading the next set of collectibles.</em>",
			}
		});

		$('#comments').comments();

	}); 
</script>