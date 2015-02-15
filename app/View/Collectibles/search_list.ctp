<?php
echo $this -> Html -> script('/bower_components/select2/select2', array('inline' => false));
echo $this -> Html -> css('/bower_components/select2/select2', array('inline' => false));
echo $this -> Html -> script('views/view.stash.add', array('inline' => false));
echo $this -> Html -> script('cs.stash', array('inline' => false));
echo $this -> Html -> script('models/model.collectible.user', array('inline' => false));
?>
<?php echo $this -> Html -> script('pages/page.collectible.search', array('inline' => true));?>


<div id="collectibles-list-component">
<h3><?php echo __('Collectibles Catalog'); ?></h3>
	<?php
	if (!isset($isLoggedIn) || !$isLoggedIn) {
	?>
	<div class="row">
		<div class="col-md-7">
			<div class="alert alert-info">
  				<strong>Hey! Listen!</strong>
  				Not finding what you are looking for? Are we missing a collectible?  <a href="/users/login">Log in</a> or <a href="/users/register">register</a> to help us maintain an accurate and up-to-date collectible database.
    		</div>			
		</div>
	</div>
	<?php } ?>
	<div class="row">
		<div class="col-md-9 filterable-list">
				<?php echo $this -> element('flash'); ?>
				<div class="row spacer">
					<div class="col-md-12">
						<div class="btn-group pull-right">
							<?php echo '<a class="btn" href="/collectibles/searchTiles"><i class="fa fa-th-large"></i></a>'; ?>
							<?php echo '<a class="btn" href="/collectibles/search"><i class="fa fa-list"></i></a>'; ?>
						</div>
					</div>
				</div>
				<?php
				$url = '/collectibles/search/list';
				if ($viewType === 'tiles') {
					$url = '/collectibles/searchTiles/';
				}
				echo $this -> element('selected_search_filters');
				?>
				<div class="row spacer">
					<div class="col-md-12">
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('name', 'Name', array('class' => 'btn sort btn-default')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('Manufacture.title', 'Manufacturer', array('escape' => false, 'class' => 'btn sort btn-default')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('License.name', 'Brand', array('escape' => false, 'class' => 'btn sort btn-default')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('Collectibletype.name', 'Platform', array('escape' => false, 'class' => 'btn sort btn-default')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('collectibles_user_count', 'Stash Count', array('escape' => false, 'class' => 'btn sort btn-default')); ?>
						</div>					
					</div>
					
				</div>
				
				<div class="row stashable collectibles" data-toggle="modal-gallery" data-target="#modal-gallery">
					<div class="col-md-12">			
					<?php
					foreach ($collectibles as $collectible) {
						$collectibleJSON = htmlspecialchars(json_encode($collectible['Collectible']), ENT_QUOTES, 'UTF-8');
						echo '<div class="spacer">';
						// this is still need for the add to stash functionality
						$stashCount = isset($collectible['Collectible']['userCounts']['stashCount']) ? $collectible['Collectible']['userCounts']['stashCount'] : 0;
						$wishlistCount = isset($collectible['Collectible']['userCounts']['wishListCount']) ? $collectible['Collectible']['userCounts']['wishListCount'] : 0;
						echo '<div class="col-md-12 collectible">';
						echo '<div class="row">';
						//TODO: the problem is with the thumbnail/span1 in the table
						echo '<div class="col-md-2">';

						if (!empty($collectible['CollectiblesUpload'])) {
							foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
								if ($upload['primary']) {
									echo '<a class="thumbnail col-md-12" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files')) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('alt' => $collectible['Collectible']['descriptionTitle'], 'imagePathOnly' => false, 'uploadDir' => 'files')) . '</a>';
									break;
								}
							}

						} else {
							echo '<a class="thumbnail col-md-12"><img alt="" src="/img/no-photo.png"></a>';
						}

						echo '</div>';

						echo '<div class="col-md-7">';
						// name
						echo '<div class="row">';
						echo '<div class="col-md-12">';
						echo '<span class="name">';
						echo $collectible['Collectible']['name'];
						if ($collectible['Collectible']['exclusive']) {
							echo __(' | Exclusive');
						}

						if ($collectible['Collectible']['variant']) {
							echo ' | <a target="_blank" href="/collectibles/view/' . $collectible['Collectible']['variant_collectible_id'] . '">' . __('Variant') . '</a>';
						}
						echo '</span>';
						echo '</div>';
						// span10
						echo '</div>';

						echo '<div class="row">';
						echo '<div class="col-md-12">';
						echo '<span class="description">';
						$description = str_replace('\n', "\n", $collectible['Collectible']['description']);
						$description = str_replace('\r', "\r", $description);
						$description = nl2br($description);
						$description = html_entity_decode($description);

						echo $description;
						echo '</span>';
						echo '</div>';
						echo '</div>';

						echo '<div class="row">';
						echo '<div class="col-md-12">';
						echo '<span class="labels">';
						echo '<span class="label label-default">' . $collectible['Collectibletype']['name'] . '</span>';
						if (!empty($collectible['License']['name'])) {
							echo '<span class="label label-default">' . $collectible['License']['name'] . '</span>';
						}
						if (!empty($collectible['Manufacture']['title'])) {
							echo '<span class="label label-default">' . $collectible['Manufacture']['title'] . '</span>';
						}
						if (!empty($collectible['ArtistsCollectible'])) {
							echo '<span class="label label-default">' . $this -> Html -> link($collectible['ArtistsCollectible'][0]['Artist']['name'], array('admin' => false, 'controller' => 'artists', 'action' => 'index', $collectible['ArtistsCollectible'][0]['Artist']['id'], $collectible['ArtistsCollectible'][0]['Artist']['slug'])) . '</span>';
						}
						if (!empty($collectible['Scale']['scale'])) {
							echo '<span class="label label-default">' . $collectible['Scale']['scale'] . '</span>';
						}

						echo '</span>';
						echo '</div>';
						echo '</div>';

						echo '</div>';
						// end span 7

						echo '<div class="col-md-3">';
						echo '<div class="row">';
						echo '<div class="col-md-12">';
						echo '<div class="btn-group pull-right">';
						echo '<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>';
						echo '<ul class="dropdown-menu">';
						if ($isLoggedIn && $collectible['Collectible']['status_id'] === '4') {
							echo '<li><a title="Add to Stash" class="add-full-to-stash" data-stash-count="' . $stashCount .'" data-wishlist-count="' . $wishlistCount .'" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectible['Collectible']['id'] . '"  href="javascript:void(0)"><img src="/img/icon/add_stash_link_25x25.png"> Add to Stash</a></li>';
							echo '<li><a data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash" title="Add to Stash without being prompted to enter information" href="#"><img src="/img/icon/add_stash_link_25x25.png"> Quick Add to Stash</a></li>';
							echo '<li><a data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-wishlist" title="Add to Wish List" href="#"><i class="fa fa-star"></i> Add to Wish List</a></li>';
						}

						echo '<li>';
					
						echo $this -> Html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']));
						echo '</li>';
						echo '</ul>';
						echo '</div>';
						echo '</div>';
						echo '</div>';

						echo '</div>';
						//span 3

						echo '</div>';

						echo '<div class="row spacer">';
						echo '<div class="col-md-12 count">';
						echo '<span class="label label-default">';

						if ($collectible['Collectible']['collectibles_user_count'] === '0') {
							echo $collectible['Collectible']['collectibles_user_count'] . ' Stashed';
						} else {
							echo $this -> Html -> link($collectible['Collectible']['collectibles_user_count'] . ' Stashed', array('admin' => false, 'action' => 'registry', 'controller' => 'collectibles_users', $collectible['Collectible']['id']));
						}
						echo '</span>';
						if ($isLoggedIn) {
							echo '<span class="label label-default">' . $collectible['Collectible']['userCounts']['stashCount'] . ' in your Stash' . '</span>';
							echo '<span class="label label-default">' . $collectible['Collectible']['userCounts']['wishListCount'] . ' in your Wish List</span>';
						}
						echo '</div>';
						echo '</div>';

						// row
						echo '</div>';
						// end span 12 collectible
						echo '</div>';
						// end row spacer
					}
				?>
					</div>
	
				

				</div>	
				<div class="row">
					<div class="col-md-12">	
						<p><?php echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true))); ?></p>
						<ul class="pagination">
							<?php echo $this -> Paginator -> prev(__('previous', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled')); ?>
							<?php echo $this -> Paginator -> numbers(array('separator' => false, 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a')); ?>
							<?php echo $this -> Paginator -> next(__('next', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled')); ?>
						</ul>						
					</div>
					
				</div>			
		</div>
		<div class="col-md-3">
			<div class="">
				<?php echo $this -> element('search_filters'); ?>
			</div>
		</div>
	</div>
</div>

<script>
var uploadDirectory = "<?php echo $this -> FileUpload -> getUploadDirectory(); ?>";
	<?php
	if ($isLoggedIn) {
		echo 'var isLogggedIn = true;';
	} else {
		echo 'var isLogggedIn = false;';
	}
	?></script>
