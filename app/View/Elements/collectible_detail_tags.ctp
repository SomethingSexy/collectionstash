<div class="well">
		<h3><?php echo __('Tags');?></h3>
	
	<?php
    echo '<div class="collectible tags">';
    echo '<ul class="tag-list unstyled">';
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
    echo '</div>';
	?>
</div>