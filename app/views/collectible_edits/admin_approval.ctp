<div id="admin-edit" class="two-column-page">
	<div class="inside">
	 	<div class="actions">
			<ul>
				<li>
					<h3><?php echo __('Admin');?></h3>
					<ul>
						<li><?php echo $this -> Html -> link('New Collectibles', '/admin/collectibles/index', array('class'=>'link')); ?></li>
						<li><?php echo $this -> Html -> link('Edits','/admin/edits/index', array('class'=>'link')); ?></li>							
					</ul>
				</li>
			</ul>	
		</div>
		<div class="page collectibles-approval">
			<div class="title">
				<h2><?php __('Approval');?></h2>
			</div>
			<?php echo $this -> element('flash');?>
			<?php
			echo $this -> element('collectible_detail_core', array('collectibleCore' => $collectible, 'showAttributes' => false, 'showImage' => false, 'showCompareFields' => true));
			?>
			
			<?php echo $this -> Form -> create('Approval', array('url'=>'/admin/edits/approval/'.$editId, 'id'=>'approval-form'));?>
				<input id="approve-input" type="hidden" name="data[Approval][approve]" value="" />
				<fieldset>
					<ul class="form-fields">
						<li>
							<div class="label-wrapper">
								<label for=""> <?php __('Notes')
									?></label>
							</div>
							<textarea rows="6" cols="30" name="data[Approval][notes]"></textarea>
						</li>	
					</ul>
				</fieldset>			
			</form>
			<div class="links">
				<input type="button" id="approval-button" class="button" value="Approve">
				<input type="button" id="deny-button" class="button" value="Deny">
			</div>
		<script>
			//Eh move this out of here
			$('#approval-button').click(function(){
				$('#approve-input').val('true');
				$('#approval-form').submit();	
			});	
			$('#deny-button').click(function(){
				$('#approve-input').val('false');
				$('#approval-form').submit();	
			});	
		</script>
		</div>
	</div>
</div>
