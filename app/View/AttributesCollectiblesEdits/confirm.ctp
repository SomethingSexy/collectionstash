<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo __('Confirm Edit Collectible Attributes')?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<?php 
			if(isset($errors)) {
				echo $this -> element('errors', array('errors' => $errors));	
			}
		?>
		<div class="component-info">
			<div>
				<p><?php echo __('You have submitted for approval the following changes to the Accessories and Features for this collectible.'); ?></p>
				<p><?php echo $html -> link('Return to Collectible', array('admin'=> false, 'controller' => '/collectibles','action'=>'view', $collectibleId));?></p>	
			</div>
		</div>
		<div class="component-view">
			<div class="collectible detail">
			<?php
			$attributeEmpty = empty($updatedAttributes);
			if ($attributeEmpty) {
				echo '<div class="attributes-list empty">';
				echo '<ul>';
				echo '<li>You did not add or update any Accessories/Features.</li>';
				echo '</ul>';
				echo '</div>';
			} else {
				$outputAttribtes = '';
				$added = false;
				foreach ($updatedAttributes as $key => $attribute) {
						$outputAttribtes .= '<li>' . '<span class="attribute-name">' . $attribute['name'] . '</span>' . '<span class="attribute-description">' . $attribute['description'] . '</span>'; 
						$outputAttribtes .= '<span class="attribute-action">';
						if($attribute['action'] === 'E'){
							$outputAttribtes .= __('Edit', true);
						} else if($attribute['action'] === 'D'){
							$outputAttribtes .= __('Delete', true);
						}else if($attribute['action'] === 'A'){
							$outputAttribtes .= __('Add', true);
						}
						$outputAttribtes .= '</span>';
						$outputAttribtes .= '</li>';
						$added = true;
				}

				if ($added) {
					echo '<div class="attributes-list">';
					echo '<ul>';
					echo '<li class="title">';
					echo '<span class="attribute-name">' . __('Part', true) . '</span>';
					echo '<span class="attribute-description">' . __('Description', true) . '</span>';
					echo '<span class="attribute-action">' . __('Action', true) . '</span>';
					echo '</li>';
					echo $outputAttribtes;
					echo '</ul>';
					echo '</div>';
				} else {
					echo '<div class="attributes-list empty">';
					echo '<ul>';
					echo '<li>You did not add or update any Accessories/Features.</li>';
					echo '</ul>';
					echo '</div>';
				}
			}
			?>
			</div>
		</div>
	</div>
</div>