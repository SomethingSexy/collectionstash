<?php
if (!isset($adminMode)) {
	$adminMode = false;
}
if (isset($setPageTitle) && $setPageTitle) {
	$pageTitle = '';
	if (!empty($collectibleDetail['Manufacture']['title'])) {
		$pageTitle .= $collectibleDetail['Manufacture']['title'] . ' - ';
	}

	if (!empty($collectibleDetail['License']['name'])) {
		$pageTitle .= $collectibleDetail['License']['name'] . ' - ';
	}

	$pageTitle .= $collectibleDetail['Collectible']['name'];

	$this -> set("title_for_layout", $pageTitle);
}
if (!$adminMode) {
	$this -> set('description_for_layout', 'Information and detail for ' . $collectibleDetail['Collectible']['descriptionTitle']);
	$this -> set('keywords_for_layout', $collectibleDetail['Manufacture']['title'] . ' ' . $collectibleDetail['Collectible']['name'] . ',' . $collectibleDetail['Collectible']['name'] . ',' . $collectibleDetail['Collectibletype']['name'] . ',' . $collectibleDetail['License']['name']);
}
if (!isset($showEdit)) {
	$showEdit = false;
}
if (!isset($editImageUrl)) {
	$editImageUrl = false;
}
if (!isset($editManufactureUrl)) {
	$editManufactureUrl = '';
}
if (!isset($showAddedBy)) {
	$showAddedBy = false;
}
if (!isset($showAddedDate)) {
	$showAddedDate = false;
}
if (!isset($adminMode)) {
	$adminMode = false;
}

if (!isset($showTags)) {
	$showTags = false;
}
if (!isset($showImage)) {
	$showImage = true;
}
if (!isset($showAttributes)) {
	$showAttributes = true;
}
if (!isset($showStatus)) {
	$showStatus = false;
}
if (!isset($allowStatusEdit)) {
	$allowStatusEdit = false;
}

//echo $this -> Minify -> script('models/model.status', array('inline' => false));
//echo $this -> Minify -> script('views/view.status', array('inline' => false));
echo $this -> Html -> script('pages/page.collectible.view', array('inline' => false));
?>


<div id="collectible-container" class="container-fluid">
<div class="row">

