<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<div class="col-md-12">
	<h2><?php
	echo $stashUsername . '\'s';
	echo __(' Wish List', true);
	?></h2>
	<div id="my-stashes-component" class="widget widget-tabs">
	
			<?php echo $this -> element('flash'); ?>
	
					<ul class="nav nav-tabs widget-wide">
						<?php
						echo '<li>';
						?>
						
						<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						echo '<li class="active">';
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
				    <div class="btn-group views pull-right">
				    	<?php
						$currentStash = 'wishlist';
						echo '<a class="btn" href="/' . $currentStash . '/' . $stashUsername . '/tile"><i class="icon-th-large"></i></a>';
						echo '<a class="btn" href="/' . $currentStash . '/' . $stashUsername . '/list"><i class="icon-list"></i></a>';
	 					?>
				    </div>
				</div>
						<?php
						if (isset($collectibles) && !empty($collectibles)) {
							echo '<div class="table-responsive"><table class="table stashable" data-toggle="modal-gallery" data-target="#modal-gallery"';
							echo '>';
							echo '<thead>';
							echo '<tr>';
							echo '<th></th>';
							echo '<th>' . $this -> Paginator -> sort('Collectible.name', 'Name') . '</th>';
							echo '<th>' . $this -> Paginator -> sort('Collectible.manufacture_id', 'Manufacturer') . '</th>';
							echo '<th>' . $this -> Paginator -> sort('created', 'Date Added') . '</th>';
							if (isset($myStash) && $myStash) {
								echo '<th>' . __('Actions') . '</th>';
							}
							echo '</tr>';

							echo '</thead>';
							foreach ($collectibles as $key => $myCollectible) {
								echo '<tr class="stash-item">';
								echo '<td style="min-width: 100px; max-width: 100px;">';

								if (!empty($myCollectible['Collectible']['CollectiblesUpload'])) {
									foreach ($myCollectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
										if ($upload['primary']) {
											echo '<a class="thumbnail col-md-6" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $myCollectible['Collectible']['descriptionTitle'], 'imagePathOnly' => false, 'uploadDir' => 'files')) . '</a>';
											break;
										}
									}

								} else {
									echo '<a class="thumbnail"><img alt="" src="/img/no-photo.png"></a>';
								}

								echo '</td>';

								echo '<td><a class="title" target="_blank" href="/collectibles/view/' . $myCollectible['Collectible']['id'] . '">' .  $myCollectible['Collectible']['name'] . '</a></td>';
								if (!empty($myCollectible['Collectible']['Manufacture']['title'])) {
									echo '<td>' . $myCollectible['Collectible']['Manufacture']['title'] . '</td>';
								} else {
									echo '<td>N/A</td>';
								}

								echo '<td>' . $this -> Time -> format('F jS, Y h:i A', $myCollectible['CollectiblesWishList']['created'], null) . '</td>';

								if (isset($myStash) && $myStash) {
									echo '<td>';
									echo '<div class="btn-group">';
									echo '<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>';
									echo '<ul class="dropdown-menu">';
									$prompt = 'data-prompt="false"';
									$collectibleJSON = json_encode($myCollectible['Collectible']);
									$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));

									$collectibleUserJSON = json_encode($myCollectible['CollectiblesWishList']);
									$collectibleUserJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleUserJSON));

									echo '<li><a ' . $prompt . ' data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $myCollectible['CollectiblesWishList']['id'] . '" class="remove-from-wishlist" title="Remove" href="#">Remove from Wish List</a></li>';
									echo '</ul>';
									echo '</div>';
									echo '</td>';
								}

								echo '</tr>';
							}
							echo '</table></div>';

							echo '<div class="pagination-container">';
							echo '<p>';
							echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
							echo '</p>';
							$urlparams = $this -> request -> query;
							unset($urlparams['url']);
							$this -> Paginator -> options(array('url' => $this -> passedArgs));

							echo '<ul class="pagination">';
							echo $this -> Paginator -> prev(__('previous', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled'));
							echo $this -> Paginator -> numbers(array('separator' => false, 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a'));
							echo $this -> Paginator -> next(__('next', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled'));
							echo '</ul>';
							echo '</div>';

						} else {
							echo '<p class="empty">' . $stashUsername . __(' has no collectibles in their wish list!', true) . '</p>';

						}
		?>
		</div>

	</div>
</div>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.masonry.min', array('inline' => false)); ?>
<?php echo $this -> Html -> script('views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Html -> script('models/model.collectible.user', array('inline' => false)); ?>

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