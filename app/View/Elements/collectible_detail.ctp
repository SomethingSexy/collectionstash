<?php
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
$this -> set('description_for_layout', 'Information and detail for ' .  $collectibleDetail['Collectible']['descriptionTitle']);
$this -> set('keywords_for_layout', $collectibleDetail['Manufacture']['title'] . ' ' . $collectibleDetail['Collectible']['name'] . ',' . $collectibleDetail['Collectible']['name'] . ',' . $collectibleDetail['Collectibletype']['name'] . ',' . $collectibleDetail['License']['name']);

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

echo $this -> Minify -> script('js/jquery.comments', array('inline' => false));
echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false));
echo $this -> Minify -> script('js/cs.stash', array('inline' => false));
echo $this -> Minify -> script('js/models/model.status', array('inline' => false));
echo $this -> Minify -> script('js/views/view.status', array('inline' => false));

echo $this -> Minify -> script('js/models/model.listing', array('inline' => false));
echo $this -> Minify -> script('js/collections/collection.listings', array('inline' => false));
echo $this -> Minify -> script('js/views/view.transactions', array('inline' => false));
echo $this -> Minify -> script('js/views/view.stash.add', array('inline' => false));
echo $this -> Minify -> script('js/models/model.collectible.user', array('inline' => false));
echo $this -> Minify -> script('js/models/model.collectible', array('inline' => false));
echo $this -> Minify -> script('js/pages/page.collectible.view', array('inline' => false));
echo $this -> Minify -> script('js/thirdparty/jquery.flot', array('inline' => false));
echo $this -> Minify -> script('js/thirdparty/jquery.flot.time', array('inline' => false));
?>


