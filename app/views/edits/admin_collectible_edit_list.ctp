<div id="admin-edit" class="two-column-page">
	<div class="inside">
	 	<div class="actions">
			<ul>
				<li>
					<h3><?php echo __('Admin');?></h3>
					<ul>
						<li><?php echo $this -> Html -> link('New Collectibles', '/admin/collectibles/index', array('class'=>'link')); ?></li>
						<li><?php echo $this -> Html -> link('Edits','/admin/edits/index', array('class'=>'link')); ?></li>							
					</ul>
				</li>
			</ul>	
		</div>
		<div class="page">
			<div class="title">
				<h2>
					<?php __('Edits');?>
				</h2>				
			</div>
			<?php echo $this -> element('flash');?>
			<div class="standard-list collectible-edit-list">
				<ul>
					<li class="title">
						<span class="collectible-id"><?php __('Id'); ?></span>
						<span class="user-id"><?php __('User Id'); ?></span>
						<span class="type"><?php __('Type'); ?></span>
						<span class="timestamp"><?php __('Timestamp'); ?></span>
						<span class="action"><?php __('Action'); ?></span>
					</li>
					<?php foreach($edits as $edit){
						echo '<li>';
						echo '<span class="collectible-id">';
						echo $html->link($edit['Edit']['collectible_id'], array('admin'=> false, 'controller' => 'collectibles', 'action'=> 'view', $edit['Edit']['collectible_id']));
						echo '</span>';
						echo '<span class="user-id">';
						echo $edit['User']['username'];
						echo '</span>';
						echo '<span class="type">';
						echo $edit['type'];
						echo '</span>';
						echo '<span class="timestamp">';
						$datetime = strtotime($edit['Edit']['created']);
						$mysqldate = date("m/d/y g:i A", $datetime);
						echo $mysqldate;
						echo '</span>';
						echo '<span class="action">';
						if($edit['type'] === 'Collectible') {
							echo $html->link('Approve', array('admin'=> true, 'controller' => 'collectible_edits', 'action'=> 'approval',$edit['Edit']['id'], $edit['Edit']['collectible_edit_id']));
						} else if ($edit['type'] === 'Upload'){
							echo $html->link('Approve', array('admin'=> true, 'controller' => 'upload_edits', 'action'=> 'approval',$edit['Edit']['id'], $edit['Edit']['upload_edit_id']));	
						} else if ($edit['type'] === 'Attribute'){
							echo $html->link('Approve', array('admin'=> true, 'controller' => 'attributes_collectibles_edits', 'action'=> 'approval',$edit['Edit']['id'], $edit['Edit']['attributes_collectibles_edit_id']));	
						}
						echo '</span>';	
						echo '</li>';																	
					} ?>
				</ul>
			</div>
			<div class="paging">
				<p>
					<?php
					echo $this -> Paginator -> counter( array('format' => __('Page %page% of %pages%, showing %current% edits out of %count% total.', true)));
					?>
				</p>
				<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
				<?php echo $this -> Paginator -> numbers();?>
				<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
			</div>
		</div>
		
	</div>
</div>
