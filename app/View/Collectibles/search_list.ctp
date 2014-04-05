<?php
echo $this -> Html -> script('thirdparty/uri', array('inline' => false));
echo $this -> Html -> script('views/view.stash.add', array('inline' => false));
echo $this -> Html -> script('views/view.filters', array('inline' => false));
echo $this -> Html -> script('models/model.collectible.user', array('inline' => false));
echo $this -> Html -> script('cs.stash', array('inline' => false));
?>

<?php
$urlparams = $this -> request -> query;
unset($urlparams['url']);
?>

<div id="collectibles-list-component" class="col-md-12">
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
		<div class="col-md-10">
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
				<div class="row spacer">
					<div class="col-md-12">
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('name', 'Name', array('class' => 'btn sort btn-default')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('Collectible.manufacture_id', 'Manufacturer', array('escape' => false, 'class' => 'btn sort btn-default')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('Collectible.license_id', 'Brand', array('escape' => false, 'class' => 'btn sort btn-default')); ?>
						</div>
						<div class="btn-group">
						<?php echo $this -> Paginator -> sort('Collectible.collectibletype_id', 'Platform', array('escape' => false, 'class' => 'btn sort btn-default')); ?>
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
						$fullCollectibleJSON = json_encode($collectible);
						$fullCollectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $fullCollectibleJSON));

						$collectibleJSON = json_encode($collectible['Collectible']);
						$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
						echo '<div class="spacer">';
						echo '<div class="col-md-12 collectible" data-collectible=\'' . $fullCollectibleJSON . '\'>';
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
						if ($isLoggedIn) {
							echo '<li><a title="Add to Stash" class="add-full-to-stash" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectible['Collectible']['id'] . '"  href="javascript:void(0)"><img src="/img/icon/add_stash_link_25x25.png"> Add to Stash</a></li>';
							echo '<li><a data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash" title="Add to Stash without being prompted to enter information" href="#"><img src="/img/icon/add_stash_link_25x25.png"> Quick Add to Stash</a></li>';
							echo '<li><a data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-wishlist" title="Add to Wish List" href="#"><i class="icon-star"></i> Add to Wish List</a></li>';
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
		<div class="col-md-2">
			<div id="fancy-filters">
				<button type="button" class="btn btn-default btn-lg btn-block filter manufacturer" data-title="Manufacturer Filter" data-container="body" data-toggle="popover" data-placement="left" data-html="true" data-content='<div class="manufacturer-typeahead"><input id="search-input-tools" type="text" class="form-control typeahead input-lg" autocomplete="off" placeholder="Start typing to see list"></div></div>'>Left</button>
  				<button type="button" class="btn btn-default btn-lg btn-block filter" data-title="Filter" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Middle</button>
  				<button type="button" class="btn btn-default btn-lg btn-block filter" data-title="Filter" data-container="body" data-toggle="popover" data-placement="left" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">Right</button>	
			</div>
<!-- 			<div id="sidebar-fixed-sticky-wrapper" class="sticky" style=""><div id="sidebar-fixed" class="" style="">
			    <div class="filter-block">
			    	<div class="form-item form-item-text ui-front" id="search-container">
			    		<span role="status" aria-live="polite" class="ui-helper-hidden-accessible">No search results.</span>
			    		<input name="search" type="text" class="form-text form-text form-search form-searchi ui-autocomplete-input" id="search" placeholder="Search..." autocomplete="off">
						<ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content ui-corner-all be-autosuggest-disabled ui-state-disabled" id="ui-id-1" tabindex="0" aria-disabled="true" style="display: none;"></ul>
					</div>   
				</div>

			    <div class="filter-block hide" id="related-tags">
			    	<div class="list-header">Related Tags</div>
			      	<div id="object-tags" class="cfix"></div>
			      	<div id="object-tags-see-all" style="display: none;">
			        	<span class="viewing-less-copy fake-link">See all <span class="tags-number"></span> tags</span>
			        	<span class="viewing-more-copy fake-link">See fewer tags</span>
			      	</div>
			    </div>
			    <div class="filter-block hide-wip">
			    	<div class="list-header">Filter By</div>
			      	<ul class="block-menu" id="filter-menu">
			        	<li class="filter">
			          		<div class="menu-item filter-schools discover-sprite">Schools</div>
			        	</li>
			        	<li class="filter filter-temp-last">
			          		<div class="menu-item filter-tools discover-sprite">Tools Used</div>
				        		<div class="sort-menu search-option search-option-no-confirm" style="top: -60px;">
				  
				  				<div class="search-option-header">Tools</div>

				 				<div class="search-option-content ui-front search-option-tools">
				    				<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
				    				<input id="search-input-tools" type="text" class="form-text form-text-normal ui-autocomplete-input" autocomplete="off" placeholder="Start typing to see list">
				  					<ul class="ui-autocomplete ui-front ui-menu ui-widget ui-widget-content ui-corner-all" id="ui-id-2" tabindex="0" style="display: none;"></ul>
				  				</div>
				  				<ul class="listselector_selections autocomplete_selections"></ul>			  
				  				<div class="search-option-nub"></div>
							</div>
						</li>
			        	<li class="filter hide-users">
			          		<div class="menu-item filter-color discover-sprite">Color</div>
			        	</li>
			      	</ul>

			      	<ul class="block-menu" id="gallery-menu">
			        	<li>
			          		<a href="/galleries" class="menu-item filter-arrow discover-sprite uppercase">
			            		Visit Galleries
			          		</a>
			        	</li>
			      	</ul>
			    </div>
			  </div>
			</div> -->

		</div>
<!-- 		<div class="col-md-5 collectible-detail">
			<div class="well" data-spy="affix" data-offset-top="200">
				<p class="text-center lead">Select a collectible to see more detail here!</p>
				
				
       		</div>	
		</div> -->
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
<?php
echo $this -> Html -> script('pages/page.collectible.search', array('inline' => true));
?>