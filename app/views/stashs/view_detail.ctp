<div id="my-stashes-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo $stashUsername . '\'s' .__(' stash', true) ?></h2>
    	<div class="actions">
    		<ul>
    			<?php 
    				if(isset($myStash) && $myStash) {
    					echo '<li><a class="link add-stash-link" href="/collections/addSearch/initial:yes/stashId:'.$myStashId.'"><img src="/img/icon/add_stash_link.png"/></a></li>';
    				}
    			?>
    			<li><?php echo '<a class="link detail-link" href="/stashs/view/'.$stashUsername. '/view:detail"><img src="/img/icon/detail_link.png"/></a>';	?></li>
    			<li><?php echo '<a class="link glimpse-link" href="/stashs/view/'.$stashUsername. '/view:glimpse"><img src="/img/icon/glimpse_link.png"/></a>';	?></li>
    		</ul>
    	</div>	      
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-view">
			<?php if(isset($collectibles) && !empty($collectibles)) {
				
				foreach($collectibles as $myCollectible) {
					echo '<div class="collectible item">';		
					echo $this -> element('collectible_list_image', array(
						'collectible' => $myCollectible['Collectible']
					));
					echo $this -> element('collectible_list_detail', array(
						'collectible' => $myCollectible['Collectible'],
						'manufacture' => $myCollectible['Collectible']['Manufacture'],
						'license' => $myCollectible['Collectible']['License'],
						'collectibletype' => $myCollectible['Collectible']['Collectibletype']
					));	
					 echo '</div>'; 
				}
						
			} else {
				echo '<p class="">'. $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
			}
			?>
    </div>    
  </div>
</div>

