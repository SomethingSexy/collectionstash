<?php echo $this -> Minify -> script('jquery.form', array('inline' => false)); ?>
<?php
echo $this -> Minify -> script('jquery.treeview', array('inline' => false));
echo $this -> Minify -> script('cs.core.tree', array('inline' => false));
echo $this -> Minify -> script('cs.attribute', array('inline' => false));
?>
<div id="admin-edit" class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions'); ?>
		<div class="page">
			<div class="title">
				<h2> <?php echo __('Edit Details'); ?> </h2>
			</div>
			<?php echo $this -> element('flash'); ?>
			<div class="detail-wrapper">
				<div class="attribute detail">
					<div class="detail title">
						<h3><?php echo __('Type of Edit', true); ?></h3>
					</div>
					<div class="directional-text">
						<?php echo __('If the collectible item is being deleted, that means the link between that item and the collectible will be removed.  If this is the only collectible that is linked to this item, then the item will also be removed.'); ?>
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
							// If it is a delete
							if ($attribute['Action']['action_type_id'] === '4') {

							}
 							?>						
						
					</dl>
					<div class="detail title">
						<h3><?php echo __('Collectible Item Details', true); ?></h3>
					</div>
					<dl>
						<dt>
							<?php echo __('Date Added'); ?>
						</dt>
						<dd>
							<?php
							$datetime = strtotime($attribute['AttributesCollectible']['created']);
							$mysqldate = date("m/d/y g:i A", $datetime);
							echo $mysqldate;
							?>
						</dd>
						<?php
						echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'AttributesCollectible', 'Field' => 'count'), __('Count', true), array('compare' => true));
						?>
						
					</dl>	
					<?php
					$attributeJSON = '{';
					$attributeJSON .= '"categoryId" : "' . $attribute['Attribute']['AttributeCategory']['id'] . '",';
					$attributeJSON .= '"categoryName" : "' . $attribute['Attribute']['AttributeCategory']['path_name'] . '",';
					$attributeJSON .= '"name" : "' . $attribute['Attribute']['name'] . '",';
					$attributeJSON .= '"description" : "' . $attribute['Attribute']['description'] . '",';
					$attributeJSON .= '"scaleId" : ';
					if (isset($attribute['Attribute']['scale_id']) && !is_null($attribute['Attribute']['scale_id'])) {
						$attributeJSON .= '"' . $attribute['Attribute']['scale_id'] . '",';
					} else {
						$attributeJSON .= '"null" ,';
					}
					$attributeJSON .= '"manufacturerId" : "' . $attribute['Attribute']['manufacture_id'] . '",';
					$attributeJSON .= '"id" : "' . $attribute['Attribute']['id'] . '"';
					$attributeJSON .= '}';
					?>
					
					
					
					<div id="attribute" class="attribute-data" data-attribute='<?php echo $attributeJSON; ?>'  data-id="<?php echo $attribute['Attribute']['id']; ?>">					
						<div class="detail title">
							<h3><?php echo __('Item Details', true); ?></h3>
							<?php
								// There can be a couple things, if this is a new attribute being added to a collectible, the attribute can be new or it can be existing

								$attributeStatusId = $attribute['Attribute']['status_id'];

								// 4 is the only status that should be active
								// Otherwis we will be approving the attribute and the collectible
								if ($attributeStatusId !== '4') {
								?>
								<div class="actions icon">
									<ul>
										<li>
											<a class="edit" href="#"><i class="icon-pencil icon-large"></i></a>
										</li>
									</ul>
								</div>
							<?php } ?>
						</div>
						<div class="directional-text">
							<?php echo __('This is the item that is being added to the above collectible.  If this item was added at the same time it was linked to the item it will be automatically approved if this edit is approved.  If it is a new item it can be edit but using the link above.  If this item is new and the edit is being denied, the item will automatically be deleted.'); ?>
						</div>
						<dl>
							<dt>
								<?php echo __('Status'); ?>
							</dt>
							<dd>
								<?php echo $attribute['Attribute']['Status']['status']; ?>
							</dd>
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
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'attribute_category_id'), __('Category', true), array('class' => 'attribute_category_id', 'value' => $attribute['Attribute']['AttributeCategory']['path_name'], 'compare' => false));
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'name'), __('Name', true), array('class' => 'name', 'compare' => false));
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'description'), __('Description', true), array('class' => 'description', 'compare' => false));
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'manufacture_id'), __('Manufacturer', true), array('class' => 'manufacture_id', 'compare' => false, 'value' => $attribute['Attribute']['Manufacture']['title']));
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'scale_id'), __('Scale', true), array('class' => 'scale_id', 'compare' => false, 'value' => $attribute['Attribute']['Scale']['scale']));
							?>
						</dl>
					</div>				
				</div>
				<div class="attribute detail">
					<div class="detail title">
						<h3><?php echo __('Collectibles currently linked to this item', true); ?></h3>
					</div>
					<?php

					if (isset($attributesCollectible) && !empty($attributesCollectible)) {
						echo '<table class="table">';
						echo '<thead>';
						echo '<tr>';
						echo '<th>';
						echo __('Collectible Name');
						echo '</th>';
						echo '<th>';
						echo __('Count');
						echo '</th>';
						echo '<th>';
						echo __('State');
						echo '</th>';
						echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach ($attributesCollectible as $key => $value) {
							echo '<tr>';
							echo '<td>';
							echo $value['Collectible']['name'];
							echo '</td>';
							echo '<td>';
							echo $value['AttributesCollectible']['count'];
							echo '</td>';
							echo '</tr>';
						}
						echo '</tbody>';
						echo '</table>';
					} else {
						echo '<div class="standard-list empty"><ul class="unstyled">';
						echo '<li>No Collectibles are linked to this item.</li>';
						echo '<ul><div>';
					}
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
				<button id="approval-button" class="btn btn-primary"><?php echo __('Approve'); ?></button>
				<button id="deny-button" class="btn"><?php echo __('Deny'); ?></button>
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

<script>
	$(function() {
		var updateAttributes = new UpdateAttributes({
			$element : $('#attribute'),
			$openElement : $('#attribute').find('a.edit'),
			$dataElement : $('#attribute'),
			adminPage : true
		});

		updateAttributes.init();
	});
</script>
<?php echo $this -> element('attribute_update_dialog'); ?>