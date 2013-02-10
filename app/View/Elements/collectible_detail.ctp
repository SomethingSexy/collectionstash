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
echo $this -> Minify -> script('js/cs.stash', array('inline' => false));
echo $this -> Html -> script('models/model.status', array('inline' => false));
echo $this -> Html -> script('views/view.status', array('inline' => false));
echo $this -> Html -> script('pages/page.collectible.view', array('inline' => false));
?>
<script>
						var collectibleStatus = {
	id : <?php echo $collectibleDetail['Collectible']['id']; ?>
		,
		status:
<?php echo json_encode($collectibleDetail['Status']); ?>};<?php
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

<div id="collectible-container" class="span12 stashable">
	<div class="row spacer">
		<div class="span8"><h2><?php echo $title; ?></h2></div>
		<?php if($collectibleDetail['Status']['id'] === '4' || ($collectibleDetail['Status']['id'] === '2' && $adminMode)) {?>
		<div class="span4">
			<div class="btn-group actions pull-right">
				<?php
				if (isset($showAddStash) && $showAddStash && $isLoggedIn) {
					echo '<a title="Add to stash" class="link add-stash-link" href="/collectibles_users/add/' . $collectibleDetail['Collectible']['id'] . '"><img src="/img/icon/add_stash_link_25x25.png"/></a>';
				}
				if (isset($isLoggedIn) && $isLoggedIn === true) {
					if ($adminMode) {
						echo '<a class="btn" title="Edit mode" href="/admin/collectibles/edit/' . $collectibleDetail['Collectible']['id'] . '"><i class="icon-pencil"></i></a>';
					} else {
						echo '<a class="btn" title="Edit mode" href="/collectibles/edit/' . $collectibleDetail['Collectible']['id'] . '"><i class="icon-pencil"></i></a>';
					}

				}

				if (isset($isLoggedIn) && $isLoggedIn === true && !$adminMode) {
					$userSubscribed = 'false';
					if (array_key_exists($collectibleDetail['Collectible']['entity_type_id'], $subscriptions)) {
						$userSubscribed = 'true';
					}
					echo '<a  id="subscribe"  data-subscribed="' . $userSubscribed . '" data-entity-type="stash" data-entity-type-id="' . $collectibleDetail['Collectible']['entity_type_id'] . '" class="btn" href="#"><i class="icon-heart"></i></a>';
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
				if (isset($showQuickAdd) && $showQuickAdd && $isLoggedIn) {
					echo $this -> Html -> link('<i class="icon-plus"></i>', '/collectibles/quickCreate/' . $collectibleDetail['Collectible']['id'] . '/true', array('title' => __('Add a varaint of this collectible.', true), 'escape' => false, 'class' => 'btn btn-warning'));
				}
				?>	
			</div>
		</div>
		<?php } ?>
	</div>

	<div id="status-container" class="row spacer">
	
	</div>

	<div class="row spacer">
		<div class="span4">
			<?php
			if ($showImage) {
				echo $this -> element('collectible_detail_upload', array('collectibleCore' => $collectibleDetail));
			}
			?>
		</div>
		<div class="span8">
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
		
		</div>
	</div>	
		
	<div class="row">	
		<div class="span12">	
			<?php
			if ($showAttributes) {
				echo $this -> element('collectible_detail_attributes', array('collectibleCore' => $collectibleDetail, 'showEdit' => $showEdit, 'adminMode' => $adminMode));?>
				<script>
					$(function() {
						// If we are in admin mode, we need to pass that in to these methods so that they can
						// do specific things based on that

						$('.attributes > table > tbody> tr > td > span.popup').popover({
							placement : 'bottom',
							html : 'true',
							trigger : 'click'
						});

					});
				</script>
		
			<?php } ?>
		</div>
	</div>	
	<div class="row">
		
		<div class="span12">
			
			<?php
			if (isset($showVariants) && $showVariants) {
				echo $this -> element('collectible_variant_list', array());
			}
			?>
			
		</div>
		
	</div>
	
</div>



<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-view">
			<?php
			if(isset($isLoggedIn) && $isLoggedIn === true)
			{
			//Show something if logged in?
			?>

			<?php } else { ?>
			<div class="helpful-hint-message alert alert-info">
				<?php echo __('See something that is inaccurate? Login or register to help us maintain the most accurate collectible database.'); ?>
			</div>
			<?php } ?>
			<?php ?>
			<?php if(isset($showComments) && $showComments) {
			?>
			<div id="comments" class="comments-container" data-entity-type-id="<?php echo $collectibleDetail['Collectible']['entity_type_id']; ?>" data-type="collectible" data-typeID="<?php echo $collectibleDetail['Collectible']['id']; ?>"></div>
			<script>
				//lazy do doing here
				$(function() {
					$('#comments').comments();
				});
			</script>
			<?php } ?>

			<?php
if ($adminMode) {
			?>

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
			<?php } ?>
		</div>
	</div>
</div>