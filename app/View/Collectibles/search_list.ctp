<?php
echo $this -> Html -> script('views/view.stash.add', array('inline' => false));
echo $this -> Html -> script('models/model.collectible.user', array('inline' => false));
echo $this -> Html -> script('cs.stash', array('inline' => false));
?>

<?php
$urlparams = $this -> request -> query;
unset($urlparams['url']);
?>

<div id="collectibles-list-component" class="span12">
	<div class="row-fluid">
		<div class="span7">
			<div class="widget">
			<div class="widget-header">
				<h3><?php echo __('Search Results'); ?></h3>
			</div>
			<div class="widget-content">
				<?php echo $this -> element('flash'); ?>
				<div class="btn-group pull-right">
			    	<?php echo '<a class="btn" href="/collectibles/searchTiles?' . http_build_query($urlparams) . '"><i class="icon-th-large"></i></a>'; ?>
			    	<?php echo '<a class="btn" href="/collectibles/search?' . http_build_query($urlparams) . '"><i class="icon-list"></i></a>'; ?>
			    </div>
				<?php
				$url = '/collectibles/search/list';
				if ($viewType === 'tiles') {
					$url = '/collectibles/searchTiles/';
				}
				echo $this -> element('search_filters', array('searchUrl' => $url . $viewType));
				?>
				<div class="row-fluid spacer">
					<div class="span12">
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('name', 'Name', array('class' => 'btn sort')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('Collectible.manufacture_id', 'Manufacturer', array('escape' => false, 'class' => 'btn sort')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('Collectible.license_id', 'Brand', array('escape' => false, 'class' => 'btn sort')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('Collectible.collectibletype_id', 'Platform', array('escape' => false, 'class' => 'btn sort')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('collectibles_user_count', 'Stash Count', array('escape' => false, 'class' => 'btn sort')); ?>
						</div>					
					</div>
					
				</div>
				
				<div class="row-fluid stashable collectibles" data-toggle="modal-gallery" data-target="#modal-gallery">
					<div span="span12">			
					<?php
					foreach ($collectibles as $collectible) {
						$fullCollectibleJSON = json_encode($collectible);
						$fullCollectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $fullCollectibleJSON));

						$collectibleJSON = json_encode($collectible['Collectible']);
						$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
						echo '<div class="row-fluid spacer">';
						echo '<div class="span12 collectible" data-collectible=\'' . $fullCollectibleJSON . '\'>';
						echo '<div class="row-fluid">';
						//TODO: the problem is with the thumbnail/span1 in the table
						echo '<div class="span2"><ul class="thumbnails"><li class="span12">';

						if (!empty($collectible['CollectiblesUpload'])) {
							foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
								if ($upload['primary']) {
									echo '<a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'uploadDir' => 'files')) . '</a>';
									break;
								}
							}

						} else {
							echo '<a class="thumbnail"><img alt="" src="/img/no-photo.png"></a>';
						}

						echo '</li></ul></div>';

						echo '<div class="span7">';
						// name
						echo '<div class="row-fluid">';
						echo '<div class="span12">';
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

						echo '<div class="row-fluid">';
						echo '<div class="span12">';
						echo '<span class="description">';

						echo $collectible['Collectible']['description'];
						echo '</span>';
						echo '</div>';
						echo '</div>';

						echo '<div class="row-fluid">';
						echo '<div class="span12">';
						echo '<span class="labels">';
						echo '<span class="label">' . $collectible['Collectibletype']['name'] . '</span>';
						if (!empty($collectible['License']['name'])) {
							echo '<span class="label">' . $collectible['License']['name'] . '</span>';
						}
						if (!empty($collectible['Manufacture']['title'])) {
							echo '<span class="label">' . $collectible['Manufacture']['title'] . '</span>';
						}
						if (!empty($collectible['ArtistsCollectible'])) {
							echo '<span class="label">' . $this -> Html -> link($collectible['ArtistsCollectible'][0]['Artist']['name'], array('admin' => false, 'controller' => 'artists', 'action' => 'index', $collectible['ArtistsCollectible'][0]['Artist']['id'], $collectible['ArtistsCollectible'][0]['Artist']['slug'])) . '</span>';
						}

						echo '</span>';
						echo '</div>';
						echo '</div>';

						echo '</div>';
						// end span 7

						echo '<div class="span3">';
						echo '<div class="row-fluid">';
						echo '<div class="span12">';
						echo '<div class="btn-group pull-right">';
						echo '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>';
						echo '<ul class="dropdown-menu">';
						if ($isLoggedIn) {
							echo '<li><a title="Add to Stash" class="add-full-to-stash" data-stash-type="Default" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectible['Collectible']['id'] . '"  href="javascript:void(0)"><img src="/img/icon/add_stash_link_25x25.png"> Add to Stash</a></li>';
							echo '<li><a data-stash-type="Default" data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash" title="Add to Stash without being prompted to enter information" href="#"><img src="/img/icon/add_stash_link_25x25.png"> Quick Add to Stash</a></li>';
							echo '<li><a data-stash-type="Wishlist" data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash" title="Add to Wishlist" href="#"><i class="icon-star"></i> Add to Wishlist</a></li>';
						}

						echo '<li>';
						//<i class="icon-search"></i>
						echo $this -> Html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']));
						echo '</li>';
						echo '</ul>';
						echo '</div>';
						echo '</div>';
						echo '</div>';

						echo '</div>';
						//span 3

						echo '</div>';

						echo '<div class="row-fluid">';
						echo '<div class="span12 count">';
						echo '<span class="label">';

						if ($collectible['Collectible']['collectibles_user_count'] === '0') {
							echo $collectible['Collectible']['collectibles_user_count'] . ' Stashed';
						} else {
							echo $this -> Html -> link($collectible['Collectible']['collectibles_user_count'] . ' Stashed', array('admin' => false, 'action' => 'registry', 'controller' => 'collectibles_users', $collectible['Collectible']['id']));
						}
						echo '</span>';
						if ($isLoggedIn && $collectible['Collectible']['userCounts']) {
							foreach ($collectible['Collectible']['userCounts'] as $key => $value) {
								if ($value['type'] === 'Default') {
									echo '<span class="label">' . $value['count'] . ' in your Stash' . '</span>';
								} else {
									echo '<span class="label">' . $value['count'] . ' in your ' . $value['type'] . '</span>';
								}

							}

						}
						echo '</div>';
						echo '</div>';

						// row-fluid
						echo '</div>';
						// end span 12 collectible
						echo '</div>';
						// end row-fluid spacer
					}
				?>
					</div>
						<p>
							<?php
							echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
							?>
						</p>
					<div class="pagination">
						<ul>
						<?php echo $this -> Paginator -> prev(__('previous', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled')); ?>
						<?php echo $this -> Paginator -> numbers(array('separator' => false, 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a')); ?>
						<?php echo $this -> Paginator -> next(__('next', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled')); ?>
						</ul>
					</div>
			
				</div>				
			</div>

			</div>
		</div>
		<div class="span5 collectible-detail">
			<div class="well" data-spy="affix" data-offset-top="200">
				<p class="text-center lead">Select a collectible to see more detail here!</p>
				
				
       		</div>	
		</div>
	</div>
</div>

<script>var uploadDirectory =      "<?php echo $this -> FileUpload -> getUploadDirectory(); ?>
	";
	<?php
	if ($isLoggedIn) {
		echo 'var isLogggedIn = true;';
	} else {
		echo 'var isLogggedIn = false;';
	}
	?></script>
<?php
echo $this -> Html -> script('pages/page.collectible.search', array('inline' => true));
?>