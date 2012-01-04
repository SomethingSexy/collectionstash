<?php
if (isset($setPageTitle) && $setPageTitle) {
	$this -> set("title_for_layout", $collectibleDetail['Manufacture']['title'] . ' - ' .$collectibleDetail['License']['name']. ' - '. $collectibleDetail['Collectible']['name']);
}
$this -> set('description_for_layout', $collectibleDetail['Manufacture']['title'] . ' ' . $collectibleDetail['Collectible']['name']);
$this -> set('keywords_for_layout', $collectibleDetail['Manufacture']['title'] . ' ' . $collectibleDetail['Collectible']['name'] . ',' . $collectibleDetail['Collectible']['name'] . ',' . $collectibleDetail['Collectibletype']['name'] . ',' . $collectibleDetail['License']['name']);
?>
<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $title;?></h2>
			<div class="actions">
				<ul>
					<?php
					if (isset($showAddStash) && $showAddStash && $isLoggedIn) {
						echo '<li><a title="Add to stash" class="link add-stash-link" href="/collectiblesUser/add/' . $collectibleDetail['Collectible']['id'] . '"><img src="/img/icon/add_stash_link.png"/></a></li>';
					}
					?>
				</ul>
			</div>
		</div>
		<div class="component-view">
			<div class="collectible links">
				<?php
				if ($showWho) {
					echo $this -> Html -> link('Registry', array('admin' => false, 'controller' => 'collectibles_user', 'action' => 'registry', $collectibleDetail['Collectible']['id']));
				}
				if (isset($showHistory) && $showHistory) {
					echo $this -> Html -> link('History', array('action' => 'history', $collectibleDetail['Collectible']['id']));
				}
				if (isset($showQuickAdd) && $showQuickAdd && $isLoggedIn) {
					if ($collectibleDetail['Collectible']['variant']) {
						echo $this -> Html -> link('Add Similar', '/collectibles/quickAdd/' . $collectibleDetail['Collectible']['id'] . '/false/', array('title' => __('Add a similar variant collectible type with the same manufacturer.', true)));
					} else {
						echo $this -> Html -> link('Add Similar', '/collectibles/quickAdd/' . $collectibleDetail['Collectible']['id'] . '/false', array('title' => __('Add a similar collectible type with the same manufacturer.', true)));

					}
					if (!$collectibleDetail['Collectible']['variant']) {
						echo $this -> Html -> link('Add Variant', '/collectibles/quickAdd/' . $collectibleDetail['Collectible']['id'] . '/true', array('title' => __('Add a varaint of this collectible.', true)));
					}
				}
				?>
			</div>
			<?php
			if (isset($collectibleDetail['CollectiblesTag']) && !empty($collectibleDetail['CollectiblesTag'])) {
				echo '<div class="collectible tags">';
				echo '<ul class="tag-list">';
				foreach ($collectibleDetail['CollectiblesTag'] as $tag) {
					echo '<li class="tag">';
					echo '<a href="/collectibles/search/?t='.$tag['Tag']['id'].'"';
					echo '>'.$tag['Tag']['tag'].'</a>';
					echo '</li>';
				}
				echo '</ul>';
				echo '</div>';
			}
			?>
			<?php
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
			echo $this -> element('collectible_detail_core', array('showEdit' => $showEdit, 'editImageUrl' => $editImageUrl, 'editManufactureUrl' => $editManufactureUrl, 'showStatistics' => $showStatistics, 'collectibleCore' => $collectibleDetail, 'showAddedBy' => $showAddedBy, 'showAddedDate' => $showAddedDate, 'adminMode' => $adminMode));
			?>
			<?php
			if ($adminMode) {
			?>

			<?php echo $this -> Form -> create('Approval', array('url' => '/admin/collectibles/approve/' . $collectibleDetail['Collectible']['id'], 'id' => 'approval-form'));?>
			<input id="approve-input" type="hidden" name="data[Approval][approve]" value="" />
			<fieldset class="approval-fields">
				<ul class="form-fields">
					<li>
						<div class="label-wrapper">
							<label for=""> <?php __('Notes')
								?></label>
						</div>
						<textarea rows="6" cols="30" name="data[Approval][notes]"></textarea>
					</li>
				</ul>
			</fieldset>
			</form>
			<div class="links">
				<input type="button" id="approval-button" class="button" value="Approve">
				<input type="button" id="deny-button" class="button" value="Deny">
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
			<?php }?>
		</div>
	</div>
</div>
<?php
if (isset($showVariants) && $showVariants && !$collectibleDetail['Collectible']['variant']) {
	echo $this -> element('collectible_variant_list', array());
}
?>