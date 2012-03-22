<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Confirm Edit Collectible Tags')
			?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<?php
        if (isset($errors)) {
            echo $this -> element('errors', array('errors' => $errors));
        }
		?>
		<div class="component-info">
			<div>
				<p>
					<?php echo __('You have submitted for approval the following changes to the Tags for this collectible.');?>
				</p>
				<p>
					<?php echo $this -> Html -> link('Return to Collectible', array('admin' => false, 'controller' => '/collectibles', 'action' => 'view', $collectibleId));?>
				</p>
			</div>
		</div>
		<div class="component-view">
			<div class="collectible detail">
				<?php
                foreach ($tags as $key => $value) {
                    echo '<div class="standard-list tag-list">';
                    echo '<ul>';
                    echo '<li class="title">';
                    echo '<span class="name">' . __('Tag', true) . '</span>';
                    echo '<span class="action">' . __('Action', true) . '</span>';
                    echo '</li>';
                    echo '<li>' . '<span class="name">' . $value['Tag']['tag'] . '</span>';
                    echo '<span class="action">';
                    if ($value['CollectiblesTag']['action'] === 'E') {
                        echo __('Edit', true);
                    } else if ($value['CollectiblesTag']['action'] === 'D') {
                        echo __('Delete', true);
                    } else if ($value['CollectiblesTag']['action'] === 'A') {
                        echo __('Add', true);
                    }
                    echo '</span>';
                    echo '</li>';
                    echo '</ul>';
                    echo '</div>';
                }
				?>
			</div>
		</div>
	</div>
</div>