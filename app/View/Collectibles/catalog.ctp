<?php echo $this -> Minify -> script('js/home', array('inline' => false)); ?>
<div id="home-components">

	
	<div class="component random-collectibles">
		<div class="inside" >
			<div class="component-title">
				<h2><?php echo __('Recently Added Collectibles'); ?></h2>
			</div>
			<div class="component-view">
				<?php

				echo '<div class="glimpse">';
				$count = 0;
				$collectiblesCount = count($recentlyAddedCollectibles) - 1;
				foreach ($recentlyAddedCollectibles as $key => $collectible) {
					$newline = false;
					$endline = false;
					if ($count === 0) {
						$newline = true;
						$count += $count + 1;
					} else {
						if ($count % 5 != 0) {
							$count += $count + 1;
						} else {
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
					if (!empty($collectible['CollectiblesUpload'])) {
						echo '<div class="image"><a href="/collectibles/view/' . $collectible['Collectible']['id'] . '/' . $collectible['Collectible']['slugField'] . '">';

						foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
							if ($upload['primary']) {
								echo $this -> FileUpload -> image($upload['Upload']['name'], array('width' => 150, 'height' => 150));
								break;
							}
						}

						echo '</a></div>';
					} else {
						echo '<div class="image"><a href="/collectibles/view/' . $collectible['Collectible']['id'] . '/' . $collectible['Collectible']['slugField'] . '"><img src="/img/silhouette_thumb.png"/></a></div>';
					}
					if ($endline) {
						echo '</div>';
					}
				}
				echo '</div>';
				?>
			</div>
		</div>
	</div>
	<div class="component manufacturers">
		<div class="inside" >
			<div class="component-title">
				<h3><?php echo __('Top Manufacturers'); ?></h3>
			</div>
			<div class="component-view">
				<div class="standard-list">
					<ul>
					<?php
					foreach ($manufactures as $key => $manufacture) {
						echo '<li>';
						echo '<span class="name">';
						echo '<a href="/collectibles/search?m=' . $manufacture['Manufacture']['id'] . '">' . $manufacture['Manufacture']['title'] . ' (' . $manufacture['Manufacture']['collectible_count'] . ')</a>';
						echo '</span>';
						echo '</li>';
					}
					?>
					</ul>
				</div>
			</div>
		</div>		
	</div>
	<div class="component licenses">
		<div class="inside" >
			<div class="component-title">
				<h3><?php echo __('Top Brands'); ?></h3>
			</div>
			<div class="component-view">
				<div class="standard-list">
					<ul>
					<?php
					foreach ($licenses as $key => $license) {
						echo '<li>';
						echo '<span class="name">';
						echo '<a href="/collectibles/search?l=' . $license['License']['id'] . '">' . $license['License']['name'] . ' (' . $license['License']['collectible_count'] . ')</a>';
						echo '</span>';
						echo '</li>';
					}
					?>
					</ul>
				</div>				
			</div>
		</div>		
	</div>	
	<div class="component collectibletypes">
		<div class="inside" >
			<div class="component-title">
				<h3><?php echo __('Top Collectible Types'); ?></h3>
			</div>
			<div class="component-view">
				<div class="standard-list">
					<ul>
					<?php
					foreach ($collectibletypes as $key => $type) {
						echo '<li>';
						echo '<span class="name">';
						echo '<a href="/collectibles/search?ct=' . $type['Collectibletype']['id'] . '">' . $type['Collectibletype']['name'] . ' (' . $type['Collectibletype']['collectible_count'] . ')</a>';
						echo '</span>';
						echo '</li>';
					}
					?>
					</ul>	
				</div>			
			</div>
		</div>		
	</div>		
	
</div>