<div class="col-md-12 stashable">
	<?php
	if (!isset($isLoggedIn) || !$isLoggedIn && $collectibleDetail['Status']['id'] === '4' ) {
	?>
	<div class="row">
		<div class="col-md-7">
			<div class="alert alert-info">
  				<strong>Hey! Listen!</strong>
  				Do you own this collectible?  <a href="/users/login">Log in</a> or <a href="/users/register">register</a> to add this collectible to your Stash! You can catalog your entire collection with Collection Stash.
    		</div>			
		</div>
	</div>
	<?php } ?>
	
	<div class="row spacer">
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-12">	
					<?php
					if ($showImage) {
						if(!isset($userUploads)){
							$userUploads = array();
						}
						echo $this -> element('collectible_detail_upload', array('collectibleCore' => $collectibleDetail, 'userUploads' => $userUploads));
					}
					?>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="row">
				<div class="col-md-12">
					<?php if($collectibleDetail['Status']['id'] === '4' || ($collectibleDetail['Status']['id'] === '2' && $adminMode)) {?>
						<nav class="navbar yamm navbar-inverse" role="navigation">
						  <div class="navbar-header">
						    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						      <span class="sr-only">Toggle navigation</span>
						      <span class="fa fa-bars"></span>
						    </button>
						  </div>
							<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					          <ul class="nav navbar-nav">
								<?php
								// check to make sure we can show stash, depending on where this is being
								// rendered, make sure they are logged in and then make sure thay have permission
								if (isset($showAddStash) && $showAddStash && $isLoggedIn && $isStashable) {
									$collectibleJSON = json_encode($collectibleDetail['Collectible']);
									$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));

									$stashCount = isset($collectibleUserCount) ? $collectibleUserCount : 0;
									$wishlistCount = isset($collectibleWishListCount) ? $collectibleWishListCount : 0;

									echo '<li><a title="Add to stash" class="add-full-to-stash" data-stash-count="' . $stashCount .'" data-wishlist-count="' . $wishlistCount .'" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectibleDetail['Collectible']['id'] . '" href="javascript:void(0)"><img src="/img/icon/add_stash_link_16x16.png"/> Add to Stash</a></li>';
								}
								if (isset($showAddStash) && $showAddStash && $isLoggedIn && $isStashable) {
									echo '<li><a data-collectible-id="' . $collectibleDetail['Collectible']['id'] . '" class="add-to-wishlist" title="Add to Wish List" href="#"><i class="fa fa-star"></i> Add to Wish List</a></li>';
								}
								// no need to show this in admin mode
								if(!$adminMode) {
									echo '<li class="dropdown">';
									echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-share"></i> Share <b class="caret"></b></a>';
									echo '<ul class="dropdown-menu">';
									echo '<li>';
									echo '<div class="yamm-content">';
									echo '<div class="row">';
									echo '<div class="col-sm-12">';
									echo '<h4>Twitter</h4>';
									echo '<a href="https://twitter.com/share" class="twitter-share-button" data-size="large" data-lang="en" data-count="none"  data-via="' . Configure::read('Settings.Twitter.name') . '" data-text="' . $collectibleDetail['Collectible']['displayTitle'] . '" data-url="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectibleDetail['Collectible']['id'] . '/' . $collectibleDetail['Collectible']['slugField'] . '">Tweet</a>';
									echo '<hr>';
									echo '<h4>Facebook</h4>';
									echo '<div class="fb-share-button" data-href="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectibleDetail['Collectible']['id'] . '/' . $collectibleDetail['Collectible']['slugField'] . '" data-type="button_count"></div>';
									echo '<hr>';
									echo '<h4>Direct</h4>';
									echo '<form>';
									echo '<div class="form-group">';
									echo '<div class="input-group">';
									echo '<input type="text" readonly="readonly" class="form-control selectable" name="" value="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectibleDetail['Collectible']['id'] . '/' . $collectibleDetail['Collectible']['slugField'] . '">';
									echo '<span class="input-group-btn"><button id="copy-to-clipboard-direct" title="Copied!" data-clipboard-text="http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectibleDetail['Collectible']['id'] . '/' . $collectibleDetail['Collectible']['slugField'] . '" class="btn btn-default btn-copy" type="button">Copy to clipboard</button></span>';
									echo '</div>';
									echo '</div>';
									echo '</form>';
									echo '<hr>';
									
									$bbCode = '[URL=\'http://' . env('SERVER_NAME') . '/' . $collectibleDetail['Collectible']['id'] . '/collectibles/view/' . $collectibleDetail['Collectible']['slugField'] . '\']' . $collectibleDetail['Collectible']['displayTitle'] . '[/URL]';
									
									echo '<h4>BBCode</h4>';
									echo '<form>';
									echo '<div class="form-group">';
									echo '<div class="input-group">';
									echo '<input type="text" readonly="readonly" class="form-control selectable" name="" value="' . $bbCode . '">';
									echo '<span class="input-group-btn"><button id="copy-to-clipboard-bbcode" title="Copied!" data-clipboard-text="' . $bbCode . '" class="btn btn-default btn-copy" type="button">Copy to clipboard</button></span>';
									echo '</div>';
									echo '</div>';
									echo '</form>';
									echo '<hr>';
									$primaryUploadURL = null;
									if (!empty($collectibleDetail['CollectiblesUpload'])) {
										foreach ($collectibleDetail['CollectiblesUpload'] as $key => $upload) {
											if ($upload['primary']) {
												$primaryUploadURL = $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true));
											}
										}
									}
	
									echo '<h4>BBCode with image</h4>';
									echo '<form>';
									echo '<div class="form-group">';
									echo '<div class="input-group">';
									$bbCodeImage = $bbCode;
							
									if (!is_null($primaryUploadURL)) {
										$bbCodeImage = '[URL=\'http://' . env('SERVER_NAME') . '/collectibles/view/' . $collectibleDetail['Collectible']['id'] . '/' . $collectibleDetail['Collectible']['slugField'] . '\'][IMG]http://' . env('SERVER_NAME') . $primaryUploadURL . '[/IMG][/URL]';
									}
	
									echo '<input type="text" readonly="readonly" class="form-control selectable" name="" value="' . $bbCodeImage . '">';
									echo '<span class="input-group-btn"><button id="copy-to-clipboard-bbcodeimage" title="Copied!" data-clipboard-text="' . $bbCodeImage . '" class="btn btn-default btn-copy" type="button">Copy to clipboard</button></span>';
									echo '</div>';
									echo '</div>';
									echo '</form>';
									echo '<hr>';
									echo '</div>';
									echo '</div>';
									echo '</div>';
									echo '</li>';
									echo '</ul>';
									echo '</li>';									
								}

								if (isset($isLoggedIn) && $isLoggedIn === true && !$adminMode) {
									$userSubscribed = $isFavorited ? 'true' : 'false';

									echo '<li><a id="subscribe"  data-subscribed="' . $userSubscribed . '" data-type="collectible" data-type-id="' . $collectibleDetail['Collectible']['id'] . '" class="" href="#"><i class="fa fa-heart"></i> Favorite</a></li>';

								}

								if ($showWho) {
									echo '<li><a class="" title="Registry" href="/collectibles_users/registry/' . $collectibleDetail['Collectible']['id'] . '"><i class="fa fa-group"></i> Registry</a></li>';
								}

								if (isset($isLoggedIn) && $isLoggedIn === true) {
									echo '<li><a class="" title="Edit mode" href="/collectibles/edit/' . $collectibleDetail['Collectible']['id'] . '"><i class="fa fa-pencil"></i> Edit</a></li>';
								}

								if (isset($showQuickAdd) && $showQuickAdd && $isLoggedIn && $allowVariantAdd) {
									echo '<li><a class="" title="Add a varaint of this collectible." href="/collectibles/quickCreate/' . $collectibleDetail['Collectible']['id'] . '/true"><i class="fa fa-plus"></i> Add Variant</a></li>';
								}
								?>	
					          </ul>
				          </div>
				        </nav>
						
					<?php } ?>	
				</div>
			</div>
			<div id="status-container" class="row spacer">
	
			</div>			
			<div class="row">
				
				
				<div class="col-md-12">
					<div class="page-header">
						<h1 class="title"><?php echo $title;
						if (isset($collectibleDetail['Collectible']['exclusive']) && $collectibleDetail['Collectible']['exclusive']) {
							echo __(' - Exclusive');
						}
							?>
							
						</h1>
						<span>
						<?php
						// maybe collectible type here?
						echo 'Platform: ' . $collectibleDetail['Collectibletype']['name'] . ' | ';

						// if it has a manufacturer display that first
						if (!empty($collectibleDetail['Collectible']['manufacture_id'])) {
							echo 'Manufacturer: <a href="/manufacturer/' . $collectibleDetail['Manufacture']['id'] . '/' . $collectibleDetail['Manufacture']['slug'] . '">' . $collectibleDetail['Manufacture']['title'] . '</a> | ';
						}
						// just grab the first artist for now
						if (!empty($collectibleDetail['ArtistsCollectible']) && !$collectibleDetail['Collectible']['custom']) {
							echo 'Artist: ' . $this -> Html -> link($collectibleDetail['ArtistsCollectible'][0]['Artist']['name'], array('admin' => false, 'controller' => 'artists', 'action' => 'index', $collectibleDetail['ArtistsCollectible'][0]['Artist']['id'], $collectibleDetail['ArtistsCollectible'][0]['Artist']['slug'])) . ' | ';
						} else if ($collectibleDetail['Collectible']['custom']) {
							if (!$collectibleDetail['User']['admin']) {
								echo 'Created By: ' . $this -> Html -> link($collectibleDetail['User']['username'], array('admin' => false, 'controller' => 'stashs', 'action' => 'view', $collectibleDetail['User']['username'])) . ' | ';
							} else {
								echo 'Created By: ' . $collectibleDetail['User']['username'] . ' | ';
							}
						}
						if ($collectibleDetail['Collectible']['custom']) {
							echo 'Custom | ';
						} else if ($collectibleDetail['Collectible']['original']) {
							echo 'Original | ';
						} else {
							// if it not a custom and not original it is mass-produced
							if ($collectibleDetail['Collectible']['official']) {
								//if ($collectibleDetail['Collectible']['official'] && !empty($collectibleDetail['Collectible']['manufacture_id'])) {
								echo 'Mass-Produced |';
								//} else {
								// I don't think this should be set here, what about if it is an art print offered
								// by the artist but there is no manufacturer? and it is official
								//echo 'Custom | ';
								//	}
							
							} else {
								echo 'Custom | ';
							}
						}

						// Then regardless of the logic above, mark official vs unofficial
						if ($collectibleDetail['Collectible']['official']) {
							echo ' Official';
						} else {
							echo ' Unofficial';
						}

						if (isset($collectibleDetail['Collectible']['variant']) && $collectibleDetail['Collectible']['variant']) {
							echo ' | <a href="/collectibles/view/' . $collectibleDetail['Collectible']['variant_collectible_id'] . '">Variant</a>';
						}

						if ($isLoggedIn && !$adminMode) {
							echo ' | <span class="label">' . $collectibleUserCount . ' in your Stash' . '</span>';
							echo ' | <span class="label">' . $collectibleWishListCount . ' in your Wish List</span>';
						}
						?>
						</span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
						<?php
						if ($collectibleDetail['Collectible']['collectibletype_id'] === Configure::read('Settings.CollectibleTypes.Print')) {
							echo $this -> element('collectible_detail_artists', array('collectibleCore' => $collectibleDetail));
						}
						?>
						
						<?php
							if ($adminMode) {
								echo $this -> element('admin_collectible_detail_core', array('showEdit' => $showEdit, 'editImageUrl' => $editImageUrl, 'editManufactureUrl' => $editManufactureUrl, 'showStatistics' => $showStatistics, 'collectibleCore' => $collectibleDetail, 'showAddedBy' => $showAddedBy, 'showAddedDate' => $showAddedDate, 'adminMode' => $adminMode, 'showTags' => $showTags));
							} else {
								echo $this -> element('collectible_detail_core', array('showEdit' => $showEdit, 'editImageUrl' => $editImageUrl, 'editManufactureUrl' => $editManufactureUrl, 'showStatistics' => $showStatistics, 'collectibleCore' => $collectibleDetail, 'showAddedBy' => $showAddedBy, 'showAddedDate' => $showAddedDate, 'adminMode' => $adminMode, 'showTags' => $showTags));
							}
						 ?>
						<?php
						if ($collectibleDetail['Collectible']['collectibletype_id'] !== Configure::read('Settings.CollectibleTypes.Print')) {
							echo $this -> element('collectible_detail_artists', array('collectibleCore' => $collectibleDetail));
						}
						?>	
						<?php
						if (isset($showTags) && $showTags === true) {
							echo $this -> element('collectible_detail_tags', array('collectibleCore' => $collectibleDetail, 'showEdit' => $showEdit, 'adminMode' => $adminMode));
						}
						?>
						<?php
						if (isset($showVariants) && $showVariants && !$collectibleDetail['Collectible']['custom'] && !$collectibleDetail['Collectible']['original']) {
							echo $this -> element('collectible_variant_list', array());
						}
						?>		
						<?php
						if ($showAttributes) {
							echo $this -> element('collectible_detail_attributes', array('collectibleCore' => $collectibleDetail, 'showEdit' => $showEdit, 'adminMode' => $adminMode));?>
						<?php } ?>								
						
				
					<?php if ($collectibleDetail['Status']['id'] === '4' && Configure::read('Settings.TransactionManager.enabled')){ ?>
						<div class="tab-pane" id="price">
							<div id="transactions">						
								<?php echo $this -> element('collectible_listings', array('collectibleDetail' => $collectibleDetail, 'allowAddListing' => $isLoggedIn, 'allowDeleteListing' => $isUserAdmin)); ?>
							</div>	
							<?php if(!empty($transactionGraphData)) { ?>
							<div class="graph-container">
								<div id="holder" style="width:850px;height:450px">
	
								</div>			    	
						    </div>	
						    <?php } ?>					 	
						</div>							
					<?php } ?>
					
				</div>
			</div>
		</div>
	</div>	
	
	<?php if(isset($showComments) && $showComments) {?>
		<div class="row spacer">
			<div class="col-md-12">
			<div class="widget">
				<div class="widget-content">
					<div id="comments" class="comments-container" data-entity-type-id="<?php echo $collectibleDetail['Collectible']['entity_type_id']; ?>" data-type="collectible" data-typeID="<?php echo $collectibleDetail['Collectible']['id']; ?>"></div>
				</div>
				</div>
			</div>
		</div>
			<?php } ?>
