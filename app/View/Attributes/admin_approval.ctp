<div id="admin-edit" class="row">
	<?php echo $this -> element('admin_actions'); ?>
	<div class="span8">
	
		<div class="page">
			<div class="title">
				<h2> <?php echo __('Edit Details'); ?> </h2>
			</div>
			<?php echo $this -> element('flash'); ?>
			<div class="detail-wrapper">
				<div class="attribute detail attribute-data">
					<div class="detail title">
						<h3><?php echo __('Type of Edit', true); ?></h3>
					</div>
					<div class="directional-text">
						<?php echo __('If this item is being deleted there can be a replacement item. This means that the item being deleted will be replaced with the replacement item attached.  All collectibles that have the item being deleted will now reference the replacement item.'); ?>
					</div>
					<dl>
						<dt>
							<?php echo __('Submitted By'); ?>
						</dt>
						<dd>
							<?php

							if (!empty($attribute['User']['username'])) {
								echo $attribute['User']['username'];
							} else {
								echo '&nbsp;';
							}
 							?>
						</dd>
						<dt>
							<?php echo __('Action'); ?>
						</dt>
						<dd>
							<?php
							if ($attribute['Action']['action_type_id'] === '1') {
								echo 'Add';
							} else if ($attribute['Action']['action_type_id'] === '2') {
								echo 'Edit';
							} else if ($attribute['Action']['action_type_id'] === '4') {
								echo 'Delete';
							} else {
								echo '&nbsp;';
							}
 							?>
						</dd>
						<dt>
							<?php echo __('Reason'); ?>
						</dt>
						<dd>
							<?php
							if (empty($attribute['Action']['reason'])) {
								echo 'N/A';
							} else {
								echo $attribute['Action']['reason'];
							}
 							?>
						</dd>
							<?php
							// If it is a delete then see if there is a link
							if ($attribute['Action']['action_type_id'] === '4') {
								echo '<dt>';
								echo __('Replacement');
								echo '</dt>';
								echo '<dd>';
								if (isset($attribute['Attribute']['replace_attribute_id']) && !empty($attribute['Attribute']['replace_attribute_id'])) {
									echo 'Yes';
								} else {
									echo 'No';
								}
								echo '</dd>';

								if (isset($attribute['Attribute']['replace_attribute_id']) && !empty($attribute['Attribute']['replace_attribute_id'])) {
									echo '<dt>';
									echo __('Replacement Attribute');
									echo '</dt>';
									echo '<dd>';
									echo $this -> Html -> link($attribute['Attribute']['replace_attribute_id'], array('admin' => true, 'controller' => 'attributes', 'action' => 'view', $attribute['Attribute']['replace_attribute_id']));
									echo '</dd>';
								}

							}
 							?>						
						
					</dl>
					
					<div class="detail title">
						<h3><?php echo __('Item Details', true); ?></h3>
					</div>
					<dl>
						<dt>
							<?php echo __('Date Added'); ?>
						</dt>
						<dd>
							<?php
							$datetime = strtotime($attribute['Attribute']['created']);
							$mysqldate = date("m/d/y g:i A", $datetime);
							echo $mysqldate;
							?>
						</dd>
						<?php
						echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'attribute_category_id'), __('Category', true), array('value' => $attribute['AttributeCategory']['path_name'], 'compare' => true));
						echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'name'), __('Name', true), array('compare' => true));
						echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'description'), __('Description', true), array('compare' => true));
						echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'manufacture_id'), __('Manufacturer', true), array('value' => $attribute['Manufacture']['title'],'compare' => true));
						echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'artist_id'), __('Artist', true), array('value' => $attribute['Artist']['name'], 'compare' => true));
						echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'scale_id'), __('Scale', true), array('value' => $attribute['Scale']['scale'], 'compare' => true));
						?>
					</dl>					
				</div>
				<div class="attribute detail">
					<div class="detail title">
						<h3><?php echo __('Collectibles linked to this item', true); ?></h3>
					</div>
					<?php
					echo '<div class="standard-list empty"><table class="table">';
					echo '<thead><tr>';
					echo '<th></th>';
					echo '<th class="name">';
					echo __('Collectible Name');
					echo '</th>';
					echo '<th class="count">';
					echo __('Count');
					echo '</th>';
					echo '</tr></thead>';
					if (isset($attributesCollectible) && !empty($attributesCollectible)) {
						foreach ($attributesCollectible as $key => $value) {
							echo '<tr>';
							if($value['Collectible']['status_id'] === '1'){
								echo '<td><i class="icon-plus"></i></td>';
							} else {
								echo '<td> </td>';
							}
							echo '<td class="name">';
							echo $value['Collectible']['name'];
							echo '</td>';
							echo '<td>';
							echo $value['AttributesCollectible']['count'];
							echo '</td>';
							echo '</tr>';
						}
					} else {

						echo '<tr><td colspan="2">No Collectibles are linked to this item.</td></tr>';

					}
					echo '</table></div>';
					?>				
				</div>	
			</div>
			<?php echo $this -> Form -> create('Approval', array('url' => '/admin/edits/approval_2/' . $editId, 'id' => 'approval-form')); ?>
			<input id="approve-input" type="hidden" name="data[Approval][approve]" value="" />
			<fieldset class="approval-fields">
				<ul class="form-fields unstyled">
					<li>
						<div class="label-wrapper">
							<label for=""> <?php echo __('Notes')
								?></label>
						</div>
						<textarea rows="6" cols="30" name="data[Approval][notes]"></textarea>
					</li>
				</ul>
			</fieldset>
			</form>
			<div class="links">
				<button id="approval-button" class="btn btn-primary"><?php echo __('Approve');?></button>
				<button id="deny-button" class="btn"><?php echo __('Deny');?></button>
			</div>
			<script>
				//Eh move this out of here
				$('#approval-button').click(function() {
					$('#approve-input').val('true');
					$('#approval-form').submit();
				});
				$('#deny-button').click(function() {
					$('#approve-input').val('false');
					$('#approval-form').submit();
				});

			</script>		
		</div>

	</div>
</div>