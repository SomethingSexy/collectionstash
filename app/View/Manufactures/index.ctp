	<div class="page-header">
		<h2><?php echo $manufacture['Manufacture']['title']?></h2>
	</div>	
		<span class="url"><?php echo $manufacture['Manufacture']['url']
				?></span>
	<div class="row">
		<div class="span4">
			<div class="well">			
				<dl class="dl-horizontal">
		
					<dt><?php echo __('Total Collectibles'); ?></dt><dd><?php echo $manufacture['Manufacture']['collectible_count']
						?></dd>
		
					<dt><?php echo __('Percentage'); ?></dt><dd><?php echo $manufacture['Manufacture']['percentage_of_total']
						?></dd>
			
					<dt><?php echo __('Total Collectible Platforms'); ?></dt><dd><?php echo $manufacture['Manufacture']['collectibletype_count']
						?></dd>
			
					<dt><?php echo __('Total Brands'); ?></dt><dd><?php echo $manufacture['Manufacture']['license_count']
						?></dd>
					<dt><?php echo __('Highest Price'); ?></dt><dd><?php echo $manufacture['Manufacture']['highest_price']
						?></dd>
					<dt><?php echo __('Lowest Price'); ?></dt><dd><?php echo $manufacture['Manufacture']['lowest_price']
						?></dd>	
					<?php
					if (isset($manufacture['Manufacture']['highest_edition_size'])) {
						echo '<dt>';
						echo __('Highest Edition Size');
						echo '</dt>';
						echo '<dd>';
						echo $manufacture['Manufacture']['highest_edition_size'];
						echo '</dd>';
					}
					?>							
					<?php
					if (isset($manufacture['Manufacture']['lowest_edition_size'])) {
						echo '<dt>';
						echo __('Lowest Edition Size');
						echo '</dt>';
						echo '<dd>';
						echo $manufacture['Manufacture']['lowest_edition_size'];
						echo '</dd>';
					}
					?>							
				</dl>	
			</div>
		</div>
		<div class="span8">
			<div class="well manufacturer-collectibles">
				<div data-toggle="modal-gallery" data-target="#modal-gallery">
			<?php
			echo '<div id="titles-nav" class="hidden">';
			echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));
			echo '</div>';
			echo '<div class="tiles stashable" data-toggle="modal-gallery" data-target="#modal-gallery">';
			
			foreach ($collectibles as $collectible) {
				echo '<div class="tile">';
				if (!empty($collectible['CollectiblesUpload'])) {
					foreach ($collectible['CollectiblesUpload'] as $key => $upload) {
						if ($upload['primary']) {
							echo '<div class="image">';
							echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 200, 'height' => 200)) . '</a>';
							echo '</div>';
							break;
						}
					}
			
					//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
				} else {
					echo '<div class="image"><img src="/img/silhouette_thumb.png"/></div>';
				}
				echo '<div class="header">';
				echo $this -> Html -> link($collectible['Collectible']['displayTitle'], array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']));
				echo '</div>';
			
				if ($isLoggedIn) {
					echo '<a class="link add-stash-link" href="/collectibles_users/add/' .$collectible['Collectible']['id'] .'" title="Add to Stash">';
					echo '<img src="/img/icon/add_stash_link_25x25.png">';
					echo  '</a>';
					echo '<a data-stash-type="Wishlist" data-collectible-id="' . $collectible['Collectible']['id'] . '" class="add-to-stash btn" title="Add to Wishlist" href="#"><i class="icon-star"></i></a>';
				}
				echo '</div>';
			}
			echo '</div>';
			     ?>
			</div>
			</div>
		</div>
		
	</div>
	
<?php echo $this -> Minify -> script('js/jquery.infinitescroll', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/jquery.masonry.min', array('inline' => false)); ?>

<script>
	$(function() {
		var $container = $('div.tiles');
		$container.imagesLoaded(function() {
			$container.masonry({
				itemSelector : '.tile'
			});
		});

		$container.infinitescroll({
			nextSelector : "#titles-nav a",
			navSelector : "#titles-nav",
			itemSelector : ".tile",
			loading : {
				finishedMsg : "All collectibles have been loaded!",
				msgText : "<em>Loading the next set of collectibles.</em>",
			}
		}, function(newElements) {
			// hide new items while they are loading
			var $newElems = $(newElements).css({
				opacity : 0
			});
			// ensure that images load before adding to masonry layout
			$newElems.imagesLoaded(function() {
				// show elems now they're ready
				$newElems.animate({
					opacity : 1
				});
				$container.masonry('appended', $newElems, true);
			});
		});
	}); 
</script>




