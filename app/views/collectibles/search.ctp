<div class="component" id="collectibles-list-component">
	<div class="inside" >
		<div class="component-title">
			<h2>
			<?php __('Search Results');?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">

			<?php echo $this -> element('search_filters', array('searchUrl'=>'/collectibles/search'));?>
			<div class="collectibles view">				
				<?php
				foreach ($collectibles as $collectible):
				?>
				<div class="collectible item">
					<?php echo $this -> element('collectible_list_image', array(
						'collectible' => $collectible
					));?>
					<?php 
						if(isset($collectible['SpecializedType']) && !empty($collectible['SpecializedType']['name'])){
							echo $this -> element('collectible_list_detail', array(
							'collectible' => $collectible['Collectible'],
							'manufacture' => $collectible['Manufacture'],
							'license' => $collectible['License'],
							'collectibletype' => $collectible['Collectibletype'],
							'speciazliedType' => $collectible['SpecializedType']
							));			
						} else {
							echo $this -> element('collectible_list_detail', array(
							'collectible' => $collectible['Collectible'],
							'manufacture' => $collectible['Manufacture'],
							'license' => $collectible['License'],
							'collectibletype' => $collectible['Collectibletype'],
							));
						}
					

					?>
					<div class="links">
						<?php if($isLoggedIn){
							echo '<a title="Add to stash" href="/collectibles_user/add/'.$collectible['Collectible']['id']. '" class="add-to-collection">Add to Stash</a>';
						} ?>
					</div>
					<div class="collectible actions">
						<?php echo $html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']));?>
					</div>
				</div>
				<?php endforeach;?>
				<div class="paging">
					<p>
						<?php
						echo $this -> Paginator -> counter( array('format' => __('Page %page% of %pages%, showing %current% collectibles out of %count% total.', true)));
						?>
					</p>
					<?php 
				
					$urlparams = $this->params['url'];
					unset($urlparams['url']); 
					$this->Paginator->options(array('url' => array('?' => http_build_query($urlparams))));
					
					echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
					<?php echo $this -> Paginator -> numbers();?>
					<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
				</div>

			</div>
		</div>
	</div>
</div>