<script type="text/javascript"><?php
echo 'var searchUrl = "' . $searchUrl . '";';
if(isset($saveSearchFilters['search'])){
	echo 'var searchFilter= "' . $saveSearchFilters['search'] . '"';
} else {
	echo 'var searchFilter= ""';
}

?></script>
<?php 
echo $this -> Html -> script('thirdparty/uri', array('inline' => false));
echo $this -> Html -> script('views/view.filters', array('inline' => false)); 

/*<div class="search-query">
	
	if (isset($saveSearchFilters['search'])) {
		echo __('You searched for: ', true) . $saveSearchFilters['search'];
	} else if (isset($saveSearchFilters['tag'])) {
		echo '<span class="tag"><span class="tag-name">' . $saveSearchFilters['tag']['tag'] . '</span></span>';
	}
	
</div> */ ?>
<h4>Filter By </h4>
<div id="fancy-filters">
	<div class="filter-btn">
	<?php
		$count_values = array();
		foreach ($saveSearchFilters as $a) {
  			foreach ($a as $key => $b) {
  				if($key === 'type') {
  					if(!isset($count_values[$b])){
  						$count_values[$b] = 0;
  					}
    				$count_values[$b]++;
    			}
  			}
		}

		debug($count_values);

		foreach ($filters as $key => $filter) {
			if(isset($filter['user_selectable']) && $filter['user_selectable']) {
				echo '<button type="button" class="btn btn-default btn-block filter" data-type="' . $key . '" data-source-key="' . $filter['key'] .'" data-source="' . $filter['source'] .'" data-title="' . $filter['label'] . ' Filter" data-container="body" data-toggle="popover" data-placement="left" data-html="true" data-content=\'<div class="typeahead-container"><input id="search-input-tools" type="text" class="form-control typeahead input-lg" autocomplete="off" placeholder="Start typing to see list"></div></div>\'>' . $filter['label'];
				if(!empty($saveSearchFilters)){

				}
				
				echo '</button>';					
			}
		}
	?>
	</div>
</div>