</div>
</div>
</div>


<script>
var collectibleStatus = {
	id : <?php echo $collectibleDetail['Collectible']['id']; ?>,
	status:<?php echo json_encode($collectibleDetail['Status']); ?>
};
var collectible = <?php echo json_encode($collectibleDetail['Collectible']); ?>;

<?php 
if(isset($transactionGraphData)){
	echo 'var transactionsGraphData = ' . json_encode($transactionGraphData) .';';
}
?>
 <?php
if ($isUserAdmin) {
	echo 'var allowDeleteListing = true;';
} else {
	echo 'var allowDeleteListing = false;';
}
?><?php
if ($isLoggedIn) {
	echo 'var allowAddListing = true;';
} else {
	echo 'var allowAddListing = false;';
}
?><?php
if ($showStatus) {
	echo 'var showStatus = true;';
} else {
	echo 'var showStatus = false;';
}
?><?php
if ($adminMode) {
	echo 'var showApproval = true;';
} else {
	echo 'var showApproval = false;';
}
?><?php
if ($allowStatusEdit) {
	echo 'var allowStatusEdit = true;';
} else {
	echo 'var allowStatusEdit = false;';
}
?></script>
<?php

	echo $this -> Html -> scriptBlock('var rawComments = ' .  json_encode($comments) . ';', array('inline' => false));

?>
<?php echo $this -> Html -> scriptBlock('var rawPermissions = ' .  json_encode($permissions) . ';', array('inline' => false));?>
