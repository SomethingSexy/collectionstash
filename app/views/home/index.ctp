<?php echo $this -> Html -> script('home', array('inline' => false));?>
<div id="home-components">
	<div class="component welcome-component">
		<div class="inside" >
			<div class="component-view">	
				<?php echo $html->image('logo/collection_stash_logo_white.png', array('alt' => 'Collection Stash'));?>
			
					<p class="heading">Welcome to Collection Stash</p>
   					<p class="body"> This site was designed to provide collectors with the ability to record and catalogue their prized possessions and connect with others who share a similar passion.  By becoming a member, you can track all details of items in your collection: manufacturer, artist, purchase date, edition size, and more.   We welcome your feedback on the site and look forward to making improvements in the future.</p>
			
			</div>
		</div>
	</div>
	
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
							echo '<img src="/img/silhouette_thumb.png"/>';
						}
						echo '</a>';
						echo '<div class="collectible image-fullsize hidden">';
						echo $fileUpload -> image($collectible['Upload'][0]['name'], array('width' => 0));
						echo '</div>';
					} else {
						echo '<a href="/collectibles/view/' . $collectible['Collectible']['id'] . '"><img src="/img/silhouette_thumb.png"/></a>';
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