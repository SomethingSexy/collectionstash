<script type="text/javascript"><?php
echo 'var searchUrl = "' . $searchUrl . '";';
echo 'var searchFilter= "' . $saveSearchFilters['search'] . '"';
?></script>
<?php echo $this -> Html -> script('jquery.filters', array('inline' => false)); ?>

<div class="search-query">
	<?php
	if (isset($saveSearchFilters['search'])) {
		echo __('You searched for: ', true) . $saveSearchFilters['search'];
	} else if (isset($saveSearchFilters['tag'])) {
		echo '<span class="tag"><span class="tag-name">' . $saveSearchFilters['tag']['tag'] . '</span></span>';
	}
	?>
</div>

<div id="filters">
	<?php
	foreach ($filters as $key => $filterGroup) {
		$count = 0;
		if (isset($saveSearchFilters[$filterGroup['type']])) {
			$count = count($saveSearchFilters[$filterGroup['type']]);
		}

		echo '<div class="btn-group filter checkbox-select" data-type="' . $filterGroup['type'] . '" data-allow-multiple="' .$filterGroup['allowMultiple'] . '">';
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
			echo '<label>';
			echo '<input  data-filter="' . $filter['id'] . '" class="filter-links" type="checkbox" value="' . $filter['id'] . '"';
			if (isset($saveSearchFilters[$filterGroup['type']]) && in_array($filter['id'], $saveSearchFilters[$filterGroup['type']])) {
				echo ' checked ';
			}

			echo '/>';			
			echo '<span>' . $filter['label'] . '</span>';
			echo '</label>';
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
