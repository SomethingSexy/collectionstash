<script type="text/javascript"><?php
//echo 'var searchUrl = "' . $searchUrl . '";';
if(isset($saveSearchFilters['search'])){
	echo 'var searchFilter= "' . $saveSearchFilters['search'] . '"';
} else {
	echo 'var searchFilter= ""';
}

?></script>
<?php 
echo $this -> Html -> script('/bower_components/select2/select2', array('inline' => false));
echo $this -> Html -> css('/bower_components/select2/select2');
echo $this -> Html -> script('thirdparty/uri', array('inline' => false));
echo $this -> Html -> script('views/app/collectible/search/view.filters', array('inline' => false)); ?>
<h4>Filter By </h4>
<div id="fancy-filters">
	<div class="filter-btn">
	<?php
		$count_values = array();
		if(isset($saveSearchFilters)){
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
		}

		foreach ($filters as $key => $filter) {
			if(isset($filter['user_selectable']) && $filter['user_selectable']) {
				echo '<h5>' .$filter['label'];
				if(isset($count_values[$key])){
					echo '<span class="badge pull-right">' . $count_values[$key]. '</span>';
				}
				echo '</h5>';
				if(isset($filter['values'])){
					echo '<select data-multiple="' . $filter['multiple'] . '" data-type="' . $key .'"><option></option>'; 
					foreach ($filter['values'] as $key => $value) {
						echo "<option value='" . $key . "'>" . $value . '</option>';
					}
					echo '</select>';
				}				
			}
		}
	?>
	</div>
</div>