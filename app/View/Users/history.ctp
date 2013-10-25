<div class="col-md-12 home">
	<div class="row spacer">
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo __('History'); ?></h3>
			</div>
				<?php
				if (isset($collectibles) && !empty($collectibles)) {
					echo $this -> element('stash_table_list', array('collectibles' => $collectibles, 'showThumbnail' => false, 'stashType' => 'default', 'history' => true));
				} else {
					echo '<p>' . __('You have no collectibles in your stash!', true) . '</p>';
				}
			?>				
		</div>

	</div>
	
</div>
