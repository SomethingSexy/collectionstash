<div class="panel panel-default" id="user-list-component">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo __('Users'); ?></h3>
		</div>
		<div class="panel-body">
		<div class="standard-list user-list">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th><?php echo $this -> Paginator -> sort('username', 'User Name'); ?></th>
							<th><?php echo $this -> Paginator -> sort('created', 'Join Date'); ?></th>
							<th><?php echo $this -> Paginator -> sort('collectibles_user_count', 'Collectible Count'); ?></th>
							<th><?php echo $this -> Paginator -> sort('user_upload_count', 'Photo Count'); ?></th>
							<th><?php echo $this -> Paginator -> sort('points', 'Nuts'); ?></th>
						</tr>					
					</thead>
					<tbody>
					<?php
					foreach ($users as $user) {
						echo '<tr>';
						echo '<td>';
						echo $this -> Html -> link($user['User']['username'], array('admin' => false, 'controller' => 'users', 'action' => 'profile', $user['User']['username']));
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
						echo '<td>';
						echo $user['User']['points'];
						echo '</td>';
						echo '</tr>';
					}
	 ?>
	 				</tbody>
				</table>
			</div>
		</div>	
		<p>
			<?php
			echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} users out of  {:count} total.', true)));
			?>
		</p>
		<ul class="pagination">
			<?php
			$urlparams = $this -> request -> query;
			unset($urlparams['url']);
			$this -> Paginator -> options(array('url' => array('?' => http_build_query($urlparams))));
			?>
		<?php echo $this -> Paginator -> prev(__('previous', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled')); ?>
		<?php echo $this -> Paginator -> numbers(array('separator' => false, 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a')); ?>
		<?php echo $this -> Paginator -> next(__('next', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled')); ?>
		</ul>
	</div>
</div>
