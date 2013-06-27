<div class="widget">
		<div class="widget-header">	
			<h3><?php  echo __('Registry'); ?></h3>
		</div>
		<div class="widget-content">
		<?php echo $this -> element('flash'); ?>
			<p>
				<?php echo __('The registry is a list of members who own this collectible.')
				?>
			</p>	
			<?php
			if (isset($registry) && !empty($registry)) {
				echo '<h4>Owned:</h4>';
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
						echo '<a href="/stash/' . $value['User']['username'] . '" class="link">' . $value['User']['username'] . '</a>';
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
					} else {
						echo '<tr><td> Private Stash</td><td> - </td></tr>';
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

			if (isset($wishlist) && !empty($wishlist)) {
				echo '<h4>Wishlisted:</h4>';
				echo '<table class="table">';
				echo '<tr>';
				echo '<th>' . __('User name', true) . '</th>';
				echo '</tr>';
				foreach ($wishlist as $key => $value) {
					if ($value['User']['Stash'][0]['privacy'] === '0') {
						echo '<tr>';
						echo '<td>';
						echo '<a href="/wishlist/' . $value['User']['username'] . '" class="link">' . $value['User']['username'] . '</a>';
						echo '</td>';
						echo '</tr>';
					} else {
						echo '<tr><td> Private Stash</td></tr>';
					}
				}
				echo '</table>';
			}
			?>
		</div>
</div>
