<div class="component" id="user-list-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Users');?></h2>
		</div>
		<div class="component-view">
		<div class="standard-list user-list">
			<ul>
				<li class="title">
					<span class="username"><?php echo $this->Paginator->sort('username', 'User Name'); ?></span>
					<span class="join-date"><?php echo $this->Paginator->sort('created', 'Join Date'); ?></span>
					<span class="count"><?php echo $this->Paginator->sort('collectibles_user_count', 'Collectible Count'); ?></span>
					<span class="upload-count"><?php echo $this->Paginator->sort('user_upload_count', 'Upload count'); ?></span>
				</li>
				<?php foreach($users as $user){
					echo '<li>';
					echo '<span class="username">';
					echo $this->Html->link($user['User']['username'], array('admin'=> false, 'controller' => 'stashs', 'action'=> 'view', $user['User']['username']));
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
				} ?>
			</ul>
		</div>			

		</div>
	</div>
</div>
