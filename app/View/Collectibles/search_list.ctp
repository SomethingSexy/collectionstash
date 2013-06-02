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
			<div class="well">
			<?php echo $this -> element('flash'); ?>
		
			<div class="page-header">
				<h2><?php echo __('Search Results'); ?></h2>
			</div>
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
			<div class="row-fluid stashable collectibles" data-toggle="modal-gallery" data-target="#modal-gallery">
				<div span="span12">			
				<?php
				// echo '<table class="table table-striped">';
				// echo '<thead>';
				// echo '<tr>';
				// echo '<th> </th>';
				// echo '<th>' . $this -> Paginator -> sort('name', 'Name') . '</th>';
				// echo '<th>' . $this -> Paginator -> sort('Collectible.variant', 'Variant', array('escape' => false)) . '</th>';
				// echo '<th>' . $this -> Paginator -> sort('Collectible.manufacture_id', 'Manufacturer', array('escape' => false)) . '</th>';
				// echo '<th>' . $this -> Paginator -> sort('Collectible.license_id', 'Brand', array('escape' => false)) . '</th>';
				// echo '<th>' . $this -> Paginator -> sort('Collectible.collectibletype_id', 'Platform', array('escape' => false)) . '</th>';
				// echo '<th>' . $this -> Paginator -> sort('collectibles_user_count', 'Stash Count', array('escape' => false)) . '</th>';
				// echo '<th>' . __('Actions') . '</th>';
				// echo '</tr>';
				//
				// echo '</thead>';
				foreach ($collectibles as $collectible) {
					$collectibleJSON = json_encode($collectible['Collectible']);
					$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
					echo '<div class="row-fluid spacer">';
					echo '<div class="span12 collectible">';
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

					echo '<div class="span8">';
					// name
					echo '<div class="row-fluid">';
					echo '<div class="span12">';
					echo '<span class="name">';
					echo $collectible['Collectible']['name'];
					if ($collectible['Collectible']['exclusive']) {
						echo __(' | Exclusive');
					}

					if ($collectible['Collectible']['variant']) {
						echo ' | <a href="/collectibles/view/' . $collectible['Collectible']['variant_collectible_id'] . '">' . __('Variant') . '</a>';
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

					echo '</span>';
					echo '</div>';
					echo '</div>';

					echo '</div>';
					// end span 8

					echo '<div class="span2">';
					echo '<div class="row-fluid">';
					echo '<div class="span12 count">';
					echo '<span class="badge pull-right">';

					if ($collectible['Collectible']['collectibles_user_count'] === '0') {
						echo $collectible['Collectible']['collectibles_user_count'];
					} else {
						echo $this -> Html -> link($collectible['Collectible']['collectibles_user_count'], array('admin' => false, 'action' => 'registry', 'controller' => 'collectibles_users', $collectible['Collectible']['id']));
					}

					echo '</span>';
					echo '</div>';
					echo '</div>';

					echo '<div class="row-fluid">';
					echo '<div class="span12">';
					echo '<div class="btn-group pull-right">';
					echo '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>';
					echo '<ul class="dropdown-menu">';
					if ($isLoggedIn) {
						echo '<li><a title="Add to Stash" class="add-full-to-stash" data-stash-type="Default" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectible['Collectible']['id'] . '"  href="javascript:void(0)">Add to Stash</a></li>';
						echo '<li><a data-stash-type="Default" data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash" title="Add to Stash without being prompted to enter information" href="#">Quick Add to Stash</a></li>';
						echo '<li><a data-stash-type="Wishlist" data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash" title="Add to Wishlist" href="#">Add to Wishlist</a></li>';
					}

					echo '<li>';
					echo $this -> Html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']));
					echo '</li>';
					echo '</ul>';
					echo '</div>';
					echo '</div>';
					echo '</div>';

					echo '</div>';
					//span 2

					echo '</div>';
					// row-fluid
					echo '</div>';
					// end span 12 collectible
					echo '</div>';
					// end row-fluid spacer
				}
			?>
				</div>
				<div class="paging">
					<p>
						<?php
						echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
						?>
					</p>
					<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled')); ?>
					<?php echo $this -> Paginator -> numbers(array('separator' => false)); ?>
					<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
				</div>
		
			</div>
			</div>
		</div>
		<div class="span5">
		<div class="well" data-spy="affix">
         <ul class="nav nav-list">
           <li class="nav-header">Sidebar</li>
           <li class="active"><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
           <li class="nav-header">Sidebar</li>
           <li><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
           <li class="nav-header">Sidebar</li>
           <li><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
           <li><a href="#">Link</a></li>
         </ul>
       </div>	
		</div>
	</div>
	

</div>
