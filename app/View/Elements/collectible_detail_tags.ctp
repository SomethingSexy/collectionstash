<div class="widget">
	<div class="widget-header">	
		<h3><?php echo __('Tags');?></h3>
	</div>
	<div class="widget-content">
	<?php
    echo '<ul class="">';
    if (isset($collectibleCore['CollectiblesTag']) && !empty($collectibleCore['CollectiblesTag'])) {
        foreach ($collectibleCore['CollectiblesTag'] as $tag) {
            echo '<li class="tag"><span class="tag-name">';
            echo '<a href="/collectibles/search/?t=' . $tag['Tag']['id'] . '"';
            echo '>' . $tag['Tag']['tag'] . '</a>';
            echo '</span></li>';
        }
    } else {
        echo '<li class="empty">No tags have been added.</li>';
    }
    echo '</ul>';
	?>
	</div>
</div>