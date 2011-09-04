<div class="collectible detail">
	<div class="detail title">
		<h3><?php __('Tags');?></h3>
	</div>
	<ul class="tag-list">
		<?php
		foreach ($collectibleCore['CollectiblesTag'] as $tag) {
			echo '<li class="tag">';
			echo $tag['Tag']['tag'];
			echo '</li>';
		}
		?>
	</ul>
</div>