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
$this -> set('description_for_layout', $collectibleDetail['Manufacture']['title'] . ' ' . $collectibleDetail['Collectible']['name']);
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
echo $this -> Html -> script('cs.stash', array('inline' => false));
echo $this -> Minify -> script('js/models/model.status', array('inline' => false));
echo $this -> Minify -> script('js/views/view.status', array('inline' => false));

echo $this -> Minify -> script('js/models/model.listing', array('inline' => false));
echo $this -> Minify -> script('js/collections/collection.listings', array('inline' => false));
echo $this -> Minify -> script('js/views/view.transactions', array('inline' => false));
echo $this -> Html -> script('views/view.stash.add', array('inline' => false));
echo $this -> Html -> script('models/model.collectible.user', array('inline' => false));
echo $this -> Html -> script('models/model.collectible', array('inline' => false));
echo $this -> Html -> script('pages/page.collectible.view', array('inline' => false));
?>


<div id="collectible-container" class="span12 stashable">
	<div class="row-fluid">
		<div class="span8">
			<div class="page-header">
				<h1 class="title"><?php echo $title; ?></h1>
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
		<div class="span4">
			<?php if($collectibleDetail['Status']['id'] === '4' || ($collectibleDetail['Status']['id'] === '2' && $adminMode)) {?>
			<div class="btn-group actions pull-right">
				<?php
				// check to make sure we can show stash, depending on where this is being
				// rendered, make sure they are logged in and then make sure thay have permission
				if (isset($showAddStash) && $showAddStash && $isLoggedIn && $isStashable) {
					$collectibleJSON = json_encode($collectibleDetail['Collectible']);
					$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
					echo '<a title="Add to stash" class="link add-full-to-stash" data-stash-type="Default" data-collectible=\'' . $collectibleJSON . '\' data-collectible-id="' . $collectibleDetail['Collectible']['id'] . '" href="javascript:void(0)"><img src="/img/icon/add_stash_link_25x25.png"/></a>';
				}
				if (isset($isLoggedIn) && $isLoggedIn === true) {
					if ($adminMode) {
						echo '<a class="btn" title="Edit mode" href="/admin/collectibles/edit/' . $collectibleDetail['Collectible']['id'] . '"><i class="icon-pencil"></i></a>';
					} else if ($allowEdit) {
						echo '<a class="btn" title="Edit mode" href="/collectibles/edit/' . $collectibleDetail['Collectible']['id'] . '"><i class="icon-pencil"></i></a>';
					}

				}

				if (isset($isLoggedIn) && $isLoggedIn === true && !$adminMode) {
					$userSubscribed = 'false';
					if (array_key_exists($collectibleDetail['Collectible']['entity_type_id'], $subscriptions)) {
						$userSubscribed = 'true';
					}
					echo '<a  id="subscribe"  data-subscribed="' . $userSubscribed . '" data-entity-type="stash" data-entity-type-id="' . $collectibleDetail['Collectible']['entity_type_id'] . '" class="btn" href="#"><i class="icon-heart"></i></a>';

				}

				if (isset($showAddStash) && $showAddStash && $isLoggedIn && $isStashable) {
					echo '<a data-stash-type="Wishlist" data-collectible-id="' . $collectibleDetail['Collectible']['id'] . '" class="add-to-stash btn" title="Add to Wishlist" href="#"><i class="icon-star"></i></a>';
				}
				?>
				<?php
				if ($showWho) {
					echo $this -> Html -> link('<i class="icon-group"></i>', '/collectibles_users/registry/' . $collectibleDetail['Collectible']['id'], array('title' => 'Registry', 'escape' => false, 'class' => 'btn'));
				}
				if (isset($showHistory) && $showHistory) {
					//echo $this -> Html -> link('<i class="icon-briefcase"></i>', '/collectibles/history/' . $collectibleDetail['Collectible']['id'], array('title' => 'History', 'escape' => false, 'class' => 'btn'));
				}
				if (isset($showQuickAdd) && $showQuickAdd && $isLoggedIn && $allowVariantAdd) {
					echo $this -> Html -> link('<i class="icon-plus"></i>', '/collectibles/quickCreate/' . $collectibleDetail['Collectible']['id'] . '/true', array('title' => __('Add a varaint of this collectible.', true), 'escape' => false, 'class' => 'btn btn-warning'));
				}
				?>	
			</div>
		<?php } ?>
		</div>
	</div>
	<div class="row-fluid spacer">
		<div class="span4">
			<?php
			if ($showImage) {
				echo $this -> element('collectible_detail_upload', array('collectibleCore' => $collectibleDetail));
			}
			?>
		</div>
		<div class="span8">
			<div id="status-container" class="row-fluid">
	
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div class="row-fluid">
						<div class="span6">
							<?php
							if ($collectibleDetail['Collectible']['collectibletype_id'] === Configure::read('Settings.CollectibleTypes.Print')) {
								echo $this -> element('collectible_detail_artists', array('collectibleCore' => $collectibleDetail));
							}
							?>
							
							<?php echo $this -> element('collectible_detail_core', array('showEdit' => $showEdit, 'editImageUrl' => $editImageUrl, 'editManufactureUrl' => $editManufactureUrl, 'showStatistics' => $showStatistics, 'collectibleCore' => $collectibleDetail, 'showAddedBy' => $showAddedBy, 'showAddedDate' => $showAddedDate, 'adminMode' => $adminMode, 'showTags' => $showTags)); ?>
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
						</div>
						<div class="span6" <?php
						if ($collectibleDetail['Status']['id'] === '4' && Configure::read('Settings.TransactionManager.enabled')) {  echo 'id="transactions"';
						}
 ?>>

						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
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
						</div>						
					</div>
				</div>
			</div>
		</div>
	</div>	
	
	<?php if(isset($showComments) && $showComments) {?>
		<div class="row-fluid spacer">
			<div class="span8 offset4">
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
</div>


			<?php
if ($adminMode) {
			?>
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
	<?php } ?>
<script>
		var collectibleStatus = {
	id : <?php echo $collectibleDetail['Collectible']['id']; ?>
		,
		status:
<?php echo json_encode($collectibleDetail['Status']); ?>
	};
	var collectible =
 <?php echo json_encode($collectibleDetail['Collectible']); ?>
	;
	var listings =
 <?php echo json_encode($collectibleDetail['Listing']); ?>
	;

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