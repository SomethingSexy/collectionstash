<?php echo $this -> element('admin_actions'); ?>
<div class="col-md-10">
	<div class="title">
		<h2>
			<?php echo __('Edits'); ?>
		</h2>				
	</div>
	<?php echo $this -> element('flash'); ?>
	<div class="standard-list edit-list">
		<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th><?php echo __('Date'); ?></th>
					<th><?php echo __('User'); ?></th>
					<th><?php echo __('Type'); ?></th>
					<th><?php echo __('Action'); ?></th>
				</tr>
			</thead>
			<?php
			foreach ($edits as $edit) {
				echo '<tr>';
				echo '<td class="date">';
				$datetime = strtotime($edit['Edit']['created']);
				$mysqldate = date("m/d/y g:i A", $datetime);
				echo $mysqldate;
				echo '</td>';
				echo '<td class="user">';
				echo $edit['User']['username'];
				echo '</td>';
				echo '<td class="type">';
				// We only support 1 right now
				if (!empty($edit['Edits'])) {
					echo $edit['Edits'][0]['edit_type'];
				}
				echo '</td>';
				echo '<td class="action">';
				echo $this -> Html -> link('View', array('admin' => true, 'action' => 'view', $edit['Edit']['id']));
				echo '</td>';
				echo '</tr>';
					}
 ?>
			</table>
			</div>
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
</div>
