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
					
					</div>
					<dl>
						<dt>
							<?php echo __('Submitted By'); ?>
						</dt>
						<dd>
							<?php

							if (!empty($collectibleUpload['User']['username'])) {
								echo $collectibleUpload['User']['username'];
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
							if ($collectibleUpload['Action']['action_type_id'] === '1') {
								echo 'Add';
							} else if ($collectibleUpload['Action']['action_type_id'] === '2') {
								echo 'Edit';
							} else if ($collectibleUpload['Action']['action_type_id'] === '4') {
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
							if (empty($collectibleUpload['Action']['reason'])) {
								echo 'N/A';
							} else {
								echo $collectibleUpload['Action']['reason'];
							}
 							?>
						</dd>
							<?php
							// If it is a delete then see if there is a link
							if ($collectibleUpload['Action']['action_type_id'] === '4') {

							}
 							?>						
						
					</dl>
					<div class="detail title">
						<h3><?php echo __('Upload Details', true); ?></h3>
					</div>
					<dl>
						<dt>
							<?php echo __('Date Added'); ?>
						</dt>
						<dd>
							<?php
							$datetime = strtotime($collectibleUpload['CollectiblesUpload']['created']);
							$mysqldate = date("m/d/y g:i A", $datetime);
							echo $mysqldate;
							?>
						</dd>
						<?php echo $this -> FileUpload -> image($collectibleUpload['Upload']['name'], array('width' => '0')); ?>
					</dl>	
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