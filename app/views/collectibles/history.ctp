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
	        <div class="collectible detail">
			<div class="detail title">
				<h3><?php __('Accessories/Features History'); ?></h3>
			</div>
			<?php
					$lastKey = 0;
					$attributeEmpty = empty($attributeHistory);
					if($attributeEmpty){
						echo '<div class="attributes-list empty">';
						echo '<ul>';
						echo '<li>No Accessories/Features have been added</li>';	
						echo '</ul>';
						echo '</div>';						
					} else {
						$outputAttribtes = '';
						$added = false;
						foreach($attributeHistory as $key => $attribute) {
								$outputAttribtes .= '<li>' . '<span class="attribute-name"><a href="/attributesCollectibles/history/'.$attribute['AttributesCollectible']['id'] .'">' . $attribute['Attribute']['name'] . '</a></span>'. '<span class="attribute-description">' . $attribute['AttributesCollectible']['description']. '</span>';
								$outputAttribtes .= '<span class="attribute-status">';
								if($attribute['AttributesCollectible']['active']) {
									$outputAttribtes .= __('Active', true);
								} else {
									$outputAttribtes .= __('Removed', true);
								}
								$outputAttribtes .= '</span></li>';
								$added = true;
						}								
						
						if($added) {
							echo '<div class="attributes-list">';	
							echo '<ul>';
							echo '<li class="title">';
							echo '<span class="attribute-name">'.__('Part', true).'</span>';
							echo '<span class="attribute-description">'.__('Description', true).'</span>';
							echo '<span class="attribute-status">'.__('Status', true).'</span>';
							echo '</li>';
							echo $outputAttribtes;	
							echo '</ul>';
							echo '</div>';								
						} else {
							echo '<div class="attributes-list empty">';
							echo '<ul>';
							echo '<li>No Accessories/Features have been added</li>';	
							echo '</ul>';
							echo '</div>';								
						}						
					} ?>	
					</div>        	
		</div>
	</div>
</div>