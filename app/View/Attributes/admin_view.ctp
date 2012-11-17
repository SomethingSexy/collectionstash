<div id="admin-edit" class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions'); ?>
		<div class="page">
			<div class="title">
				<h2> <?php echo __('Edit Details'); ?> </h2>
			</div>
			<?php echo $this -> element('flash'); ?>
			<div class="detail-wrapper">
				<div class="attribute detail attribute-data">
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
						echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Manufacture', 'Field' => 'title'), __('Manufacturer', true), array('compare' => true));
						echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Scale', 'Field' => 'scale'), __('Scale', true), array('compare' => true));
						?>
					</dl>					
				</div>
			</div>
			<?php echo $this -> Form -> create('Approval', array('url' => '/admin/attributes/approve/' . $attribute['Attribute']['id'], 'id' => 'approval-form')); ?>
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