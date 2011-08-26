<div id="admin-edit" class="two-column-page">
	<div class="inside">
	 	<div class="actions">
			<ul>
				<li><?php echo $html->link('New Collectibles', array('admin'=> true, 'controller' => 'collectibles')); ?></li>
				<li><?php echo $html->link('Edits', array('admin'=> true, 'controller' => 'edits')); ?></li>
			</ul>	
		</div>
		<div class="page">
			<div class="title">
				<h2>
					<?php __('Edits');?>
				</h2>				
			</div>
			<div class="standard-list">
				<ul>
					<li class="title">
						<span><?php __('Id'); ?></span>
						<span><?php __('User Id'); ?></span>
						<span><?php __('Type'); ?></span>
						<span><?php __('Timestamp'); ?></span>
						<span><?php __('Action'); ?></span>
					</li>
					<?php foreach($edits as $edit){
						echo '<li>';
						echo '<span>';
						echo $edit['User']['username'];
						echo '</span>';
						echo '<span>';
						echo $edit['type'];
						echo '</span>';
						echo '<span>';
						echo $edit['Edit']['created'];
						echo '</span>';
						echo '<span>';
						echo 'approved';
						echo '</span>';	
						echo '</li>';																	
					} ?>
				</ul>
			</div>
			<div class="paging">
				<p>
					<?php
					echo $this -> Paginator -> counter( array('format' => __('Page %page% of %pages%, showing %current% collectibles out of %count% total.', true)));
					?>
				</p>
				<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
				<?php echo $this -> Paginator -> numbers();?>
				<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
			</div>
		</div>
		
	</div>
</div>
