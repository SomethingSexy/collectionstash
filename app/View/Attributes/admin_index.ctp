<div class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions');?>
		<div class="page">
			<div class="title">
				<h2>
					<?php echo __('Pending Items');?>
				</h2>				
			</div>
			<?php echo $this -> element('flash');?>
		<div class="standard-list attributes index">
			<table class="table">
				<thead>
					<tr>
						<th><?php echo $this -> Paginator -> sort('attribute_category_id', 'Category'); ?></th>
						<th><?php echo $this -> Paginator -> sort('name', 'Name'); ?></th>
						<th><?php echo __('Description'); ?></th>
						<th><?php echo $this -> Paginator -> sort('manufacture_id', 'Manufacturer'); ?></th>
						<th><?php echo $this -> Paginator -> sort('scale_id', 'Scale'); ?></th>
						<th> </th>
					
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ($attributes as $attribute) {
					echo '<tr>';
					echo '<td class="category">';
					echo $attribute['AttributeCategory']['path_name'];
					echo '</td>';
					echo '<td class="name">';
					if (empty($attribute['Attribute']['name'])) {
						echo '&nbsp;';
					} else {
						echo $attribute['Attribute']['name'];
					}

					echo '</td>';
					echo '<td class="description">';
					echo $attribute['Attribute']['description'];
					echo '</td>';
					echo '<td class="manufacturer">';
					echo $attribute['Manufacture']['title'];
					echo '</td>';
					echo '<td class="scale">';
					echo $attribute['Scale']['scale'];
					echo '</td>';
					echo '<td class="actions">';
					echo $this -> Html -> link('Approve', array('admin' => true, 'action'=> 'view', $attribute['Attribute']['id']), array('class' => 'link'));
					echo '</td>';
					echo '</tr>';

				}?>
				</tbody>
			</table>
		</div>		
		</div>
		
	</div>
</div>
