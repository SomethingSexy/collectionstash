<?php echo $this -> Html -> script('home', array('inline' => false));?>
<div id="home-components">
	<div class="component random-collectibles">
		<div class="inside" >
			<div class="component-title">
				<h2><?php __('Recently Added Collectibles');?></h2>
			</div>
			<div class="component-view">
				<?php

				echo '<div class="glimpse">';
				$count = 0;			
				$collectiblesCount = count($randomCollectibles) - 1;
				foreach ($randomCollectibles as $key => $collectible) {
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
					if (!empty($collectible['Upload'])) {
						echo '<a href="/collectibles/view/' . $collectible['Collectible']['id'] . '">';

						if (!empty($collectible['Upload'])) {
							echo $fileUpload -> image($collectible['Upload'][0]['name'], array('width' => 100));
						} else {
							echo '<img src="/img/silhouette_thumb.gif"/>';
						}
						echo '</a>';
						echo '<div class="collectible image-fullsize hidden">';
						echo $fileUpload -> image($collectible['Upload'][0]['name'], array('width' => 0));
						echo '</div>';
					} else {
						echo '<a href="/collectibles/view/' . $collectible['Collectible']['id'] . '"><img src="/img/silhouette_thumb.gif"/></a>';
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
</div>