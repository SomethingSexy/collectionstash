<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(); ?>	
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			    <h2><?php  __('Review Collectible');?></h2>
			</h2>
		</div>
	    <?php echo $this->element('flash'); ?>
	    <div class="component-info">
	      	<div><?php __('Please review the collectible below.');?></div> 
	    </div>
		<div class="component-view review">
			<?php echo $this->element('collectible_detail_core');	?>		
			<div class="links review">
				<?php echo $this->Form->create('Collectible' , array('url' => '/'.$this->params['controller']. '/'.$this->action.'/review', ));?>
					<input type="hidden" name="data[balls]" value="Submit">
					<input type="submit" value="Submit">
				</form>
			</div>
		</div>
	</div>
</div>







