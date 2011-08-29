<div id="admin-edit" class="two-column-page">
	<div class="inside">
		<div class="actions">
			<ul>
				<li>
					<?php echo $html -> link('New Collectibles', array('admin' => true, 'controller' => 'collectibles'));?>
				</li>
				<li>
					<?php echo $html -> link('Edits', array('admin' => true, 'controller' => 'edits'));?>
				</li>
			</ul>
		</div>
		<div class="page">
			<div class="title">
				<h2><?php __('Approval');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<?php echo $fileUpload -> image($upload['Upload']['name'], array('width' => '0'));?>
			<?php echo $this -> Form -> create('Approval', array('url'=>'/admin/edits/approval/'.$editId, 'id'=>'approval-form'));?>
				<input type="hidden" name="data[Approval][approve]" value="true" />
			</form>
			<?php echo $this -> Form -> create('Approval', array('url'=>'/admin/upload_edits/approval/'.$editId, 'id'=>'deny-form'));?>
				<input type="hidden" name="data[Approval][approve]" value="false" />
			</form>
			<div class="links">
				<input type="button" id="approval-button" class="button" value="Approve">
				<input type="button" id="deny-button" class="button" value="Deny">
			</div>
		<script>
			//Eh move this out of here
			$('#approval-button').click(function(){
				$('#approval-form').submit();	
			});	
			$('#deny-button').click(function(){
				$('#deny-form').submit();	
			});	
		</script>
		</div>
	</div>
</div>