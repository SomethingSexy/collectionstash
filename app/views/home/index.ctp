<?php echo $this->Html->script('home',array('inline'=>false)); ?>
<div id="home-components">
	<div class="component random-collectibles">
		<div class="inside" >
			<div class="component-title">
      			<h2><?php __('Recently Added Collectibles');?></h2>
    		</div>
    		<div class="component-view">
    			<div class="collectibles">
					<?php foreach ($randomCollectibles as $collectible):?>	
						<div class="collectible">
							<div class="image">
								<?php 
									if (!empty($collectible['Upload'])) { ?>
										<?php echo $fileUpload->image($collectible['Upload'][0]['name'], array('width' => 50)); ?>
									<?php } else { ?>
										<img src="/img/silhouette_thumb.gif"/>
								<?php } ?>
							</div>
							<div class="detail">
								<p><?php echo $collectible['Manufacture']['title']; ?></php?></p>
								
								<p><a href="/collectibles/view/<?php echo $collectible['Collectible']['id']; ?>"><?php echo $collectible['Collectible']['name']; ?></a></p>
							</div>
		 				</div>
		 			<?php endforeach; ?>     				
    			</div>

			</div>
		</div>	
	</div>
</div>