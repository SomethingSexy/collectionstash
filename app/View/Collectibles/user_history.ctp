<?php echo $this->element('account_top'); ?>

	<div class="title">
		<h2><?php echo __('History'); ?></h2>
	</div>
	<?php echo $this -> element('flash');?>
	<div class="standard-list collectible-history-list">
		<table class="table">
			<tr class="title">
				<th><?php echo $this->Paginator->sort('name', 'Name'); ?></th>
				<th><?php echo $this->Paginator->sort( 'manufacture_id', 'Manufacturer'); ?></th>
				<th><?php echo $this->Paginator->sort('collectibletype_id', 'Type'); ?></th>
				<th><?php echo $this->Paginator->sort('created', 'Date Added'); ?></th>
				<th><?php echo $this->Paginator->sort('state', 'State'); ?></th>
			</tr>
			<?php foreach($collectibles as $edit){
				echo '<tr>';
				echo '<td class="collectible-name">';
				if($edit['Collectible']['state'] === '0') {
					echo $this -> Html -> link($edit['Collectible']['name'], '/collectibles/view/'.$edit['Collectible']['id'], array('class' => 'link'));
				} else {
					echo $edit['Collectible']['name'];	
				}
				
				echo '</td>';
				echo '<td class="collectible-manufacturer">';
				echo $edit['Manufacture']['title'];
				echo '</td>';
				echo '<td class="collectible-type">';
				echo $edit['Collectibletype']['name'];
				echo '</td>';
				echo '<td class="timestamp">';
				$datetime = strtotime($edit['Collectible']['created']);
				$mysqldate = date("m/d/y g:i A", $datetime);
				echo $mysqldate;
				echo '</td>';
				echo '<td class="status">';
				if($edit['Collectible']['state'] === '0') {
					echo __('Approved');
				} else if ($edit['Collectible']['state'] === '1'){
					echo __('Pending');
				}
				echo '</td>';	
				echo '</tr>';																	
			} ?>
		</table>
	</div>
	<div class="paging">
		<p>
			<?php
			echo $this -> Paginator -> counter( array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
			?>
		</p>
		<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
		<?php echo $this -> Paginator -> numbers(array('separator'=> false));?>
		<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>

<?php echo $this->element('account_bottom'); ?>

