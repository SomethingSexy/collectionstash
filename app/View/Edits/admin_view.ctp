<div id="admin-edit" class="row">
	<?php echo $this -> element('admin_actions'); ?>
	<div class="col-md-8">
		<div class="page">
			<div class="title">
				<h2><?php echo __('Edit View'); ?></h2>
			</div>
			<?php echo $this -> element('flash'); ?>
			<div class="standard-list collectible-edit-list">
				<table class="table">
					<thead>	
						<tr>
							<td><?php echo __('Id'); ?></td>
							<td><?php echo __('User Id'); ?></td>
							<td><?php echo __('Type'); ?></td>
							<td><?php echo __('Timestamp'); ?></td>
							<td><?php echo __('Action'); ?></td>
						</tr>
					</thead>
					<?php
					foreach ($editDetail['Edits'] as $edit) {
						echo '<tr>';
						echo '<td class="collectible-id">';

						if ($edit['edit_type'] === 'Collectible') {
							echo $this -> Html -> link($edit['base_id'], array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $edit['base_id']));
						} else if ($edit['edit_type'] === 'Attribute') {
							echo $this -> Html -> link($edit['base_id'], array('admin' => false, 'controller' => 'attributes', 'action' => 'view', $edit['base_id']));
						} else if ($edit['edit_type'] === 'Upload') {
							echo $this -> Html -> link($edit['collectible_id'], array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $edit['collectible_id']));
						} else if ($edit['edit_type'] === 'AttributesCollectible') {
							echo $this -> Html -> link($edit['collectible_id'], array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $edit['collectible_id']));
						} else if ($edit['edit_type'] === 'Tag') {
							echo $this -> Html -> link($edit['collectible_id'], array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $edit['collectible_id']));
						} else if ($edit['edit_type'] === 'CollectiblesUpload') {
							echo $this -> Html -> link($edit['collectible_id'], array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $edit['collectible_id']));
						} else if ($edit['edit_type'] === 'ArtistsCollectible') {
							echo $this -> Html -> link($edit['collectible_id'], array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $edit['collectible_id']));
						} else if ($edit['edit_type'] === 'AttributesUpload') {
							echo $this -> Html -> link($edit['attribute_id'], array('admin' => false, 'controller' => 'attributes', 'action' => 'view', $edit['attribute_id']));
						}
						echo '</td>';
						echo '<td class="user-id">';
						echo $editDetail['User']['username'];
						echo '</td>';
						echo '<td class="type">';
						echo $edit['edit_type'];
						echo '</td>';
						echo '<td class="timestamp">';
						$datetime = strtotime($editDetail['Edit']['created']);
						$mysqldate = date("m/d/y g:i A", $datetime);
						echo $mysqldate;
						echo '</td>';
						echo '<td class="action">';
						if ($edit['edit_type'] === 'Collectible') {
							echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'collectibles', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
						} else if ($edit['edit_type'] === 'Attribute') {
							echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'attributes', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
						} else if ($edit['edit_type'] === 'Upload') {
							echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'upload_edits', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
						} else if ($edit['edit_type'] === 'AttributesCollectible') {
							echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'attributes_collectibles', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
						} else if ($edit['edit_type'] === 'Tag') {
							echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'collectibles_tags', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
						} else if ($edit['edit_type'] === 'CollectiblesUpload') {
							echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'collectibles_uploads', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
						} else if ($edit['edit_type'] === 'ArtistsCollectible') {
							echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'artists_collectibles', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
						} else if ($edit['edit_type'] === 'AttributesUpload') {
							echo $this -> Html -> link('Approve', array('admin' => true, 'controller' => 'attributes_uploads', 'action' => 'approval', $editDetail['Edit']['id'], $edit['id']));
						}

						echo '</td>';
						echo '</tr>';
					}
					?>

				</table>
			</div>
		</div>
	</div>
</div>