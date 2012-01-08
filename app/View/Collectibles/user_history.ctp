<?php echo $this->element('account_top'); ?>

	<div class="title">
		<h2><?php echo __('History'); ?></h2>
	</div>
	<?php echo $this -> element('flash');?>
	<div class="standard-list collectible-history-list">
		<ul>
			<li class="title">
				<span class="collectible-name"><?php echo $this->Paginator->sort('name', 'Name'); ?></span>
				<span class="collectible-manufacturer"><?php echo $this->Paginator->sort( 'manufacture_id', 'Manufacturer'); ?></span>
				<span class="collectible-type"><?php echo $this->Paginator->sort('collectibletype_id', 'Type'); ?></span>
				<span class="timestamp"><?php echo $this->Paginator->sort('created', 'Date Added'); ?></span>
				<span class="status"><?php echo $this->Paginator->sort('state', 'State'); ?></span>
			</li>
			<?php foreach($collectibles as $edit){
				echo '<li>';
				echo '<span class="collectible-name">';
				if($edit['Collectible']['state'] === '0') {
					echo $this -> Html -> link($edit['Collectible']['name'], '/collectibles/view/'.$edit['Collectible']['id'], array('class' => 'link'));
				} else {
					echo $edit['Collectible']['name'];	
				}
				
				echo '</span>';
				echo '<span class="collectible-manufacturer">';
				echo $edit['Manufacture']['title'];
				echo '</span>';
				echo '<span class="collectible-type">';
				echo $edit['Collectibletype']['name'];
				echo '</span>';
				echo '<span class="timestamp">';
				$datetime = strtotime($edit['Collectible']['created']);
				$mysqldate = date("m/d/y g:i A", $datetime);
				echo $mysqldate;
				echo '</span>';
				echo '<span class="status">';
				if($edit['Collectible']['state'] === '0') {
					echo __('Approved');
				} else if ($edit['Collectible']['state'] === '1'){
					echo __('Pending');
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

