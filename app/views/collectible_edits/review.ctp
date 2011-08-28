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
			<?php echo $this->element('collectible_detail_core', array(
				'collectibleCore' => $collectibleReview,
				'showImage' => false,
				'showAttributes' => false
			));	?>		
			<div class="links review">
				<?php echo $this->Form->create('Collectible', array('url'=>'/collectible_edits/confirm'));?>
					<input type="submit" value="Submit">
				</form>
			</div>
		</div>
	</div>
</div>