<div id="collectible-container" class="col-md-12 stashable">
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
						echo $this -> element('collectible_detail_upload', array('collectibleCore' => $collectibleDetail));
					}
					?>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="row">
				<div class="col-md-12">
					<?php if($collectibleDetail['Status']['id'] === '4' || ($collectibleDetail['Status']['id'] === '2' && $adminMode)) {?>
						<div class="btn-group actions pull-right">
							<?php
							// check to make sure we can show stash, depending on where this is being
							// rendered, make sure they are logged in and then make sure thay have permission
							if (isset($showAddStash) && $showAddStash && $isLoggedIn && $isStashable) {
								$collectibleJSON = json_encode($collectibleDetail['Collectible']);
								$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
								echo '<button title="Add to stash" class="btn btn-default btn-lg add-full-to-stash" data-stash-type="Default" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectibleDetail['Collectible']['id'] . '" href="javascript:void(0)"><img src="/img/icon/add_stash_link_25x25.png"/> Add to Stash</button>';
							}
							if (isset($showAddStash) && $showAddStash && $isLoggedIn && $isStashable) {
								echo '<button data-stash-type="Wishlist" data-collectible-id="' . $collectibleDetail['Collectible']['id'] . '" class="add-to-stash btn btn-default btn-lg" title="Add to Wishlist" href="#"><i class="icon-star"></i> Add to Wishlist</button>';
							}
							
							if (isset($isLoggedIn) && $isLoggedIn === true && !$adminMode) {
								$userSubscribed = 'false';
								if (array_key_exists($collectibleDetail['Collectible']['entity_type_id'], $subscriptions)) {
									$userSubscribed = 'true';
								}
								echo '<button id="subscribe"  data-subscribed="' . $userSubscribed . '" data-entity-type="stash" data-entity-type-id="' . $collectibleDetail['Collectible']['entity_type_id'] . '" class="btn btn-default btn-lg" href="#"><i class="icon-heart"></i> Watch</button>';
			
							}	
	
							if ($showWho) {
								echo '<a class="btn btn-default btn-lg" title="Registry" href="/collectibles_users/registry/' .  $collectibleDetail['Collectible']['id'] . '"><i class="icon-group"></i> Registry</a>';
							}
		
							if (isset($isLoggedIn) && $isLoggedIn === true) {
								if ($adminMode) {
									echo '<a class="btn btn-default btn-lg" title="Edit mode" href="/admin/collectibles/edit/' . $collectibleDetail['Collectible']['id'] . '"><i class="icon-pencil"></i> Edit</a>';
								} else if ($allowEdit) {
									echo '<a class="btn btn-default btn-lg" title="Edit mode" href="/collectibles/edit/' . $collectibleDetail['Collectible']['id'] . '"><i class="icon-pencil"></i> Edit</a>';
								}
			
							}
	
							if (isset($showHistory) && $showHistory) {
								//echo $this -> Html -> link('<i class="icon-briefcase"></i>', '/collectibles/history/' . $collectibleDetail['Collectible']['id'], array('title' => 'History', 'escape' => false, 'class' => 'btn'));
							}
							if (isset($showQuickAdd) && $showQuickAdd && $isLoggedIn && $allowVariantAdd) {
								echo '<a class="btn btn-default btn-lg" title="Add a varaint of this collectible." href="/collectibles/quickCreate/' .  $collectibleDetail['Collectible']['id'] . '/true"><i class="icon-plus"></i> Add Variant</a>';
							}
							?>	
						</div>
					<?php } ?>	
				</div>
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
							if ($collectibleDetail['Collectible']['official'] && !empty($collectibleDetail['Collectible']['manufacture_id'])) {
								echo 'Mass-Produced | ';
							} else {
								echo 'Custom | ';
							}
		
						}
		
						if ($collectibleDetail['Collectible']['official']) {
							echo 'Official';
						} else {
							echo 'Unofficial';
						}
						
						if (isset($collectibleDetail['Collectible']['variant']) && $collectibleDetail['Collectible']['variant']) {
							echo ' | <a href="/collectibles/view/' . $collectibleDetail['Collectible']['variant_collectible_id'] . '">Variant</a>';
						}
						
						if ($isLoggedIn && $userCounts) {
							foreach ($userCounts as $key => $value) {
								if ($value['type'] === 'Default') {
									echo ' | <span class="label">' . $value['count'] . ' in your Stash' . '</span>';
								} else {
									echo ' | <span class="label">' . $value['count'] . ' in your ' . $value['type'] . '</span>';
								}
		
							}
		
						}
						?>
						</span>
					</div>
				</div>
			</div>

			<div id="status-container" class="row">
	
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php
					if ($collectibleDetail['Collectible']['collectibletype_id'] === Configure::read('Settings.CollectibleTypes.Print')) {
						echo $this -> element('collectible_detail_artists', array('collectibleCore' => $collectibleDetail));
					}
					?>
					
					<?php 
					if($adminMode) {
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
					if (isset($showVariants) && $showVariants && !$collectibleDetail['Collectible']['custom'] && !$collectibleDetail['Collectible']['original']) {
						echo $this -> element('collectible_variant_list', array());
					}
					?>							

					<?php
					if ($showAttributes) {
						echo $this -> element('collectible_detail_attributes', array('collectibleCore' => $collectibleDetail, 'showEdit' => $showEdit, 'adminMode' => $adminMode));?>
						<script>
							$(function() {
								// If we are in admin mode, we need to pass that in to these methods so that they can
								// do specific things based on that

								$('span.popup', '.attributes-list').popover({
									placement : 'bottom',
									html : 'true',
									template : '<div class="popover" onmouseover="$(this).mouseleave(function() {$(this).hide(); });"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>'
								}).click(function(e) {
									e.preventDefault();
								}).mouseenter(function(e) {
									$(this).popover('show');
								});

							});
						</script>
				
					<?php } ?>								
					
			
				<?php if ($collectibleDetail['Status']['id'] === '4' && Configure::read('Settings.TransactionManager.enabled')){ ?>
					<div class="tab-pane" id="price">
						<div id="transactions">						
						
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
					<script>
						//lazy do doing here
						$(function() {
							$('#comments').comments();
						});
					</script>
					</div>
				</div>
			</div>
		</div>
			<?php } ?>
					<?php
		if ($adminMode) {
					?>
		<div class="row spacer">
			<div class="col-md-12">
				<div class="component" id="collectible-detail">
					<div class="inside">
						<div class="component-view">
				
				
				
				
							<?php echo $this -> Form -> create('Approval', array('url' => '/admin/collectibles/approve/' . $collectibleDetail['Collectible']['id'], 'id' => 'approval-form')); ?>
							<input id="approve-input" type="hidden" name="data[Approval][approve]" value="" />
							<fieldset class="approval-fields">
								<ul class="form-fields unstyled">
									<li>
										<div class="label-wrapper">
											<label for=""> <?php echo __('Notes')
												?></label>
										</div>
										<textarea rows="6" cols="30" name="data[Approval][notes]"></textarea>
									</li>
								</ul>
							</fieldset>
							</form>
							<div class="links">
								<button id="approval-button" class="btn btn-primary"><?php echo __('Approve'); ?></button>
								<button id="deny-button" class="btn"><?php echo __('Deny'); ?></button>
							</div>
							<script>
								//Eh move this out of here
								$('#approval-button').click(function() {
									$('#approve-input').val('true');
									$('#approval-form').submit();
								});
								$('#deny-button').click(function() {
									$('#approve-input').val('false');
									$('#approval-form').submit();
								});
				
							</script>
						
						</div>
					</div>
				</div>
			</div>
		</div>
			<?php } ?>	
	
</div>



<script>
	var collectibleStatus = {
	id : <?php echo $collectibleDetail['Collectible']['id']; ?>,
	status:<?php echo json_encode($collectibleDetail['Status']); ?>
	};
	var collectible =<?php echo json_encode($collectibleDetail['Collectible']); ?>;
	var listings = <?php echo json_encode($collectibleDetail['Listing']); ?>;

	var transactionsGraphData =<?php echo json_encode($transactionGraphData); ?>;
	var collectiblePriceData =<?php 
 if(isset($collectibleDetail['CollectiblePriceFact'])) {
 	echo json_encode($collectibleDetail['CollectiblePriceFact']);
 } else {
 	echo 'null';
 }?>;

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
	if ($allowStatusEdit) {
		echo 'var allowStatusEdit = true;';
	} else {
		echo 'var allowStatusEdit = false;';
	}
?></script>