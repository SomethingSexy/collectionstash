<script>
	$(function(){
		<?php 
		if(isset($saveSearchFilters['manufacturer'])){
			echo 'var manFilter = '.$saveSearchFilters['manufacturer'].';';
		} else {
			echo 'var manFilter = null;';
		} 
		if(isset($saveSearchFilters['collectibletype'])){
			echo 'var typeFilter = '.$saveSearchFilters['collectibletype'].';';
		} else {
			echo 'var typeFilter = null;';
		} 		
		?>
		
		
		$('#filters').children('.filter').click(function(){
			$('#filters').children('.filter').children('.filter-list').hide();                                                                                                                                                                                                                                             
			$(this).children('.filter-list').show();
		});
	});
	
</script>

<div class="component" id="collectibles-list-component">
	<div class="inside" >
		<div class="component-title">
			<h2>
			<?php __('Search Results');?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<div id="filters">
				<div class="filter">
					<?php
						//First lets get all of the filters, cause we might need the name of one for the title
						$manufacturers = $this -> Session -> read('Manufacture_Search.filter');
						$manfilters = '';
						foreach ($manufacturers as $key => $value) {
							if(isset($saveSearchFilters['manufacturer']) && $saveSearchFilters['manufacturer'] == $value['Manufacture']['id']){
								$manfilters .='<li class="selected">';
								//if this is the name then grab the name
								$selectedMan = $value['Manufacture']['title'];
							} else {
								$manfilters .='<li>';
							}
							$manfilters .='<a data-type="m" data-filer="'.$value['Manufacture']['id'].'" href="/collectibles/search?m='.$value['Manufacture']['id']. '">';
							$manfilters .=$value['Manufacture']['title'];
							$manfilters .='</a>';
							$manfilters .='</li>';	
						}
						
						echo '<div class="filter-name"><span>';
						if(isset($selectedMan)){
							echo $selectedMan;
						} else {
							echo  __('Manufacturer', true);
						}
						echo '</span></div>';
						echo '<div class="filter-list">';
						echo '<ol>';
						echo $manfilters;
						echo '</ol>';
						echo '</div>';
					?>
				</div>
				
				
				<div class="filter">
					<?php
						//First lets get all of the filters, cause we might need the name of one for the title
						$collectibleTypes = $this -> Session -> read('CollectibleType_Search.filter');
						$typefilters = '';
						foreach ($collectibleTypes as $key => $value) {
							if(isset($saveSearchFilters['collectibletype']) && $saveSearchFilters['collectibletype'] == $value['Collectibletype']['id']){
								$typefilters .='<li class="selected">';
								//if this is the name then grab the name
								$selectedType = $value['Collectibletype']['name'];
							} else {
								$typefilters .='<li>';	
							}
							$typefilters .='<a data-type="ct" data-filer="'.$value['Collectibletype']['id'].'" href="/collectibles/search?ct='.$value['Collectibletype']['id']. '">';
							$typefilters .=$value['Collectibletype']['name'];
							$typefilters .='</a>';
							$typefilters .='</li>';	
						}
						
						echo '<div class="filter-name"><span>';
						if(isset($selectedType)){
							echo $selectedType;
						} else {
							echo  __('Type', true);
						}
						echo '</span></div>';
						echo '<div class="filter-list">';
						echo '<ol>';
						echo $typefilters;
						echo '</ol>';
						echo '</div>';
					?>
				</div>
			</div>			
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
						<?php echo $html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id']));?>
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