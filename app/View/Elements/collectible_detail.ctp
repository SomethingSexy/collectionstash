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
			<h2><?php echo $title;?></h2>
			<div class="actions icon">
				<ul>
					<?php
                    if (isset($showAddStash) && $showAddStash && $isLoggedIn) {
                        echo '<li><a title="Add to stash" class="link add-stash-link" href="/collectibles_users/add/' . $collectibleDetail['Collectible']['id'] . '"><img src="/img/icon/add_stash_link_25x25.png"/></a></li>';
                    }
					?>
					<?php
                    if ($showWho) {
                        echo '<li>';
                        echo $this -> Html -> link($this -> Html -> image('/img/icon/group.png', array('alt' => "Registry", 'border' => "0")), '/collectibles_users/registry/' . $collectibleDetail['Collectible']['id'], array('title' => 'Registry', 'escape' => false));
                        echo '</li>';
                    }
                    if (isset($showHistory) && $showHistory) {
                        echo '<li>';
                        echo $this -> Html -> link($this -> Html -> image('/img/icon/cabinet.png', array('alt' => "History", 'border' => "0")), '/collectibles/history/' . $collectibleDetail['Collectible']['id'], array('title' => 'History', 'escape' => false));
                        echo '</li>';
                    }
                    if (isset($showQuickAdd) && $showQuickAdd && $isLoggedIn) {
                        echo '<li>';
                        if ($collectibleDetail['Collectible']['variant']) {
                            echo $this -> Html -> link($this -> Html -> image('/img/icon/action.png', array('alt' => "Add", 'border' => "0")), '/collectibles/quickAdd/' . $collectibleDetail['Collectible']['id'] . '/false/', array('title' => __('Add a similar variant collectible type with the same manufacturer.', true), 'escape' => false));
                        } else {
                            echo $this -> Html -> link($this -> Html -> image('/img/icon/action.png', array('alt' => "Add", 'border' => "0")), '/collectibles/quickAdd/' . $collectibleDetail['Collectible']['id'] . '/false/', array('title' => __('Add a similar collectible type with the same manufacturer.', true), 'escape' => false));
                        }
                        echo '</li>';
                        if (!$collectibleDetail['Collectible']['variant']) {
                            echo '<li>';
                            echo $this -> Html -> link($this -> Html -> image('/img/icon/action_variant.png', array('alt' => "Add", 'border' => "0")), '/collectibles/quickAdd/' . $collectibleDetail['Collectible']['id'] . '/true', array('title' => __('Add a varaint of this collectible.', true), 'escape' => false));
                            echo '</li>';
                        }
                    }
					?>
				</ul>
			</div>
		</div>
		<div class="component-view">
			<?php
if(isset($isLoggedIn) && $isLoggedIn === true)
{
//Show something if logged in?
			?>

			<?php } else {?>
			<div class="helpful-hint-message">
				<?php echo __('See something that is inaccurate? Login or register to help us maintain the most accurate collectible database.');?>
			</div>
			<?php }?>
			<?php
            echo $this -> element('collectible_detail_core', array('showEdit' => $showEdit, 'editImageUrl' => $editImageUrl, 'editManufactureUrl' => $editManufactureUrl, 'showStatistics' => $showStatistics, 'collectibleCore' => $collectibleDetail, 'showAddedBy' => $showAddedBy, 'showAddedDate' => $showAddedDate, 'adminMode' => $adminMode, 'showTags' => $showTags));
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
							<label for=""> <?php echo __('Notes')
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
if (isset($showVariants) && $showVariants) {
    echo $this -> element('collectible_variant_list', array());
}
?>