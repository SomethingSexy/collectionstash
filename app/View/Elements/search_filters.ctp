<script type="text/javascript"><?php
echo 'var searchUrl = "' . $searchUrl . '";';
if(isset($saveSearchFilters['search'])){
	echo 'var searchFilter= "' . $saveSearchFilters['search'] . '"';
} else {
	echo 'var searchFilter= ""';
}

?></script>
<?php echo $this -> Html -> script('views/view.filters', array('inline' => false)); ?>

<!--<div class="search-query">
	<?php
	if (isset($saveSearchFilters['search'])) {
		echo __('You searched for: ', true) . $saveSearchFilters['search'];
	} else if (isset($saveSearchFilters['tag'])) {
		echo '<span class="tag"><span class="tag-name">' . $saveSearchFilters['tag']['tag'] . '</span></span>';
	}
	?>
</div> -->

<div id="fancy-filters">
	<?php
		foreach ($filters as $key => $filter) {
			if(isset($filter['user_selectable']) && $filter['user_selectable']) {
				echo '<button type="button" class="btn btn-default btn-lg btn-block filter" data-type="' . $key . '" data-source-key="' . $filter['key'] .'" data-source="' . $filter['source'] .'" data-title="' . $filter['label'] . ' Filter" data-container="body" data-toggle="popover" data-placement="left" data-html="true" data-content=\'<div class="typeahead-container"><input id="search-input-tools" type="text" class="form-control typeahead input-lg" autocomplete="off" placeholder="Start typing to see list"></div></div>\'>' . $filter['label'] .'</button>';				
			}
		}
	?>
</div>