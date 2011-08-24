<div id="my-stashes-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $stashUsername . '\'s' .__(' stash', true)
			?></h2>
			<div class="actions">
				<ul>
					<?php
					if (isset($myStash) && $myStash) {
						echo '<li><a class="link add-stash-link" href="/collectibles/search/initial:yes/"><img src="/img/icon/add_stash_link.png"/></a></li>';
					}
					?>
					<li>
						<?php echo '<a class="link detail-link" href="/stashs/view/' . $stashUsername . '/view:detail"><img src="/img/icon/detail_link.png"/></a>';?>
					</li>
					<li>
						<?php echo '<a class="link glimpse-link" href="/stashs/view/' . $stashUsername . '/view:glimpse"><img src="/img/icon/glimpse_link.png"/></a>';?>
					</li>
				</ul>
			</div>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<?php
			if (isset($collectibles) && !empty($collectibles)) {
				echo '<div class="glimpse">';
				$count = 0;
				$collectiblesCount = count($collectibles) - 1;
				foreach ($collectibles as $key => $myCollectible) {
					$newline = false;
					$endline = false;
					//First chec to see if we are starting over
					if ($count === 0) {
						$newline = true;
						$count += $count + 1;
					} else {
						//If not first, check to see if we are on the 5th one or not
						if ($count % 5 != 0) {
							//if we are not, lets increase
							$count += $count + 1;
						} else {
							//if we are, then lets start over and make sure we close out the div.
							$count = 0;
							$endline = true;
						}
					}

					if (!$endline) {
						if (($collectiblesCount) === $key) {
							$endline = true;
						}
					}

					if ($newline) {
						echo '<div class="line">';
					}
					if (!empty($myCollectible['Collectible']['Upload'])) {
						echo '<a href="/collectiblesUser/view/' . $myCollectible['CollectiblesUser']['id'] . '">' . $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('width' => '100')) . '</a>';
						echo '<div class="collectible image-fullsize hidden">';
						echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('width' => 0));
						echo '</div>';
					} else {
						echo '<a href="/collections/viewCollectible/' . $myCollectible['id'] . '"><img src="/img/silhouette_thumb.gif"/></a>';
					}
					if ($endline) {
						echo '</div>';
					}
				}
				echo '</div>';
			} else {
				echo '<p class="">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
			}
			?>
			<div class="paging">
				<p>
					<?php
					echo $this -> Paginator -> counter(array('format' => __('Page %page% of %pages%, showing %current% collectibles out of %count% total.', true)));
					?>
				</p>
				<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
				<?php echo $this -> Paginator -> numbers();?>
				<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
			</div>
		</div>
	</div>
</div>