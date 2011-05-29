<?php echo $this->Html->script('home',array('inline'=>false)); ?>
<div id="home-components">
	<div class="component random-collectibles">
		<div class="inside" >
			<div class="component-title">
      			<h2><?php __('Random Collectibles');?></h2>
    		</div>
    		<div class="component-view">
    			<div id="images">
					<?php foreach ($randomCollectibles as $collectible):?>	
						<?php 
							if (!empty($collectible['Upload'])) { ?>
								<?php echo $fileUpload->image($collectible['Upload'][0]['name'], array('width' => 200)); ?>
							<?php } else { ?>
								<img src="/img/silhouette.gif"/>
						<?php } ?>
		 				
		 			<?php endforeach; ?>     				
    			</div>

			</div>
		</div>	
	</div>
</div>