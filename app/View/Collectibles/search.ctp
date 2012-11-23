<div class="component" id="collectibles-list-component">
	<div class="inside" >
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<div class="title">
				<h3><?php echo __('Search Results'); ?></h3>
			    <div class="btn-group views">
			    	<?php echo '<a class="btn" href="/stashs/view/' . $stashUsername . '/tile"><i class="icon-th-large"></i></a>'; ?>
			    	<?php echo '<a class="btn" href="/stashs/view/' . $stashUsername . '/list"><i class="icon-list"></i></a>'; ?>
			    </div>
			</div>
			<?php echo $this -> element('search_filters', array('searchUrl'=>'/collectibles/search'));?>
			<div class="collectibles view" data-toggle="modal-gallery" data-target="#modal-gallery">				
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
							'speciazliedType' => $collectible['SpecializedType'],
							'showStats' => true
							));			
						} else {
							echo $this -> element('collectible_list_detail', array(
							'collectible' => $collectible['Collectible'],
							'manufacture' => $collectible['Manufacture'],
							'license' => $collectible['License'],
							'collectibletype' => $collectible['Collectibletype'],
							'showStats' => true
							));
						}
					?>
					<div class="links">
						<?php if($isLoggedIn){
							echo '<a title="Add to stash" href="/collectibles_users/add/'.$collectible['Collectible']['id']. '" class="add-to-collection">Add to Stash</a>';
						} ?>
					</div>
					<div class="collectible actions">
						<?php echo $this -> Html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']));?>
					</div>
				</div>
				<?php endforeach;?>
				<div class="paging">
					<p>
						<?php
						echo $this -> Paginator -> counter( array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
						?>
					</p>
					<?php 
				
					
					$urlparams = $this->request->query;
					unset($urlparams['url']); 
					$this->Paginator->options(array('url' => array('?' => http_build_query($urlparams))));
					
					echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
					<?php echo $this -> Paginator -> numbers(array('separator'=> false));?>
					<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
				</div>

			</div>
		</div>
	</div>
</div>