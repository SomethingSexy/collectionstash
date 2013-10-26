<div class="panel panel-default">
	<div class="panel-heading">	
		<h3 class="panel-title"><?php echo __('Tags'); ?></h3>
	</div>
	<div class="panel-body">
	<?php

	if (isset($collectibleCore['CollectiblesTag']) && !empty($collectibleCore['CollectiblesTag'])) {
		echo '<ul class="">';
		foreach ($collectibleCore['CollectiblesTag'] as $tag) {
			echo '<li class="tag"><span class="tag-name">';
			echo '<a href="/collectibles/search/?t=' . $tag['Tag']['id'] . '"';
			echo '>' . $tag['Tag']['tag'] . '</a>';
			echo '</span></li>';
		}
		echo '</ul>';
	} else {
		echo '<p>No tags have been added.</p>';
	}
	?>
	</div>
</div>