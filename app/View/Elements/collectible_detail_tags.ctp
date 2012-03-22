<div class="collectible detail">
	<div class="detail title">
		<h3><?php echo __('Tags');?></h3>
	</div>
	<ul class="tag-list no-link">
		<?php
        foreach ($collectibleCore['CollectiblesTag'] as $tag) {
            echo '<li class="tag"><span class="tag-name">';
            echo $tag['Tag']['tag'];
            echo '</span></li>';
        }
		?>
	</ul>
</div>