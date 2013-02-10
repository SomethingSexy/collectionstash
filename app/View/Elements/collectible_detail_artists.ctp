<div class="well">
		<h3><?php echo __('Artists');?></h3>
	
	<?php
    echo '<ul class="unstyled">';
    if (isset($collectibleCore['ArtistsCollectible']) && !empty($collectibleCore['ArtistsCollectible'])) {
        foreach ($collectibleCore['ArtistsCollectible'] as $artist) {
            echo '<li class="">';
            echo $artist['Artist']['name'];
            echo '</li>';
        }
    } else {
        echo '<li class="empty">No artists have been added.</li>';
    }
    echo '</ul>';
	?>
</div>