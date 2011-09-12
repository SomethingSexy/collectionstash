<?php if($this -> Session -> check('add.collectible.mode.variant')) { ?>
<?php      
	echo $this->element('collectible_detail', array(
		'title' => __('Base Collectible Details', true),
		'showStatistics' => false,
		'showWho' => false,
		'collectibleDetail' => $collectible
	));
?>
<?php } ?>
<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(array('manufacture'=>'Manufacturer Details', 'variantFeatures'=>'Variant Features', 'attributes'=>'Accessories/Features', 'tags'=>'Tags','image'=>'Image', 'review'=> 'Review')); ?>	
</div>
<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2>
			    <h2><?php  __('Review Collectible Submission');?></h2>
			</h2>
		</div>
	    <?php echo $this->element('flash'); ?>
	    <div class="component-info">
	      	<div><?php __('Please review the collectible you are submitting to Collection Stash.');?></div> 
	    </div>
		<div class="component-view review">

			<?php echo $this->element('collectible_detail_core', array(
				'collectibleCore' => $collectibleReview,
				'showTags' => true
			));	?>	
		
			<div class="links review">
				<?php echo $this->Form->create('Collectible' , array('url' => '/'.$this->params['controller']. '/'.$this->action.'/review', ));?>
					<input type="hidden" name="data[balls]" value="Submit">
					<input type="submit" value="Submit">
				</form>
			</div>
		</div>
	</div>
</div>







