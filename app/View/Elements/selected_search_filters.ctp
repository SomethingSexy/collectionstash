<?php if(!empty($saveSearchFilters)){ ?>
<div class="row spacer">
	<div class="col-md-12">
		<div class="selected-search-filters">
				<?php
					foreach ($saveSearchFilters as $key => $value) {
						echo '<span class="filter label label-info">';
						echo $value['label'];
						echo '<span data-role="remove"></span>';
						echo '</span>';
					}
				?>
		</div>
	</div>
</div>
<?php } ?>