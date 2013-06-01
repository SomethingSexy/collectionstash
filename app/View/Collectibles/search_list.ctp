<?php
echo $this -> Html -> script('views/view.stash.add', array('inline' => false));
echo $this -> Html -> script('models/model.collectible.user', array('inline' => false));
echo $this -> Html -> script('cs.stash', array('inline' => false));
?>

<?php
$urlparams = $this -> request -> query;
unset($urlparams['url']);
?>
<div class="component" id="collectibles-list-component">
	<div class="inside" >
		<?php echo $this -> element('flash'); ?>
		<div class="component-view">
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
			<div class="collectibles view stashable" data-toggle="modal-gallery" data-target="#modal-gallery">				
				<?php
				echo '<table class="table table-striped">';
				echo '<thead>';
				echo '<tr>';
				echo '<th> </th>';
				echo '<th>' . $this -> Paginator -> sort('name', 'Name') . '</th>';
				echo '<th>' . $this -> Paginator -> sort('Collectible.variant', 'Variant', array('escape' => false)) . '</th>';
				echo '<th>' . $this -> Paginator -> sort('Collectible.manufacture_id', 'Manufacturer', array('escape' => false)) . '</th>';
				echo '<th>' . $this -> Paginator -> sort('Collectible.license_id', 'Brand', array('escape' => false)) . '</th>';
				echo '<th>' . $this -> Paginator -> sort('Collectible.collectibletype_id', 'Platform', array('escape' => false)) . '</th>';
				echo '<th>' . $this -> Paginator -> sort('Collectible.specialized_type_id', 'Manufacturer Type', array('escape' => false)) . '</th>';
				echo '<th>' . $this -> Paginator -> sort('collectibles_user_count', 'Stash Count', array('escape' => false)) . '</th>';
				echo '<th>' . __('Actions') . '</th>';
				echo '</tr>';

				echo '</thead>';
				foreach ($collectibles as $collectible) {
					$collectibleJSON = json_encode($collectible['Collectible']);
					$collectibleJSON = htmlentities(str_replace(array("\'","'"), array("\\\'","\'"), $collectibleJSON));
					echo '<tr>';

					echo '<td><ul class="thumbnails"><li class="span1">';

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

					echo '</li></ul></td>';

					echo '<td>' . $collectible['Collectible']['name'];
					if ($collectible['Collectible']['exclusive']) {
						echo __(' - Exclusive');
					}
					echo '</td>';
					if ($collectible['Collectible']['variant']) {
						echo '<td><a href="/collectibles/view/' . $collectible['Collectible']['variant_collectible_id'] . '">' . __('Yes') . '</a></td>';
					} else {
						echo '<td>' . __('No') . ' </td>';
					}

					if (!empty($collectible['Manufacture']['title'])) {
						echo '<td>' . '<a href="/manufacturer/' . $collectible['Manufacture']['id'] . '/' . $collectible['Manufacture']['slug'] . '">' . $collectible['Manufacture']['title'] . '</a>' . '</td>';
					} else {
						echo '<td>Not Recorded</td>';
					}
					if (!empty($collectible['License']['name'])) {
						echo '<td>' . $collectible['License']['name'] . '</td>';
					} else {
						echo '<td>Not Recorded</td>';
					}
					echo '<td>' . $collectible['Collectibletype']['name'] . '</td>';
					if (isset($collectible['SpecializedType']) && !empty($collectible['SpecializedType'])) {
						echo '<td>' . $collectible['SpecializedType']['name'] . '</td>';
					} else {
						echo '<td> </td>';
					}
					if ($collectible['Collectible']['collectibles_user_count'] === '0') {
						echo '<td>' . $collectible['Collectible']['collectibles_user_count'] . '</td>';
					} else {
						echo '<td>' . $this -> Html -> link($collectible['Collectible']['collectibles_user_count'], array('admin' => false, 'action' => 'registry', 'controller' => 'collectibles_users', $collectible['Collectible']['id'])) . '</td>';
					}

					echo '<td>';
					echo '<div class="btn-group">';
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
					echo '</td>';

					echo '</tr>';
				}
			?>
				
				</table>
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
</div>