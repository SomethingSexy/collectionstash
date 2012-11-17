<?php echo $this -> element('account_top'); ?>

	<div class="title">
		<h2><?php echo __('History'); ?></h2>
	</div>
	<?php echo $this -> element('flash'); ?>
	<div class="standard-list edit-history-list">
		<table class="table">
			<tr class="title">
				<th><?php echo __('Name'); ?></th>
				<th><?php echo __('Details'); ?></th>
				<th><?php echo $this -> Paginator -> sort('created', 'Date Added'); ?></th>
				<th><?php echo $this -> Paginator -> sort('status', 'State'); ?></th>
			</tr>
			<?php
			foreach ($edits as $edit) {
				echo '<tr>';
				echo '<td class="collectible-name">';
				if ($edit['Edits'][0]['edit_type'] === 'Attribute') {
					echo $this -> Html -> link($edit['Edits'][0]['base_id'], array('admin' => false, 'controller' => 'attributes', 'action' => 'view', $edit['Edits'][0]['base_id']));
				} else {
					echo $this -> Html -> link($edit['Edits'][0]['base_id'], array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $edit['Edits'][0]['base_id']));
				}

				echo '</td>';
				echo '<td class="type">';
				echo __('Future Use');
				echo '</td>';
				echo '<td class="timestamp">';
				$datetime = strtotime($edit['Edit']['created']);
				$mysqldate = date("m/d/y g:i A", $datetime);
				echo $mysqldate;
				echo '</td>';
				echo '<td class="status">';
				if ($edit['Edit']['status'] === '0') {
					echo __('Pending');
				} else if ($edit['Edit']['status'] === '1') {
					echo __('Approved');
				} else if ($edit['Edit']['status'] === '2') {
					echo __('Denied');
				}
				echo '</td>';

				echo '</tr>';
			}
 ?>
		</table>
	</div>
	<div class="paging">
		<p>
			<?php
			echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
			?>
		</p>
		<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled')); ?>
		<?php echo $this -> Paginator -> numbers(array('separator' => false)); ?>
		<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
	</div>

<?php echo $this -> element('account_bottom'); ?>

