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
				echo '<div class="standard-list tag-list">';
                echo '<table class="table">';
                echo '<thead><tr>';
                echo '<th class="name">' . __('Tag', true) . '</th>';
                echo '<th class="action">' . __('Action', true) . '</th>';
                echo '</tr></thead><tbody>';
                foreach ($tags as $key => $value) {

                    echo '<tr>' . '<td class="name">' . $value['Tag']['tag'] . '</td>';
                    echo '<td class="action">';
                    if ($value['CollectiblesTag']['action'] === 'E') {
                        echo __('Edit', true);
                    } else if ($value['CollectiblesTag']['action'] === 'D') {
                        echo __('Delete', true);
                    } else if ($value['CollectiblesTag']['action'] === 'A') {
                        echo __('Add', true);
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
                echo '</div>';
				?>
			</div>
		</div>
	</div>
</div>