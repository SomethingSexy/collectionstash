<div class="component" id="user-list-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Users'); ?></h2>
		</div>
		<div class="component-view">
		<div class="standard-list user-list">
			<ul>
				<li class="title">
					<span class="username"><?php echo $this -> Paginator -> sort('username', 'User Name'); ?></span>
					<span class="join-date"><?php echo $this -> Paginator -> sort('created', 'Join Date'); ?></span>
					<span class="count"><?php echo $this -> Paginator -> sort('collectibles_user_count', 'Collectible Count'); ?></span>
					<span class="upload-count"><?php echo $this -> Paginator -> sort('user_upload_count', 'Photo Count'); ?></span>
				</li>
				<?php
				foreach ($users as $user) {
					echo '<li>';
					echo '<span class="username">';
					echo $this -> Html -> link($user['User']['username'], array('admin' => false, 'controller' => 'stashs', 'action' => 'view', $user['User']['username']));
					echo '</span>';
					echo '<span class="join-date">';
					$datetime = strtotime($user['User']['created']);
					$mysqldate = date("m/d/y g:i A", $datetime);
					echo $mysqldate;
					echo '</span>';
					echo '<span class="count">';
					echo $user['User']['collectibles_user_count'];
					echo '</span>';
					echo '<span class="upload-count">';
					echo $user['User']['user_upload_count'];
					echo '</span>';
					echo '</li>';
				}
 ?>
			</ul>
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
