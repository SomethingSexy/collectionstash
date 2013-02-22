<div class="well">
		<h3><?php echo __('Artists'); ?></h3>
	
	<?php
    echo '<ul class="unstyled">';
    if (isset($collectibleCore['ArtistsCollectible']) && !empty($collectibleCore['ArtistsCollectible'])) {
        foreach ($collectibleCore['ArtistsCollectible'] as $artist) {
            echo '<li class="">';
            echo  $this -> Html -> link($artist['Artist']['name'], array('admin' => false, 'controller' => 'artists', 'action' => 'index', $artist['Artist']['id'], $artist['Artist']['slug'])); 
            echo '</li>';
        }
    } else {
        echo '<li class="empty">No artists have been added.</li>';
    }
    echo '</ul>';
	?>
</div>