<div id="my-stashes-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo $stashUsername . '\'s' .__(' stash', true) ?></h2>
          	<div class="actions">
    		<ul>
    			<?php 
    				if(isset($myStash) && $myStash) {
    					echo '<li><a class="link add-stash-link" href="/collectibles/search/initial:yes/"><img src="/img/icon/add_stash_link.png"/></a></li>';
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
				echo '<div class="glimpse">';
				foreach($collectibles as $myCollectible) {
					if (!empty($myCollectible['Collectible']['Upload'])) { 
						echo '<a href="/collectiblesUser/view/'.$myCollectible['CollectiblesUser']['id']. '">'.$fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('width' => '100')).'</a>';
						echo '<div class="collectible image-fullsize hidden">';
						echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('width' => 0));
						echo '</div>';
					 } else { 
						echo '<a href="/collections/viewCollectible/'.$myCollectible['id']. '"><img src="/img/silhouette_thumb.gif"/></a>';
				 	}	 
				}
				echo'</div>';			
			} else {
				echo '<p class="">'. $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
			}
			?>
    </div>    
  </div>
</div>