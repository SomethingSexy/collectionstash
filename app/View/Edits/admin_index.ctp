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
			<div class="standard-list edit-list">
				<ul>
					<li class="title">
						<span class="collectible-id"><?php __('Id'); ?></span>
						<span class="name"><?php __('Name'); ?></span>
						<span class="action"><?php __('Action'); ?></span>
					</li>
					<?php foreach($edits as $edit){
						echo '<li>';
						echo '<span class="collectible-id">';
						echo $this -> Html -> link($edit['Edit']['collectible_id'], array('admin'=> false, 'controller' => 'collectibles', 'action'=> 'view', $edit['Edit']['collectible_id']));
						echo '</span>';
						echo '<span class="name">';
						echo $edit['Collectible']['name'];
						echo '</span>';
						echo '<span class="action">';
						echo $this -> Html -> link('View', array('admin'=> true, 'action'=> 'collectibleEditList', $edit['Edit']['collectible_id']));
						echo '</span>';	
						echo '</li>';																	
					} ?>
				</ul>
			</div>
			<div class="paging">
				<p>
					<?php
					echo $this -> Paginator -> counter( array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
					?>
				</p>
				<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
				<?php echo $this -> Paginator -> numbers();?>
				<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
			</div>
		</div>
		
	</div>
</div>
