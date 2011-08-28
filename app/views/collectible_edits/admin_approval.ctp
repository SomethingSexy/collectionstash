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
			<?php
			echo $this -> element('collectible_detail_core', array('collectibleCore' => $collectible, 'showAttributes' => false, 'showImage' => false, 'showCompareFields' => true));
			?>
			<?php echo $this -> Form -> create('Approval', array('url'=>'/collectible_edits/approval/'.$editApprovalId, 'id'=>'approve-form'));?>
				<input type="hidden" name="data[Approval][approve]" value="true" />
			</form>
			<?php echo $this -> Form -> create('Approval', array('url'=>'/collectible_edits/approval/'.$editApprovalId, 'id'=>'deny-form'));?>
				<input type="hidden" name="data[Approval][approve]" value="false" />
			</form>
			<div class="links">
				<input type="button" id="add-image-button" class="button" value="Approve">
				<input type="button" id="skip-image-button" class="button" value="Deny">
			</div>
		<script>
			//Eh move this out of here
			$('#add-image-button').click(function(){
				$('#add-image-form').submit();	
			});	
			$('#skip-image-button').click(function(){
				$('#skip-image-form').submit();	
			});	
		</script>
		</div>
	</div>
</div>
