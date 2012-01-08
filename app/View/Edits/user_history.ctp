<?php echo $this->element('account_top'); ?>

	<div class="title">
		<h2><?php echo __('History'); ?></h2>
	</div>
	<?php echo $this -> element('flash');?>
	<div class="standard-list edit-history-list">
		<ul>
			<li class="title">
				<span class="collectible-name"><?php echo __('Name'); ?></span>
				<span class="type"><?php echo __('Type'); ?></span>
				<span class="timestamp"><?php echo $this->Paginator->sort('Date Added', 'created'); ?></span>
				<span class="status"><?php echo $this->Paginator->sort('State', 'status'); ?></span>
			</li>
			<?php foreach($edits as $edit){
				echo '<li>';
				echo '<span class="collectible-name">';
				echo $this -> Html -> link($edit['Collectible']['name'], array('admin'=> false, 'controller' => 'collectibles', 'action'=> 'view', $edit['Collectible']['id']));
				echo '</span>';
				echo '<span class="type">';
				echo $edit['type'];
				echo '</span>';
				echo '<span class="timestamp">';
				$datetime = strtotime($edit['Edit']['created']);
				$mysqldate = date("m/d/y g:i A", $datetime);
				echo $mysqldate;
				echo '</span>';
				echo '<span class="status">';
				if($edit['Edit']['status'] === '0') {
					echo __('Pending');
				} else if ($edit['Edit']['status'] === '1'){
					echo __('Approved');
				} else if ($edit['Edit']['status'] === '2'){
					echo __('Denied');
				}				
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

<?php echo $this->element('account_bottom'); ?>

