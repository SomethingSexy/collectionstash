<?php echo $this -> Minify -> script('js/jquery.comments', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/cs.subscribe', array('inline' => false)); ?>
<?php
if (isset($setPageTitle) && $setPageTitle) {
	$this -> set("title_for_layout", $collectibleDetail['Manufacture']['title'] . ' - ' . $collectibleDetail['License']['name'] . ' - ' . $collectibleDetail['Collectible']['name']);
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
?>
<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $title; ?></h2>
			<div class="btn-group actions">
				<?php
				if (isset($showAddStash) && $showAddStash && $isLoggedIn) {
					echo '<a title="Add to stash" class="link add-stash-link" href="/collectibles_users/add/' . $collectibleDetail['Collectible']['id'] . '"><img src="/img/icon/add_stash_link_25x25.png"/></a>';
				}
				if (isset($isLoggedIn) && $isLoggedIn === true && !$adminMode) {
					$userSubscribed = 'false';
					if (array_key_exists($collectibleDetail['Collectible']['entity_type_id'], $subscriptions)) {
						$userSubscribed = 'true';
					}
					echo '<a  id="subscribe"  data-subscribed="' . $userSubscribed . '" data-entity-type="stash" data-entity-type-id="' . $collectibleDetail['Collectible']['entity_type_id'] . '" class="btn" href="#"><i class="icon-heart"></i></a>';
				}
				
				?>
				<?php
				if ($showWho) {
					echo $this -> Html -> link('<i class="icon-group"></i>', '/collectibles_users/registry/' . $collectibleDetail['Collectible']['id'], array('title' => 'Registry', 'escape' => false, 'class' => 'btn'));
				}
				if (isset($showHistory) && $showHistory) {
					echo $this -> Html -> link('<i class="icon-briefcase"></i>', '/collectibles/history/' . $collectibleDetail['Collectible']['id'], array('title' => 'History', 'escape' => false, 'class' => 'btn'));
				}
				if (isset($showQuickAdd) && $showQuickAdd && $isLoggedIn) {
					if ($collectibleDetail['Collectible']['variant']) {
						echo $this -> Html -> link('<i class="icon-plus"></i>', '/collectibles/quickAdd/' . $collectibleDetail['Collectible']['id'] . '/false/', array('title' => __('Add a similar variant collectible type with the same manufacturer.', true), 'escape' => false, 'class'=>'btn'));
					} else {
						echo $this -> Html -> link('<i class="icon-plus"></i>', '/collectibles/quickAdd/' . $collectibleDetail['Collectible']['id'] . '/false/', array('title' => __('Add a similar collectible type with the same manufacturer.', true), 'escape' => false, 'class'=>'btn'));
					}
					if (!$collectibleDetail['Collectible']['variant']) {
						echo $this -> Html -> link('<i class="icon-plus"></i>', '/collectibles/quickAdd/' . $collectibleDetail['Collectible']['id'] . '/true', array('title' => __('Add a varaint of this collectible.', true), 'escape' => false, 'class'=> 'btn btn-warning'));
					}
				}
				?>
			</div>
		</div>
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
			<?php
			echo $this -> element('collectible_detail_core', array('showEdit' => $showEdit, 'editImageUrl' => $editImageUrl, 'editManufactureUrl' => $editManufactureUrl, 'showStatistics' => $showStatistics, 'collectibleCore' => $collectibleDetail, 'showAddedBy' => $showAddedBy, 'showAddedDate' => $showAddedDate, 'adminMode' => $adminMode, 'showTags' => $showTags));
			?>
			<?php
			if (isset($showVariants) && $showVariants) {
				echo $this -> element('collectible_variant_list', array());
			}
			?>
			<?php if(isset($showComments) && $showComments) {
			?>
			<div id="comments" class="comments-container" data-entity-type-id="<?php echo $collectibleDetail['Collectible']['entity_type_id']; ?>" data-type="collectible" data-typeID="<?php echo $collectibleDetail['Collectible']['id']; ?>"></div>
			<script>
				//lazy do doing here
				$(function(){
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
				<button id="approval-button" class="btn btn-primary"><?php echo __('Approve');?></button>
				<button id="deny-button" class="btn"><?php echo __('Deny');?></button>
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