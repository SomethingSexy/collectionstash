<?php
if (isset($setPageTitle) && $setPageTitle) {
	$this -> set("title_for_layout", $collectibleDetail['Manufacture']['title'] . ' - ' . $collectibleDetail['Collectible']['name']);
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
					echo $html -> link('Registry', array('admin' => false, 'controller' => 'collections', 'action' => 'who', $collectibleDetail['Collectible']['id']));
				}
				if (isset($showHistory) && $showHistory) {
					echo $html -> link('History', array('action' => 'history', $collectibleDetail['Collectible']['id']));
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
					echo $tag['Tag']['tag'];
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
			if(!isset($adminMode)){
				$adminMode = false;
			}
			echo $this -> element('collectible_detail_core', array('showEdit' => $showEdit, 'editImageUrl' => $editImageUrl, 'editManufactureUrl' => $editManufactureUrl, 'showStatistics' => $showStatistics, 'collectibleCore' => $collectibleDetail, 'showAddedBy' => $showAddedBy, 'showAddedDate' => $showAddedDate, 'adminMode' => $adminMode ));
			?>
			<?php
			if ($adminMode) {
				echo $this -> Form -> create('Collectible', array('url' => '/admin/collectibles/approve/' . $collectibleDetail['Collectible']['id']));
				echo $this -> Form -> end(__('Approve', true));

			}
			?>
		</div>
	</div>
</div>
<?php
if (isset($showVariants) && $showVariants) {
	echo $this -> element('collectible_variant_list', array());
}
?>