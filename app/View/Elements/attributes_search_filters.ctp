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

		echo '<div class="filter" data-type="' . $filterGroup['type'] . '">';
		echo '<div class="filter-name">';
		echo '<span class="name">';
		if ($count > 0) {
			echo $count . __(' selected');
		} else {
			echo $filterGroup['label'];
		}

		echo '</span>';
		if ($count > 0) {
			echo '<a class="ui-icon ui-icon ui-icon-close"></a>';
		} else {
			echo '<a class="ui-icon ui-icon-triangle-1-s"></a>';
		}
		
		echo '</div>';
		echo '<div class="filter-list-container">';
		echo '<div class="filter-list">';
		echo '<ol>';
		foreach ($filterGroup['filters'] as $key => $filter) {
			if (isset($saveSearchFilters[$filterGroup['type']]) && in_array($filter['id'], $saveSearchFilters[$filterGroup['type']])) {
				echo '<li class="selected">';
			} else {
				echo '<li>';
			}
			echo '<a class="filter-links"  data-filter="' . $filter['id'] . '">';
			echo $filter['label'];
			echo '</a>';
			echo '</li>';
		}
		echo '</ol>';
		echo '</div>';
		echo '</div>';

		echo '</div>';
	}
	?>
</div>
<script>
	$(function() {
		$('#filters').filters();
	}); 
</script>