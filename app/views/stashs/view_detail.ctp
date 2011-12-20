<div id="my-stashes-component" class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo $stashUsername . '\'s' .__(' stash', true) ?></h2>
    	<div class="actions">
    		<ul>
    			<?php 
    				if(isset($myStash) && $myStash) {
    					echo '<li><a class="link add-stash-link" href="/collectibles/search/initial:yes/"><img src="/img/icon/add_stash_link.png"/></a></li>';
						echo '<li><a class="link upload-link" href="/user_uploads/uploads"><img src="/img/icon/upload_link.png"/></a></li>';
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
				echo '<div class="collectibles view">';
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
					echo '<div class="collectible actions">';
					echo $html -> link('Details', array('controller' => 'collectibles_user', 'action' => 'view', $myCollectible['CollectiblesUser']['id']));
					echo '</div>';
					echo '</div>'; 
				}
				echo '</div>'; 
						
			} else {
				echo '<p class="">'. $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
			}
			?>
			<div class="paging">
				<p>
					<?php
					echo $this -> Paginator -> counter( array('format' => __('Page %page% of %pages%, showing %current% collectibles out of %count% total.', true)));
					?>
				</p>
				<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
				<?php echo $this -> Paginator -> numbers();?>
				<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
			</div>
    </div>    
  </div>
</div>

