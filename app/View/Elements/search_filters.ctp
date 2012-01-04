<?php 
	//This is a parameter for this element.
	//This will tell us if we should lock the manufacturer filter or not
	//default is false
	if(!isset($lockManufacturer)){
		$lockManufacturer = false;
	}
?>

<script type="text/javascript">
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
	if(isset($saveSearchFilters['tag'])){
		echo 'var tagFilter = "'.$saveSearchFilters['tag'].'";';
	} else {
		echo 'var tagFilter = null;';
	} 
	if(isset($saveSearchFilters['license'])){
		echo 'var licenseFilter = "'.$saveSearchFilters['license'].'";';
	} else {
		echo 'var licenseFilter = null;';
	} 		
	
	echo 'var searchUrl = "'. $searchUrl.'";';		
?>
</script>
<?php echo $this -> Html -> script('search-filters', array('inline' => false));?>
<div id="filters">
	<div class="search-query"><?php 
	if(isset($saveSearchFilters['search'])) {
		echo __('You searched for: ', true) .$saveSearchFilters['search'];
	} else if(isset($saveSearchFilters['tag'])){
		//Check to see if we did a tag search, if so then grab it from the saved tag
		$tag = $this -> Session -> read('Tag_Search.filter');
		echo '<span class="tag">'.$tag['Tag']['tag'] .'</span>';
	}
	?></div>
	
	<?php
		if(!$lockManufacturer){
			echo '<div class="filter manufacturer">';	
		} else {
			echo '<div class="filter manufacturer lock">';
		}
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
			echo '<span class="name">';
			echo $selectedMan;
			echo '</span>';
			if(!$lockManufacturer){
				echo '<a data-type="m" class="ui-icon ui-icon-close"></a>';	
			}
		} else {
			echo '<span class="name">';
			echo  __('Manufacturer', true);
			echo '</span>';
			if(!$lockManufacturer){
				echo '<a class="ui-icon ui-icon-triangle-1-s"></a>';
			}
		}

		echo '</div>';
		echo '<div class="filter-list-container">';
		echo '<div class="filter-list">';
		echo '<ol>';
		echo $manfilters;
		echo '</ol>';
		echo '</div>';
		echo '</div>';
		
		echo  '</div>';
	?>

	
	
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
				echo '<span class="name">';
				echo $selectedType;
				echo '</span>';
				echo '<a data-type="ct" class="ui-icon ui-icon-close"></a>';
			} else {
				echo '<span class="name">';
				echo  __('Type', true);
				echo '</span>';
				echo '<a class="ui-icon ui-icon-triangle-1-s"></a>';
			}
			echo '</div>';
			echo '<div class="filter-list-container">';
			echo '<div class="filter-list">';
			echo '<ol>';
			echo $typefilters;
			echo '</ol>';
			echo '</div>';
			echo '</div>';
		?>
	</div>
	
	<div class="filter license">
		<?php
			//First lets get all of the filters, cause we might need the name of one for the title
			$licenses = $this -> Session -> read('License_Search.filter');
			$typefilters = '';
		
			foreach ($licenses as $key => $value) {
				if(isset($saveSearchFilters['license']) && $saveSearchFilters['license'] == $value['License']['id']){
					$typefilters .='<li class="selected">';
					//if this is the name then grab the name
					$selectedLicense = $value['License']['name'];
				} else {
					$typefilters .='<li>';	
				}
				$typefilters .='<a class="filter-links" data-type="l" data-filter="'.$value['License']['id'].'">';
				$typefilters .=$value['License']['name'];
				$typefilters .='</a>';
				$typefilters .='</li>';	
			}
			
			echo '<div class="filter-name">';
			if(isset($selectedLicense)){
				echo '<span class="name">';
				echo $selectedLicense;
				echo '</span>';
				echo '<a data-type="l" class="ui-icon ui-icon-close"></a>';
			} else {
				echo '<span class="name">';
				echo  __('License', true);
				echo '</span>';
				echo '<a class="ui-icon ui-icon-triangle-1-s"></a>';
			}
			echo '</div>';
			echo '<div class="filter-list-container">';
			echo '<div class="filter-list">';
			echo '<ol>';
			echo $typefilters;
			echo '</ol>';
			echo '</div>';
			echo '</div>';
		?>
	</div>
</div>	