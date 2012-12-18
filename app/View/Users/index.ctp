<div class="component" id="user-list-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Users'); ?></h2>
		</div>
		<div class="component-view">
		<div class="standard-list user-list">
			<table class="table">
				<thead>
					<tr>
						<th><?php echo $this -> Paginator -> sort('username', 'User Name'); ?></th>
						<th><?php echo $this -> Paginator -> sort('created', 'Join Date'); ?></th>
						<th><?php echo $this -> Paginator -> sort('collectibles_user_count', 'Collectible Count'); ?></th>
						<th><?php echo $this -> Paginator -> sort('user_upload_count', 'Photo Count'); ?></th>
					</tr>					
				</thead>
				<tbody>
				<?php
				foreach ($users as $user) {
					echo '<tr>';
					echo '<td>';
					echo $this -> Html -> link($user['User']['username'] . ' (' . $user['User']['points'] . ')' , array('admin' => false, 'controller' => 'stashs', 'action' => 'view', $user['User']['username']));
					echo '</td>';
					echo '<td>';
					$datetime = strtotime($user['User']['created']);
					$mysqldate = date("m/d/y g:i A", $datetime);
					echo $mysqldate;
					echo '</td>';
					echo '<td>';
					echo $user['User']['collectibles_user_count'];
					echo '</td>';
					echo '<td>';
					echo $user['User']['user_upload_count'];
					echo '</td>';
					echo '</tr>';
				}
 ?>
 				</tbody>
			</table>
		</div>	
				<div class="paging">
			<p>
				<?php
				echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} users out of  {:count} total.', true)));
				?>
			</p>
			<?php

			$urlparams = $this -> request -> query;
			unset($urlparams['url']);
			$this -> Paginator -> options(array('url' => array('?' => http_build_query($urlparams))));

			echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));
			?>
			<?php echo $this -> Paginator -> numbers(array('separator' => false)); ?>
			<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
		</div>		

		</div>
	</div>
</div>
