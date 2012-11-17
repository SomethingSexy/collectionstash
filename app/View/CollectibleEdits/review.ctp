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
<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2>
			    <h2><?php  echo __('Review Collectible Update');?></h2>
			</h2>
		</div>
	    <?php echo $this->element('flash'); ?>
	    <div class="component-info">
	      	<div><?php echo __('Please review the collectible you are updating below.');?></div> 
	    </div>
		<div class="component-view review">
			<?php echo $this->element('collectible_detail_core', array(
				'collectibleCore' => $collectibleReview,
				'showImage' => false,
				'showAttributes' => false
			));	?>		
			<div class="links review">
				<?php echo $this->Form->create('Collectible', array('url'=>'/collectible_edits/confirm'));?>
					<input type="submit" value="Submit" class="btn btn-primary">
				</form>
			</div>
		</div>
	</div>
</div>







