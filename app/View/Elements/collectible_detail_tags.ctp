<div class="collectible detail">
	<div class="detail title">
		<h3><?php echo __('Tags');?></h3>
		<?php
        if (isset($showEdit) && $showEdit) {
            echo '<div class="actions icon">';
            echo '<ul>';
            echo '<li>';
            if (isset($collectibleCore['CollectiblesTag']) && !empty($collectibleCore['CollectiblesTag'])) {
                if ($adminMode) {
                    echo $this -> Html -> link('<i class="icon-pencil icon-large"></i>', array('admin' => false, 'controller' => 'collectibles_tags', 'action' => 'edit', $collectibleCore['Collectible']['id'], 'true'), array('title' => 'Edit Tags', 'escape' => false));
                } else {
                    echo $this -> Html -> link('<i class="icon-pencil icon-large"></i>', array('admin' => false, 'controller' => 'collectibles_tags', 'action' => 'edit', $collectibleCore['Collectible']['id']), array('title' => 'Edit Tags', 'escape' => false));
                }
            } else {
                if ($adminMode) {
                    echo $this -> Html -> link($this -> Html -> image('/img/icon/add-gray.png'), array('admin' => false, 'controller' => 'collectibles_tags', 'action' => 'edit', $collectibleCore['Collectible']['id'], 'true'), array('title' => 'Add Tags', 'escape' => false));
                } else {
                    echo $this -> Html -> link($this -> Html -> image('/img/icon/add-gray.png'), array('admin' => false, 'controller' => 'collectibles_tags', 'action' => 'edit', $collectibleCore['Collectible']['id']), array('title' => 'Add Tags', 'escape' => false));
                }
            }
            echo '</li>';
            echo '</ul>';
            echo '</div>';
        }
		?>
	</div>
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