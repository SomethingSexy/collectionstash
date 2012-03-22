<div id="admin-edit" class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions');?>
		<div class="page">
			<div class="title">
				<h2><?php echo __('Edit View');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<div class="standard-list collectible-edit-list">
				<ul>
					<li class="title">
						<span class="collectible-id"><?php echo __('Id');?></span>
						<span class="user-id"><?php echo __('User Id');?></span>
						<span class="type"><?php echo __('Type');?></span>
						<span class="timestamp"><?php echo __('Timestamp');?></span>
						<span class="action"><?php echo __('Action');?></span>
					</li>
					<?php
                    foreach ($editDetail['Edits'] as $edit) {
                        echo '<li>';
                        echo '<span class="collectible-id">';
                        echo $this -> Html -> link($editDetail['Edit']['collectible_id'], array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $editDetail['Edit']['collectible_id']));
                        echo '</span>';
                        echo '<span class="user-id">';
                        echo $editDetail['User']['username'];
                        echo '</span>';
                        echo '<span class="type">';
                        echo $edit['edit_type'];
                        echo '</span>';
                        echo '<span class="timestamp">';
                        $datetime = strtotime($editDetail['Edit']['created']);
                        $mysqldate = date("m/d/y g:i A", $datetime);
                        echo $mysqldate;
                        echo '</span>';
                        echo '<span class="action">';
                        if ($edit['edit_type'] === 'Collectible') {
                            echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'collectible_edits', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
                        } else if ($edit['edit_type'] === 'Upload') {
                            echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'upload_edits', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
                        } else if ($edit['edit_type'] === 'Attribute') {
                            echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'attributes_collectibles_edits', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
                        } else if ($edit['edit_type'] === 'Tag') {
                            echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'collectibles_tags', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
                        }

                        echo '</span>';
                        echo '</li>';
                    }
					?>

				</ul>
			</div>
		</div>
	</div>
</div>