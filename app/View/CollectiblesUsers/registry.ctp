<div class="component" id="registry-component">
	<div class="inside">
		<div class="component-title">
			<h2><h2><?php  echo __('Registry'); ?></h2></h2>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-info">
			<div>
				<?php echo __('The registry is a list of members who own this collectible.')
				?>
			</div>
		</div>		
		<div class="component-view">
			<?php
			if (isset($registry) && !empty($registry)) {
				echo '<table class="table">';
				echo '<tr>';
				echo '<th>' . __('User name', true) . '</th>';
				if ($showEditionSize) {
					echo '<th>' . __('Edition Number', true) . '</th>';
				}
				echo '</tr>';
				foreach ($registry as $key => $value) {
					if ($value['User']['Stash'][0]['privacy'] === '0') {
						echo '<tr>';
						echo '<td>';
						echo '<a href="/stashs/view/' . $value['User']['username'] . '" class="link">' . $value['User']['username'] . '</a>';
						echo '</td>';
						if ($showEditionSize) {
							echo '<td>';
							if (!empty($value['CollectiblesUser']['edition_size'])) {
								echo $value['CollectiblesUser']['edition_size'];
							} else {
								echo __('Not recorded');
							}
							echo '</td>';
						}
						echo '</tr>';
					}
				}
				echo '</table>';
			} else {
				echo '<div class="standard-list empty">';
				echo '<ul class="unstyled">';
				echo '<li>No one owns this collectible.</li>';
				echo '</ul>';
				echo '</div>';
			}
			?>
		</div>
	</div>
</div>
