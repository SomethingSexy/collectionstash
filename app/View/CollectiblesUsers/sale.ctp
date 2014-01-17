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
						echo '<li>';
						?>
						
						<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						echo '<li>';
						?>
						<?php echo '<a href="/wishlist/' . $stashUsername . '">' . __('Wish List') . '</a>'; ?>
						</li>
						<li class="active">
						<?php echo '<a href="/sale/' . $stashUsername . '">' . __('Sale/Trade List') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
						</li>
						<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
						<li><?php echo '<a href="/stashs/history/' . $stashUsername . '">' . __('History') . '</a>'; ?></li>
					</ul>	
					<div class="widget-content">
						<?php
						if (isset($collectibles) && !empty($collectibles)) {
							echo '<div class="table-responsive"><table class="table stashable" data-toggle="modal-gallery" data-target="#modal-gallery">';
							echo '<thead>';
							echo '<tr>';
							echo '<th></th>';
							echo '<th>' . $this -> Paginator -> sort('Collectible.name', 'Name') . '</th>';
							echo '<th>' . $this -> Paginator -> sort('condition_id', 'Condition') . '</th>';
							echo '<th>' . $this -> Paginator -> sort('edition_size', 'Edition Size') . '</th>';
							echo '<th>' . $this -> Paginator -> sort('Collectible.average_price', 'Collection Stash Value') . '</th>';
							echo '<th>' . $this -> Paginator -> sort('cost', 'Price') . '</th>';
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

								echo '<td><a class="title" target="_blank" href="/collectibles/view/' . $myCollectible['Collectible']['id'] . '">' . $myCollectible['Collectible']['name'] . '</a></td>';
								
								if (!empty($myCollectible['CollectiblesUser']['condition_id'])) {
									echo '<td>' . $myCollectible['Condition']['name'] . '</td>';
								} else {
									echo '<td> - </td>';
								}

								if (empty($myCollectible['Collectible']['edition_size'])) {
									echo '<td> - </td>';
								} else if (empty($myCollectible['CollectiblesUser']['edition_size'])) {
									echo '<td>' . __('Not Recorded') . '</td>';
								} else {
									echo '<td>' . $myCollectible['CollectiblesUser']['edition_size'] . '/' . $myCollectible['Collectible']['edition_size'] . '</td>';
								}

								if (isset($myCollectible['Collectible']['CollectiblePriceFact'])) {
									echo '<td>' . $myCollectible['Collectible']['CollectiblePriceFact']['average_price'] . '</td>';
								} else {
									echo '<td> - </td>';
								}

								if (!empty($myCollectible['CollectiblesUser']['sold_cost'])) {
									echo '<td>$' . $myCollectible['CollectiblesUser']['sold_cost'] . '</td>';
								} else if (!empty($myCollectible['CollectiblesUser']['traded_for'])) {
									echo '<td>' . $myCollectible['CollectiblesUser']['traded_for'] . '</td>';
								} else {
									echo '<td> - </td>';
								}

								if (isset($myStash) && $myStash) {
									echo '<td>';
									echo '<div class="btn-group">';
									echo '<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>';
									echo '<ul class="dropdown-menu">';
									$collectibleJSON = json_encode($myCollectible['Collectible']);
									$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));

									$collectibleUserJSON = json_encode($myCollectible['CollectiblesUser']);
									$collectibleUserJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleUserJSON));
									
									$collectibleUserListingJSON = json_encode($myCollectible['Listing']);
									$collectibleUserListingJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleUserListingJSON));

									if ($myCollectible['CollectiblesUser']['sale']) {
										// this will bring up the remove from stash modal
										echo '<li><a href="javascript:void(0);" data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $myCollectible['CollectiblesUser']['id'] . '" data-listing=\'' . $collectibleUserListingJSON . '\' class="stash-mark-as-sold" title="' . __('Mark as Sold') . '"><i class="icon-dollar"></i>  ' . __('Mark as Sold') . '</a></li>';
										// this will remove the listing completely and mark it as unsold
										echo '<li><a href="javascript:void(0);" data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $myCollectible['CollectiblesUser']['id'] . '" class="stash-remove-listing" title="' . __('Remove Listing') . '"><i class="icon-dollar"></i>  ' . __('Remove Listing') . '</a></li>';
										//
										echo '<li><a href="javascript:void(0);" data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $myCollectible['CollectiblesUser']['id'] . '" class="stash-edit-listing" title="' . __('Edit Listing') . '"><i class="icon-dollar"></i>  ' . __('Edit Listing') . '</a></li>';
									} else {
										echo '<li><a href="javascript:void(0);" data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $myCollectible['CollectiblesUser']['id'] . '" class="stash-sell" title="' . __('Sell') . '"><i class="icon-dollar"></i>  ' . __('Sell') . '</a></li>';
									}

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
							echo '<p class="empty">' . $stashUsername . __(' has no collectibles for sale or trade!', true) . '</p>';
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
	?></script>