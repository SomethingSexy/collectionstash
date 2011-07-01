<div class="component" id="collectible-history-list-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo __('History');?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
	        <table class="history">
	        	<tr>
	        		<th><?php echo __('User');?></th>
	        		<th><?php echo __('Time of Edit');?></th>
	        	</tr>
	        <?php  
	        foreach ($history as $historyEntry):
	        ?>
				<tr>
					<td><?php echo $historyEntry['Collectible']['user_name']; ?></td>
					<td><?php 
						$datetime = strtotime($historyEntry['Collectible']['version_created']);
						$mysqldate = date("m/d/y g:i A", $datetime);
					echo $html->link($mysqldate, array('action' => 'historyDetail', $historyEntry['Collectible']['id'], $historyEntry['Collectible']['version_id']));?></td>
				</tr>
	        <?php endforeach; ?>  
	        </table>	
		</div>
	</div>
</div>