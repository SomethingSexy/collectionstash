<div class="component" id="registry-component">
	<div class="inside">
		<div class="component-title">
			<h2><h2><?php  echo __('Registry');?></h2></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				<?php echo __('The registry is a list of members who own this collectible.')
				?>
			</div>
		</div>		
		<div class="component-view">
			<?php
			if (isset($registry) && !empty($registry)) {
				if ($showEditionSize) {
					echo '<div class="standard-list edition-size">';
				} else {
					echo '<div class="standard-list">';
				}	
				echo '<ul>';
				echo '<li class="title">';
				echo '<span class="username">' . __('User name', true) . '</span>';
				if ($showEditionSize) {
					echo '<span class="edition-size">' . __('Edition Number', true) . '</span>';
				}
				echo '</li>';
				foreach ($registry as $key => $value) {
					if ($value['User']['Stash'][0]['privacy'] === '0') {
						echo '<li>';
						echo '<span class="username">';
						echo '<a href="/stashs/view/'.$value['User']['username'].'" class="link">'.$value['User']['username'].'</a>';
						echo '</span>';
						if ($showEditionSize) {
							echo '<span class="edition-size">';
							if(!empty($value['CollectiblesUser']['edition_size'])) {
								echo $value['CollectiblesUser']['edition_size'];	
							} else {
								echo __('Not recorded');
							}
							
						}
						echo '</span>';
						echo '</li>';
					}
				}
				echo '</ul>';
				echo '</div>';
			} else {
				echo '<div class="standard-list empty">';
				echo '<ul>';
				echo '<li>No one owns this collectible.</li>';
				echo '</ul>';
				echo '</div>';
			}
			?>
		</div>
	</div>
</div>
