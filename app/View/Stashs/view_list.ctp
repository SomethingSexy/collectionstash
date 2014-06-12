<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<div class="col-md-12">
	<h2><?php
	echo $stashUsername . '\'s';
	echo __(' Stash', true);
	?></h2>
	<div id="my-stashes-component" class="widget widget-tabs">
			<?php echo $this -> element('flash'); ?>
					<ul class="nav nav-tabs widget-wide">
						<?php
						echo '<li class="active">';
						?>
						
						<?php echo '<a href="/user/' . $stashUsername . '/stash">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						echo '<li>';
						?>
						<?php echo '<a href="/user/' . $stashUsername . '/wishlist">' . __('Wish List') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user/' . $stashUsername . '/sale">' . __('Sale/Trade List') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user/' . $stashUsername . '/photos">' . __('Photos') . '</a>'; ?>	
						</li>
						<li><?php echo '<a href="/user/' . $stashUsername . '/comments">' . __('Comments') . '</a>'; ?></li>
						<li><?php echo '<a href="/user/' . $stashUsername . '/history">' . __('History') . '</a>'; ?></li>
					</ul>	
		<div class="widget-content">
				<div class="clearfix">
					<div class="btn-group actions pull-left">
							<?php
							if (isset($myStash) && $myStash) {
								echo '<a title="Edit" class="btn" href="/stashs/edit/' . $stashUsername . '"><i class="fa fa-pencil-square-o"></i> Edit</a>';
							}
							if (isset($isLoggedIn) && $isLoggedIn === true && !$myStash) {
								$userSubscribed = 'false';
								if (array_key_exists($stash['entity_type_id'], $subscriptions)) {
									$userSubscribed = 'true';
								}
								echo '<a  id="subscribe"  data-subscribed="' . $userSubscribed . '" data-entity-type="stash" data-entity-type-id="' . $stash['entity_type_id'] . '" class="btn" href="#"><i class="fa fa-heart"></i></a>';
							}
							?>
					</div>
				    <div class="btn-group views pull-right">
				    	<?php
						$currentStash = 'stash';
						echo '<a class="btn" href="/' . $currentStash . '/' . $stashUsername . '/tile"><i class="fa fa-th-large"></i></a>';
						echo '<a class="btn" href="/' . $currentStash . '/' . $stashUsername . '/list"><i class="fa fa-list"></i></a>';
	 					?>
				    </div>
				</div>
						<?php
						if (isset($collectibles) && !empty($collectibles)) {
							echo '<div class="row">';
							echo '<div class="col-md-9 filterable-list">';
							echo $this -> element('selected_search_filters');
							echo $this -> element('stash_table_list', array('collectibles' => $collectibles));
							echo '</div>';
							echo '<div class="col-md-3">';
							echo '<div class="">';
							echo $this -> element('search_filters');
							echo '</div>';
							echo '</div>';
							echo '</div>';
						} else {
							echo '<p class="empty">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
						}
		?>
		</div>

	</div>
</div>
<?php echo $this -> Minify -> script('cs.subscribe', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('jquery.masonry.min', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('views/view.stash.sell', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('models/model.collectible.user', array('inline' => false)); ?>

<script><?php
if (isset($reasons)) {
	echo 'var reasons = \'' . json_encode($reasons) . '\';';
}
	?>
    var filtersView = new FiltersView();

    filtersView.render();

    var selectedFiltersView = new SelectedFiltersView();
    selectedFiltersView.render();
</script>