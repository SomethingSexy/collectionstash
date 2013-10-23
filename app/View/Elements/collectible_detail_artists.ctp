<h4><?php echo __('Artists'); ?></h4>
<?php

if (isset($collectibleCore['ArtistsCollectible']) && !empty($collectibleCore['ArtistsCollectible'])) {
	echo '<ul class="">';
	foreach ($collectibleCore['ArtistsCollectible'] as $artist) {
		echo '<li class="">';
		echo $this -> Html -> link($artist['Artist']['name'], array('admin' => false, 'controller' => 'artists', 'action' => 'index', $artist['Artist']['id'], $artist['Artist']['slug']));
		echo '</li>';
	}
	echo '</ul>';
} else {
	echo '<p>No artists have been added.</p>';
}

?>