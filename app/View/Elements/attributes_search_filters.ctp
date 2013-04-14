<script type="text/javascript"><?php
echo 'var searchUrl = "' . $searchUrl . '";';
?></script>
<?php echo $this -> Minify -> script('js/jquery.filters', array('inline' => false)); ?>
<div id="filters">
	<?php
	foreach ($filters as $key => $filterGroup) {
		$count = 0;
		if (isset($saveSearchFilters[$filterGroup['type']])) {
			$count = count($saveSearchFilters[$filterGroup['type']]);
		}


   		echo '<div class="btn-group filter" data-type="' . $filterGroup['type'] . '">';
        echo '<a href="#" data-toggle="dropdown" class="btn btn-info dropdown-toggle">';
        
		if ($count > 0) {
			echo $count . __(' selected');
		} else {
			echo $filterGroup['label'];
		}
		echo ' <span class="caret"></span>';
		echo '</a>';
        echo '<ul class="dropdown-menu">';
		foreach ($filterGroup['filters'] as $key => $filter) {
			echo '<li>';
			echo '<input  data-filter="' . $filter['id'] . '" class="filter-links" type="checkbox" value="'. $filter['id'] .'"';
			if (isset($saveSearchFilters[$filterGroup['type']]) && in_array($filter['id'], $saveSearchFilters[$filterGroup['type']])) {
				echo ' checked ';	
			}
			
			echo '/>';
			echo '<label for="">' . $filter['label'] . '</label>';
			echo '</li>';
		}
        echo '</ul>';
  
        echo '</div>';
	}

	?>
</div>
<script>
	$(function() {
		$('#filters').filters();
	}); 
</script>
