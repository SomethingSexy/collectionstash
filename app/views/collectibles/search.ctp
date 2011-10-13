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
		if(isset($saveSearchFilters['search'])){
			echo 'var searchFilter = "'.$saveSearchFilters['search'].'";';
		} else {
			echo 'var searchFilter = null;';
		} 		
		?>
		
		//This is for clicking and opening up the filter box
		$('#filters').children('.filter').children('.filter-name').children('span').click(function(){
			$('#filters').children('.filter').children('.filter-list').hide();                                                                                                                                                                                                                                             
			$(this).parent('.filter-name').parent('.filter').children('.filter-list').show();
		});
		
		//This is for clicking anywhere else but the filter box and closing them
		$('body').bind('click', function(e){
        	if(!$(e.target).parent().is('.filter-name') && !$(e.target).is('.filter-list') && !$(e.target).is('ol', '.filter-list') && !$(e.target).is('li', '.filter-list ol')){
         		$('#filters').children('.filter').children('.filter-list').hide();  	
        	}
       	});
       	
       	//This is for clicking a specific filter
		$('#filters').children('.filter').children('.filter-list').children('ol').children('li').children('.filter-links').click(function(){
			var selectedType = $(this).attr('data-type');
			var selectedFilter = $(this).attr('data-filter');
			//When they select a new one we will refresh the page to add the new filters
			//but we need to make sure to pass the existing ones as well
			//Right now we only allow one filter per type but this could be updated later
			
			if (searchFilter !== null){
				searchFilter = '&q=' + searchFilter;
			} else {
				searchFilter = '';
			}
			
			if(selectedType === 'm'){
				//If manufacture is clicked, we will restart everything cause they might not have valid types
				//This kind of seems like a hack 
				window.location.href = "/collectibles/search?m=" + selectedFilter + searchFilter;	
			} else if (selectedType === 'ct'){
				if(manFilter !== null) {
					window.location.href = "/collectibles/search?m=" + manFilter + "&ct=" + selectedFilter + searchFilter;	
				} else {
					window.location.href = "/collectibles/search?ct=" + selectedFilter + searchFilter;	
				}
			}
		});
		
		$('#filters').children('.filter').children('.filter-name').children('.ui-icon-close').click(function(){
			var selectedType = $(this).attr('data-type');

				
			if(selectedType === 'm'){
				//If manufacture is clicked, we will restart everything cause they might not have valid types
				//This kind of seems like a hack 
				
				if (searchFilter !== null){
					window.location.href = "/collectibles/search?q=" + searchFilter;
				} else {
					window.location.href = "/collectibles/search";
				}
			} else if (selectedType === 'ct'){
				if(manFilter !== null) {
					window.location.href = "/collectibles/search?m=" + manFilter;	
				} else {
					window.location.href = "/collectibles/search";	
				}
			}	
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
				<div class="search-query"><?php 
				if(isset($saveSearchFilters['search'])) {
					echo $saveSearchFilters['search'];
				}
				?></div>
				<div class="filter manufacturer">
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
							$manfilters .='<a class="filter-links" data-type="m" data-filter="'.$value['Manufacture']['id'].'">';
							$manfilters .=$value['Manufacture']['title'];
							$manfilters .='</a>';
							$manfilters .='</li>';	
						}
						
						echo '<div class="filter-name">';
						if(isset($selectedMan)){
							echo '<span>';
							echo $selectedMan;
							echo '</span>';
							echo '<a data-type="m" class="ui-icon ui-icon-close"></a>';
						} else {
							echo '<span>';
							echo  __('Manufacturer', true);
							echo '</span>';
							echo '<a class="ui-icon ui-icon-triangle-1-s"></a>';
						}
				
						echo '</div>';
						echo '<div class="filter-list">';
						echo '<ol>';
						echo $manfilters;
						echo '</ol>';
						echo '</div>';
					?>
				</div>
				
				
				<div class="filter type">
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
							$typefilters .='<a class="filter-links" data-type="ct" data-filter="'.$value['Collectibletype']['id'].'">';
							$typefilters .=$value['Collectibletype']['name'];
							$typefilters .='</a>';
							$typefilters .='</li>';	
						}
						
						echo '<div class="filter-name">';
						if(isset($selectedType)){
							echo '<span>';
							echo $selectedType;
							echo '</span>';
							echo '<a data-type="ct" class="ui-icon ui-icon-close"></a>';
						} else {
							echo '<span>';
							echo  __('Type', true);
							echo '</span>';
							echo '<a class="ui-icon ui-icon-triangle-1-s"></a>';
						}
						echo '</div>';
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